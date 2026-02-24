<?php

namespace App\Services;

class TursoSync
{
    /**
     * Execute a SQL statement on Turso (INSERT/UPDATE/DELETE).
     */
    public static function execute(string $sql, array $args = []): bool
    {
        $result = self::request($sql, $args);
        return $result !== false;
    }

    /**
     * Execute a SELECT query and return rows.
     */
    public static function query(string $sql, array $args = []): array
    {
        $result = self::request($sql, $args);
        if ($result === false || !isset($result['results'])) {
            return [];
        }

        // Parse Turso pipeline response
        foreach ($result['results'] as $res) {
            if (isset($res['response']['type']) && $res['response']['type'] === 'execute') {
                $executeResult = $res['response']['result'];
                $cols = array_column($executeResult['cols'] ?? [], 'name');
                $rows = [];
                foreach ($executeResult['rows'] ?? [] as $row) {
                    $assoc = [];
                    foreach ($row as $i => $cell) {
                        $assoc[$cols[$i]] = $cell['value'] ?? null;
                    }
                    $rows[] = $assoc;
                }
                return $rows;
            }
        }

        return [];
    }

    /**
     * On cold start: fetch all users and contacts from Turso → insert into local SQLite.
     */
    public static function syncToLocal(): void
    {
        $url = getenv('TURSO_DATABASE_URL');
        $token = getenv('TURSO_AUTH_TOKEN');

        if (empty($url) || empty($token)) {
            return;
        }

        try {
            // Sync users
            $users = self::query('SELECT id, name, email, password, email_verified_at, remember_token, role, active, created_at, updated_at FROM users');
            if (!empty($users)) {
                $pdo = new \PDO('sqlite:' . (getenv('DB_DATABASE') ?: '/tmp/database.sqlite'));
                $stmt = $pdo->prepare('INSERT OR REPLACE INTO users (id, name, email, password, email_verified_at, remember_token, role, active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                foreach ($users as $user) {
                    $stmt->execute([
                        $user['id'],
                        $user['name'],
                        $user['email'],
                        $user['password'],
                        $user['email_verified_at'],
                        $user['remember_token'],
                        $user['role'] ?? 'representante',
                        $user['active'] ?? 1,
                        $user['created_at'],
                        $user['updated_at'],
                    ]);
                }
            }

            // Sync contacts
            $contacts = self::query('SELECT id, name, email, phone, message, opt_in, status, user_id, notes, created_at, updated_at FROM contacts');
            if (!empty($contacts)) {
                $pdo = $pdo ?? new \PDO('sqlite:' . (getenv('DB_DATABASE') ?: '/tmp/database.sqlite'));
                $stmt = $pdo->prepare('INSERT OR REPLACE INTO contacts (id, name, email, phone, message, opt_in, status, user_id, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                foreach ($contacts as $contact) {
                    $stmt->execute([
                        $contact['id'],
                        $contact['name'],
                        $contact['email'],
                        $contact['phone'],
                        $contact['message'],
                        $contact['opt_in'],
                        $contact['status'] ?? 'novo',
                        $contact['user_id'],
                        $contact['notes'],
                        $contact['created_at'],
                        $contact['updated_at'],
                    ]);
                }
            }
        } catch (\Exception $e) {
            error_log('TursoSync::syncToLocal failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync a contact to Turso (upsert).
     */
    public static function upsertContact($contact): void
    {
        try {
            // Check if exists
            $existing = self::query('SELECT id FROM contacts WHERE id = ?', [$contact->id]);

            if (!empty($existing)) {
                self::execute(
                    'UPDATE contacts SET name = ?, email = ?, phone = ?, message = ?, opt_in = ?, status = ?, user_id = ?, notes = ?, updated_at = ? WHERE id = ?',
                    [$contact->name, $contact->email, $contact->phone, $contact->message, $contact->opt_in ? 1 : 0, $contact->status ?? 'novo', $contact->user_id, $contact->notes, $contact->updated_at?->toDateTimeString(), $contact->id]
                );
            } else {
                self::execute(
                    'INSERT INTO contacts (name, email, phone, message, opt_in, status, user_id, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$contact->name, $contact->email, $contact->phone, $contact->message, $contact->opt_in ? 1 : 0, $contact->status ?? 'novo', $contact->user_id, $contact->notes, $contact->created_at?->toDateTimeString(), $contact->updated_at?->toDateTimeString()]
                );
            }
        } catch (\Exception $e) {
            error_log('TursoSync::upsertContact failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync a user to Turso (upsert).
     */
    public static function upsertUser($user): void
    {
        try {
            $existing = self::query('SELECT id FROM users WHERE id = ?', [$user->id]);

            if (!empty($existing)) {
                self::execute(
                    'UPDATE users SET name = ?, email = ?, password = ?, role = ?, active = ?, email_verified_at = ?, updated_at = ? WHERE id = ?',
                    [$user->name, $user->email, $user->password, $user->role ?? 'representante', $user->active ? 1 : 0, $user->email_verified_at?->toDateTimeString(), $user->updated_at?->toDateTimeString(), $user->id]
                );
            } else {
                self::execute(
                    'INSERT INTO users (name, email, password, role, active, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [$user->name, $user->email, $user->password, $user->role ?? 'representante', $user->active ? 1 : 0, $user->email_verified_at?->toDateTimeString(), $user->created_at?->toDateTimeString(), $user->updated_at?->toDateTimeString()]
                );
            }
        } catch (\Exception $e) {
            error_log('TursoSync::upsertUser failed: ' . $e->getMessage());
        }
    }

    /**
     * Make HTTP request to Turso pipeline API.
     */
    private static function request(string $sql, array $args = []): array|false
    {
        $url = getenv('TURSO_DATABASE_URL');
        $token = getenv('TURSO_AUTH_TOKEN');

        if (empty($url) || empty($token)) {
            return false;
        }

        // Convert libsql:// to https:// for HTTP API
        $httpUrl = str_replace('libsql://', 'https://', $url);
        $pipelineUrl = rtrim($httpUrl, '/') . '/v2/pipeline';

        // Build args for Turso API
        $tursoArgs = [];
        foreach ($args as $arg) {
            if (is_null($arg)) {
                $tursoArgs[] = ['type' => 'null'];
            } elseif (is_int($arg)) {
                $tursoArgs[] = ['type' => 'integer', 'value' => (string) $arg];
            } elseif (is_float($arg)) {
                $tursoArgs[] = ['type' => 'float', 'value' => (string) $arg];
            } else {
                $tursoArgs[] = ['type' => 'text', 'value' => (string) $arg];
            }
        }

        $stmt = ['sql' => $sql];
        if (!empty($tursoArgs)) {
            $stmt['args'] = $tursoArgs;
        }

        $payload = json_encode([
            'requests' => [
                ['type' => 'execute', 'stmt' => $stmt],
                ['type' => 'close'],
            ],
        ]);

        $ch = curl_init($pipelineUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && $response) {
            return json_decode($response, true) ?: [];
        }

        error_log("TursoSync HTTP error {$httpCode}: {$response}");
        return false;
    }
}
