<?php

require __DIR__ . '/vendor/autoload.php';

use App\MoveFilesToTarget;

$config = require 'config.php'; 

if (!count($config)) {
    exit("No config values provided. Add a config value to continue"); 
}

foreach ($config as $name => $values) 
{
    if (!isset($values['sourceDir'])) 
    {
        exit("The source dir for $name is blank. Add a value"); 
    }

    if (!isset($values['targetDir'])) {
        exit("The target dir for $name is blank. Add a value");
    }

    $targetDirFolderName = collect(
        explode('/', rtrim($values['targetDir'], '/'))
    )->reverse()
    ->first(); 

    if ($targetDirFolderName == $values['needle']) {
        exit("The target dir's folder name for $name needs to be different from the corresponding needle value " . $values['needle']); 
    }
}

foreach ($config as $name => $values) 
{
    MoveFilesToTarget::handle(
        $values['sourceDir'],
        $values['targetDir'],
        $values['fileTypes'], 
        $values['needle']
    );
}
