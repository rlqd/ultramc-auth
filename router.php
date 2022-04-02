<?php

//chdir(__DIR__ . DIRECTORY_SEPARATOR . 'www');
$path = '/'.ltrim(parse_url($_SERVER['REQUEST_URI'])['path'],'/');

if (strpos($path, '/api/') !== 0) {
    http_response_code(404);
    exit('Not found');
}

$file = 'www' . substr($path, 4);
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
