<?php
echo "<h2>Image Path Test</h2>";

// Test 1: Check if file exists
$paths = [
    'assets/images/logo.png',
    'assets/images/logo.jpg',
    'assets/images/logo.PNG',
    'assets/images/Logo.png',
    'images/logo.png',
    'logo.png'
];

foreach($paths as $path) {
    if(file_exists($path)) {
        echo "<p style='color:green'>✓ FOUND: $path</p>";
        echo "<img src='$path' width='100'><br>";
    } else {
        echo "<p style='color:red'>✗ NOT FOUND: $path</p>";
    }
}

// Test 2: List all files in assets/images/
echo "<h3>Files in assets/images/ folder:</h3>";
if(is_dir('assets/images/')) {
    $files = scandir('assets/images/');
    foreach($files as $file) {
        if($file != '.' && $file != '..') {
            echo "- $file<br>";
        }
    }
} else {
    echo "<p style='color:red'>Folder 'assets/images/' does not exist!</p>";
}

// Test 3: Show current directory
echo "<h3>Current directory:</h3>";
echo "<pre>" . __DIR__ . "</pre>";
?>