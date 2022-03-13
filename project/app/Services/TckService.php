<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;

class TckService
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getBaseUrl(): string
    {
        return config('services.tck.url');
    }

    private function getBrandItemsUrl(string $brand): string
    {
        return $this->getBaseUrl() . '/items/brand/' . $brand;
    }

    private function getAssortmentUrl(int $assortmentId): string
    {
        return $this->getBaseUrl() . '/items/assortment/' . $assortmentId;
    }

    private function getSkuUrl(string $sku): string
    {
        return $this->getBaseUrl() . '/items/sku/' . $sku;
    }

    #[ArrayShape(['api_key' => "string"])]
    private function getParams(): array
    {
        return [
            'api_key' => $this->apiKey,
        ];
    }

    public function getBrandItems(string $brand)
    {
        try {
            $response = Http::get($this->getBrandItemsUrl($brand), $this->getParams())->json();
            $products = $response[0]['assortment'][0];

            foreach ($products as $key => $product) {
                $productResponse = Http::get($this->getAssortmentUrl($key), $this->getParams())->json();
                $variants = $productResponse[0]['sku'][0];

                foreach ($variants as $k => $variant) {
                    $productResponse = Http::get($this->getSkuUrl($k), $this->getParams())->json();
                }
            }
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
        }
    }
}
