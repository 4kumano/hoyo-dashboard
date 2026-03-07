<?php

// Forward Vercel serverless requests to Laravel

// Set the public path for static file serving
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
$_ENV['APP_RUNNING_IN_VERCEL'] = true;

require __DIR__ . '/../public/index.php';
