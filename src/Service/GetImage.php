<?php

namespace App\Service;

use Safe;
use Vich\UploaderBundle\Entity\File;

class GetImage
{
    public function get(?string $url): ?File
    {
        if (null === $url) {
            return null;
        }

        if (false === $size = $this->getImageSize($url)) {
            return null;
        }

        $extension = $this->getImageType($url);

        $name = \str_replace('.', '', \uniqid('', true));
        $name = \sprintf('%s.%s', $name, $extension);

        Safe\file_put_contents(sprintf('images/products/%s', $name), Safe\fopen($url, 'r'));

        $file = new File();
        $file->setName($name);
        $file->setOriginalName($name);
        $file->setMimeType(sprintf('image/%s', $extension));
        $file->setSize($size);
        $file->setDimensions($this->getImageDimensions($url));

        return $file;
    }

    /** @return bool|int */
    private function getImageSize(string $url)
    {
        $headers = get_headers($url, 1);

        if ($headers[0] !== 'HTTP/1.1 200 OK') {
            return false;
        }

        return $headers['Content-Length'];
    }

    private function getImageType(string $url): string
    {
        $value = exif_imagetype($url);

        if (false === $value) {
            return 'jpg';
        }

        if (IMAGETYPE_GIF == $value) {
            return 'gif';
        }

        if (IMAGETYPE_JPEG == $value) {
            return 'jpg';
        }

        if (IMAGETYPE_PNG == $value) {
            return 'png';
        }

        return 'jpg';
    }

    private function getImageDimensions(string $url)
    {
        $size = getimagesize($url);

        return [$size[0], $size[1]];
    }
}