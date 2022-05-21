<?php

require __DIR__ . '/vendor/autoload.php';

$sourceFolder = rtrim("/Users/ajay/Desktop", "/");

$targetFolderPath = rtrim("/Users/ajay/Desktop/Screenshots", "/");  

$folderItems = scandir($sourceFolder); 

/**
 * 1. Filter only the items that match "screenshot"
 * 2. Remove any folders that match step 1
 */
$screenshots = collect($folderItems)->filter(function ($item) {
    return str_contains(strtolower($item), 'screenshot'); 
})->filter(function ($screenshot) use ($sourceFolder) {
    return !is_dir($sourceFolder . '/' . $screenshot);
});

var_dump($screenshots);

if (!count($screenshots)) {
    echo "No screenshots present. Stopping execution"; 
    die(); 
}

// var_dump(is_dir($targetFolderPath)); 

if (!is_dir($targetFolderPath)) {
    echo "Target Folder doesn't exist. Creating the folder \n";

    mkdir($targetFolderPath); 
    
    var_dump(is_dir($targetFolderPath) ? "Created the folder \n" : "failed to create the folder.");

    if (!is_dir($targetFolderPath)) {
        echo "Failed to create the folder"; 
        die(); 
    }
}

/**
 * 2. move the screenshots from the current folder to the new one
 */
$screenshots->each(function ($screenshot) use ($sourceFolder, $targetFolderPath) {
    
    $screenshotPath = $sourceFolder . '/' . $screenshot; 

    $targetScreenshotPath = $targetFolderPath . '/' . $screenshot; 

    var_dump("Moving $screenshotPath to $targetScreenshotPath"); 

    rename($screenshotPath, $targetScreenshotPath); 
}); 


echo "hello world"; 
echo "\n"; 