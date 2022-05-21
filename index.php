<?php

require __DIR__ . '/vendor/autoload.php';

use App\MoveFilesToTarget;

MoveFilesToTarget::handle(
    "/Users/ajay/Desktop", 
    "/Users/ajay/Desktop/Screenshots",
    "screenshot"
); 