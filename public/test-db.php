<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    // Test database connection
    $connection = \DB::connection();
    echo "Database connection: SUCCESS\n";
    
    // Test if users table exists
    $tables = \DB::select("SHOW TABLES");
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        foreach ($table as $value) {
            echo "- $value\n";
        }
    }
    
    // Test if we can create a user
    echo "\nTesting user creation...\n";
    $userData = [
        'name' => 'Test User ' . time(),
        'email' => 'test' . time() . '@example.com',
        'password' => \Hash::make('password123'),
    ];
    
    $user = \App\Models\User::create($userData);
    echo "User created successfully: ID " . $user->id . "\n";
    
    // Clean up
    $user->delete();
    echo "Test user deleted\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nEnvironment check:\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
echo "DB_HOST: " . env('DB_HOST') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME') . "\n";
