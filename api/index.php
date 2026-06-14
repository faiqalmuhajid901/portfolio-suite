<?php

$paths = [
    '/tmp/views',
    '/tmp/framework',
    '/tmp/framework/cache',
    '/tmp/framework/sessions',
    '/tmp/framework/views',
];

foreach ($paths as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

require __DIR__ . '/../public/index.php';