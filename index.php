<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// If the requested URI exists in the public folder, serve it directly
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    // Return false to let the PHP built-in server serve the file, 
    // but in Apache this won't work, we need a different approach for Apache.
    // Actually, Apache shouldn't hit this index.php for existing public files if .htaccess is configured correctly.
}

require_once __DIR__.'/public/index.php';
