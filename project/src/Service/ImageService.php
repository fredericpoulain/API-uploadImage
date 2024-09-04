<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageService
{

    const MAX_FILE_SIZE = 10 * 1024 * 1024;


    /**
     * @param UploadedFile $image
     * @return bool
     */
    public function checkTypeMime(UploadedFile $image): bool
    {

        $typeMimeArray = [
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "webp" => "image/webp",
            "png" => "image/png"
        ];
        $typeMime = $image->getMimeType();

        if (!in_array($typeMime, $typeMimeArray, true)) {
            return false;
        }
        return true;
    }

    /**
     * @param UploadedFile $image
     * @return bool
     */
    public function checkExtension(UploadedFile $image): bool
    {

        $extensionsArray = ["jpg", "jpeg", "webp", "png"];

        if (!in_array($this->getExtension($image), $extensionsArray, true)) {
            return false;
        }
        return true;
    }

    /**
     * @param UploadedFile $image
     * @return bool
     */
    public function checkSize(UploadedFile $image): bool
    {
        return $image->getSize() <= self::MAX_FILE_SIZE;
    }

    /**
     * @param UploadedFile $image
     * @return string
     */
    public function generateImageName(UploadedFile $image): string
    {
        $extension = explode("/", $image->getMimeType())[1];
        return uniqid(mt_rand(), true) . '.' . $extension;
    }

    /**
     * @param UploadedFile $image
     * @return string
     */
    private function getExtension(UploadedFile $image): string
    {
        return explode("/", $image->getMimeType())[1];
    }
}