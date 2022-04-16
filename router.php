<?php

/** 
 * Web root aware router for dev server.
 * By default actions are available at /api/ (eg http://localhost:8000/api/profile.php).
 *
 * Usage:
 * php -S localhost:8000 router.php
*/

//Extract web root
$envFile = __DIR__ . '/.env';
if (!is_file($envFile)) {
    throw new Exception('.env file not found at ' . $envFile);
}
$envContent = file_get_contents($envFile);
if (!preg_match('/\n\s*WEB_ROOT\s*=\s*(\w+)\s*\n/', $envContent, $matches)) {
    throw new Exception('WEB_ROOT not specified in .env, no need to use router');
}
$webRoot = $matches[1];

//Do routing
$path = '/'.ltrim(parse_url($_SERVER['REQUEST_URI'])['path'],'/');

if (strpos($path, "/$webRoot/") !== 0) {
    http_response_code(404);
    exit('Not found');
}

$file = 'www' . substr($path, strlen($webRoot) + 1);
if (!file_exists($file)) {
    http_response_code(404);
    exit('Not found ' . $file);
}

if (substr($file, -4) === '.php') {
    include($file);
} else {
    header("Content-Type: " . mime_content_type($file));
    return readfile($file);
}
