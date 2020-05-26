<?php

namespace App\Service;

class FilenameCreator
{
    /**
     * Create a unique and sure filemame
     */
    public function createUniqueFilename($originalFilename)
    {
        $filename = pathinfo($originalFilename, PATHINFO_FILENAME);
        $sanitized = preg_replace('#[^a-zA-Z0-9\-\._]#','', $filename);//remove all characters not present in the regex
        $filenameExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        
        return $sanitized . '-' . uniqid() . '.' . $filenameExtension;
    }

   
}