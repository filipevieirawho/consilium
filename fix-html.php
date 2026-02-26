<?php
$content = file_get_contents('/Users/filipevieirawho/Herd/consilium/resources/views/contacts/show.blade.php');
if (strpos($content, '<button type="submit"') !== false && strpos($content, 'x-transition:enter-end="opacity-100 transform translate-y-0"') !== false) {
    echo "Found the area.\n";
} else {
    echo "Area not right.\n";
}
