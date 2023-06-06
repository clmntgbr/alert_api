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
        if (array_key_exists('categories', $response['product'])) {
            return $response['product']['categories'];
        }

        if (array_key_exists('categories_old', $response['product'])) {
            return $response['product']['categories_old'];
        }

        return null;
    }

    /**
     * @throws \HttpException
     */
    public function getOpenFoodFactProductName(array $response): string
    {
        if (array_key_exists('product_name_fr', $response['product']) && '' !== $response['product']['product_name_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['product_name_fr']))));
        }

        if (array_key_exists('product_name', $response['product']) && '' !== $response['product']['product_name']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['product_name']))));
        }

        throw new \HttpException(404, 'Product not found.');
    }

    public function getOpenFoodFactProductOrigin(array $response): ?string
    {
        if (array_key_exists('origin_fr', $response['product']) && '' !== $response['product']['origin_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['origin_fr']))));
        }

        if (array_key_exists('origins', $response['product']) && '' !== $response['product']['origins']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['origins']))));
        }

        return null;
    }

    public function getOpenFoodFactProductNutritionIngredients(array $response): ?string
    {
        if (array_key_exists('ingredients_text_with_allergens_fr', $response['product']) && '' !== $response['product']['ingredients_text_with_allergens_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['ingredients_text_with_allergens_fr']))));
        }

        if (array_key_exists('ingredients_text_with_allergens', $response['product']) && '' !== $response['product']['ingredients_text_with_allergens']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['ingredients_text_with_allergens']))));
        }

        if (array_key_exists('ingredients_text_fr', $response['product']) && '' !== $response['product']['ingredients_text_fr']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['ingredients_text_fr']))));
        }

        if (array_key_exists('ingredients_text', $response['product']) && '' !== $response['product']['ingredients_text']) {
            return ucwords(strtolower(trim(strip_tags($response['product']['ingredients_text']))));
        }

        return null;
    }

    public function getOpenFoodFactProductLink(array $response): ?string
    {
        if (array_key_exists('link', $response['product']) && '' !== $response['product']['link']) {
            return $response['product']['link'];
        }

        return null;
    }

    public function getOpenFoodFactProductManufacturingPlace(array $response): ?string
    {
        if (array_key_exists('manufacturing_places', $response['product']) && '' !== $response['product']['manufacturing_places']) {
            return $response['product']['manufacturing_places'];
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
