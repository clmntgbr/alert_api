<?php

namespace App\Service;

use Safe;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\JsonException;
use Vich\UploaderBundle\Entity\File;

class OpenFoodFactApiService
{
    public function __construct(
        private readonly string $openFoodFactsUrl
    ) {
    }

    /**
     * @throws JsonException
     */
    public function getProduct(?string $ean, string $geography)
    {
        $response = file_get_contents(sprintf($this->openFoodFactsUrl, $geography, $ean));

        return Safe\json_decode($response, true);
    }

    public function getOpenFoodFactProductCategories(array $response): ?string
    {
        if (array_key_exists('categories', $response)) {
            return $response['categories'];
        }

        if (array_key_exists('categories_old', $response)) {
            return $response['categories_old'];
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function getOpenFoodFactProductName(array $response): string
    {
        if (array_key_exists('product_name_fr', $response) && '' !== $response['product_name_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['product_name_fr']))));
        }

        if (array_key_exists('product_name', $response) && '' !== $response['product_name']) {
            return ucwords(strtolower(trim(strip_tags($response['product_name']))));
        }

        if (array_key_exists('product_name_en', $response) && '' !== $response['product_name_en']) {
            return ucwords(strtolower(trim(strip_tags($response['product_name_en']))));
        }

        if (array_key_exists('product_name_en_imported', $response) && '' !== $response['product_name_en_imported']) {
            return ucwords(strtolower(trim(strip_tags($response['product_name_en_imported']))));
        }

        throw new \Exception('Product not found.', 404);
    }

    public function getOpenFoodFactProductOrigin(array $response): ?string
    {
        if (array_key_exists('origin_fr', $response) && '' !== $response['origin_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['origin_fr']))));
        }

        if (array_key_exists('origins', $response) && '' !== $response['origins']) {
            return ucwords(strtolower(trim(strip_tags($response['origins']))));
        }

        return null;
    }

    public function getOpenFoodFactProductNutritionIngredients(array $response): ?string
    {
        if (array_key_exists('ingredients_text_with_allergens_fr', $response) && '' !== $response['ingredients_text_with_allergens_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['ingredients_text_with_allergens_fr']))));
        }

        if (array_key_exists('ingredients_text_with_allergens', $response) && '' !== $response['ingredients_text_with_allergens']) {
            return ucwords(strtolower(trim(strip_tags($response['ingredients_text_with_allergens']))));
        }

        if (array_key_exists('ingredients_text_fr', $response) && '' !== $response['ingredients_text_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['ingredients_text_fr']))));
        }

        if (array_key_exists('ingredients_text', $response) && '' !== $response['ingredients_text']) {
            return ucwords(strtolower(trim(strip_tags($response['ingredients_text']))));
        }

        if (array_key_exists('ingredients_text_en', $response) && '' !== $response['ingredients_text_en']) {
            return ucwords(strtolower(trim(strip_tags($response['ingredients_text_en']))));
        }

        if (array_key_exists('ingredients_text_en_imported', $response) && '' !== $response['ingredients_text_en_imported']) {
            return ucwords(strtolower(trim(strip_tags($response['ingredients_text_en_imported']))));
        }

        return null;
    }

    public function getOpenFoodFactProductLink(array $response): ?string
    {
        if (array_key_exists('link', $response) && '' !== $response['link']) {
            return $response['link'];
        }

        return null;
    }

    public function getOpenFoodFactProductManufacturingPlace(array $response, string $key = 'manufacturing_places'): ?string
    {
        if (array_key_exists($key, $response) && '' !== $response[$key]) {
            return $response[$key];
        }

        return null;
    }

    /**
     * @throws FilesystemException
     */
    public function getOpenFoodFactProductImage(?string $url): ?File
    {
        if (null === $url) {
            return null;
        }

        if (false === $size = $this->getOpenFoodFactProductImageSize($url)) {
            return null;
        }

        $extension = $this->getOpenFoodFactProductImageType($url);

        $name = \str_replace('.', '', \uniqid('', true));
        $name = \sprintf('%s.%s', $name, $extension);

        Safe\file_put_contents(sprintf('images/products/%s', $name), Safe\fopen($url, 'r'));

        $file = new File();
        $file->setName($name);
        $file->setOriginalName($name);
        $file->setMimeType(sprintf('image/%s', $extension));
        $file->setSize($size);
        $file->setDimensions($this->getOpenFoodFactProductImageDimensions($url));

        return $file;
    }

    private function getOpenFoodFactProductImageSize(string $url): bool|int
    {
        $headers = get_headers($url, 1);

        if ('HTTP/1.1 200 OK' !== $headers[0]) {
            return false;
        }

        return $headers['Content-Length'];
    }

    private function getOpenFoodFactProductImageType(string $url): string
    {
        $value = exif_imagetype($url);

        return match ($value) {
            false => 'jpg',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
        };
    }

    private function getOpenFoodFactProductImageDimensions(string $url): array
    {
        $size = getimagesize($url);

        return [$size[0], $size[1]];
    }
}
