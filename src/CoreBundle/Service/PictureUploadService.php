<?php

namespace App\CoreBundle\Service;

use App\CoreBundle\Constants\FileConstants;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploadService
{
    private string $uploadsDirectory;
    private string $thumbnailsDirectory;

    public function __construct(string $uploadsDirectory, string $thumbnailsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->thumbnailsDirectory = $thumbnailsDirectory;
    }

    public function uploadFile(?UploadedFile $file): ?string
    {
        if ($file instanceof UploadedFile) {
            try {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->uploadsDirectory, $newFilename);

                $this->createThumbnail($newFilename);

                return $newFilename;
            } catch (FileException $e) {
                return null;
            }
        }

        return null;
    }

    private function createThumbnail(string $filename): void
    {
        $sourcePath = $this->uploadsDirectory . '/' . $filename;
        $thumbnailPath = $this->thumbnailsDirectory . '/' . $filename;

        $thumbnailWidth = FileConstants::NEWS_THUMBNAIL_WIDTH;
        $thumbnailHeight = FileConstants::NEWS_THUMBNAIL_HEIGHT;

        list($width, $height, $type) = getimagesize($sourcePath);
        $aspectRatio = $width / $height;

        if ($thumbnailWidth / $thumbnailHeight > $aspectRatio) {
            $thumbnailWidth = $thumbnailHeight * $aspectRatio;
        } else {
            $thumbnailHeight = $thumbnailWidth / $aspectRatio;
        }

        $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            default:
                return;
        }

        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail, $thumbnailPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, $thumbnailPath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnail, $thumbnailPath);
                break;
        }

        imagedestroy($source);
        imagedestroy($thumbnail);
    }

    public function deleteFile(string $filename): void
    {
        $filePath = $this->uploadsDirectory . '/' . $filename;
        $thumbnailPath = $this->thumbnailsDirectory . '/' . $filename;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }
}
