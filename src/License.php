<?php

namespace Licenzo;

class License
{
    private string $license;
    private string $product;
    private string $variation;
    private ?string $hash;

    public function __construct(string $license, string $product, string $variation, ?string $hash = null)
    {
        $this->license = $license;
        $this->product = $product;
        $this->variation = $variation;
        $this->hash = $hash;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function getVariation(): ?string
    {
        return $this->variation;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}
