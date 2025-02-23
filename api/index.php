<?php  
require __DIR__ . "/../public/index.php";
$path = env('STORAGE_PATH', storage_path('app')); // Automatically uses /tmp on Vercel
Storage::disk('custom')->put($path . '/example.txt', 'Hello Vercel!');