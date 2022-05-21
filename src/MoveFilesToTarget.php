<?php 

namespace App; 

class MoveFilesToTarget 
{
    protected $sourceDir; 

    protected $targetDir; 

    protected $dirItems; 

    protected $needle; 

    public function __construct ($sourceDir, $targetDir, $needle) 
    {
       $this->sourceDir = rtrim($sourceDir, "/"); 
       $this->targetDir = rtrim($targetDir, "/");
       $this->needle = $needle; 
       $this->dirItems = $this->filterDirItems(
           scandir($this->sourceDir)
        ); 
    }

    public static function handle ($sourceDir, $targetDir, $needle = 'screenshot') 
    {
        return (
            new static($sourceDir, $targetDir, $needle)
        )->execute(); 
    }

    protected function execute ()  
    {
        // 1. kill script if there are no dirItems
        $this->killScriptIfNoDirItems(); 

        // 2. Create target Dir if it doesn't exist
        $this->createTargetDir(); 

        // 3. Move files from sourceDir to targetDir
        $this->moveFromSourceToTarget(); 
    }

    protected function moveFromSourceToTarget () 
    {
        $this->dirItems->each(function ($fileName) {

            $sourcePath = $this->sourceDir . '/' . $fileName;

            $targetPath = $this->targetDir . '/' . $fileName;

            echo "Moving $sourcePath to $targetPath \n";

            rename($sourcePath, $targetPath);
        }); 
    }

    protected function createTargetDir () 
    {
        if (is_dir($this->targetDir)) return null;

        return mkdir($this->targetDir) ?: exit("Failed to create the folder");
    }

    protected function killScriptIfNoDirItems () 
    {
        return count($this->dirItems) ?: exit("No screenshots present. Stopping execution"); 
    }

    /**
     * 1. Filter only the items that match "screenshot"
     * 2. Remove any folders that match step 1
     */
    protected function filterDirItems ($dirItems)  
    {
        return collect($dirItems)->filter(function ($item) {
            return str_contains(
                strtolower($item), 
                $this->needle
            );
        })->filter(function ($fileName) {
            return !is_dir($this->sourceDir . '/' . $fileName);
        });
    }
}

