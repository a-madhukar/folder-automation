<?php 

namespace App; 

class MoveFilesToTarget 
{
    protected $sourceDir; 

    protected $targetDir; 

    protected $dirItems;

    protected $fileTypes; 

    protected $needle; 

    protected $ignoreDirectories; 

    public function __construct (
        $sourceDir, 
        $targetDir, 
        $fileTypes = '', 
        $needle = ''
    ) 
    {
        // var_dump($fileTypes); 
       $this->sourceDir = rtrim($sourceDir, "/"); 
       $this->targetDir = rtrim($targetDir, "/");
       $this->fileTypes = empty($fileTypes) ? '' : collect(explode('|', $fileTypes)); 
       $this->needle = $needle;
       $this->ignoreDirectories = true; 
       $this->dirItems = $this->filterDirItems(
           scandir($this->sourceDir)
        ); 
        
    }

    public static function handle (
        $sourceDir, 
        $targetDir, 
        $fileTypes = '', 
        $needle = ''
    ) 
    {
        return (
            new static($sourceDir, $targetDir, $fileTypes,  $needle)
        )->execute(); 
    }

    protected function execute ()  
    {
        // exit($this->fileTypes); 
        // skip if there are no dir Items
        // $this->killScriptIfNoDirItems(); 
        if (!count($this->dirItems)) 
        {
            
            echo "No items present for " . $this->fileTypes . "\n"; 
            return null; 
        }

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
        $items = collect($dirItems); 

        if (count($this->fileTypes)) 
        {
            $items = $items->filter(function ($item) {
                // var_dump($item); 
                // var_dump($this->fileTypes->contains(pathinfo($item, PATHINFO_EXTENSION))); 
                // var_dump(pathinfo($item, PATHINFO_EXTENSION)); 
                return $this->fileTypes->contains(
                    pathinfo($item, PATHINFO_EXTENSION)
                ); 
            }); 
        } 

        // var_dump(count($items)); 

        if (!empty($this->needle)) 
        {
            $items = $items->filter(function ($item) {
                return str_contains(
                    strtolower($item),
                    strtolower($this->needle)
                );
            }); 
        }

        // var_dump(count($items)); 

        if ($this->ignoreDirectories) 
        {
            $items = $items->filter(function ($fileName) {

                if (
                    $this->fileTypes->contains('app') && pathinfo($fileName, PATHINFO_EXTENSION) == 'app'
                ) {
                    return true; 
                }

                return !is_dir($this->sourceDir . '/' . $fileName);
            }); 
        }

        // var_dump(count($items)); 

        return $items; 
    }
}

