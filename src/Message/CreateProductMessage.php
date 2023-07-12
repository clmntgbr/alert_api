<?php

namespace App\Message;

final class CreateProductMessage
{
    public function __construct(
        private readonly string $ean,
        private readonly array $data
    ) {
    }

    public function getEan(): string
    {
        return $this->ean;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
