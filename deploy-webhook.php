<?php

/**
 * Post-Deployment Webhook Script
 * 
 * Place this file in your public directory on the cPanel server.
 * Call it after FTP deployment to run artisan commands.
 * 
 * URL: https://yourdomain.com/deploy-webhook.php?token=YOUR_SECRET_TOKEN
 * 
 * IMPORTANT: 
 * 1. Set your secret token below
 * 2. Delete this file after deployment is stable for security
 */

// Set your secret token here (also add to GitHub Secrets as DEPLOY_WEBHOOK_TOKEN)
$secretToken = getenv('DEPLOY_WEBHOOK_TOKEN') ?: 'YOUR_SECRET_TOKEN_HERE';

// Verify token
$providedToken = $_GET['token'] ?? $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';

if ($providedToken !== $secretToken) {
    http_response_code(403);
    die('Unauthorized');
}

// Change to project root directory
$projectPath = dirname(__DIR__); // Assumes this file is in /public
chdir($projectPath);

// Set content type
header('Content-Type: text/plain');

echo "=== Post-Deployment Script ===\n";
echo "Project Path: $projectPath\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Commands to run
$commands = [
    'php artisan down --refresh=30',
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan view:clear',
    'php artisan route:clear',
    'php artisan migrate --force',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
    'php artisan event:cache',
    'composer dump-autoload --optimize --no-dev',
    'php artisan up',
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    echo implode("\n", $output) . "\n";
    
    if ($returnCode !== 0) {
        echo "⚠️  Command returned non-zero exit code: $returnCode\n";
    }
    echo "\n";
}

echo "=== Deployment Complete ===\n";

