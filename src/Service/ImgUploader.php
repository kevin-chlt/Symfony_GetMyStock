<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImgUploader
{
    private  $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    #Create a new filename and move the file in public/image
    public function getFileName (UploadedFile $file): string
    {
            $newFileName = 'produit_'.uniqid().'.'.$file->guessExtension();
            $file->move($this->getTargetDirectory(), $newFileName);
            return $newFileName;
    }
}