<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;

class TckService
{
    private string $apiKey;
    private LightSpeedService $lightSpeedService;

    public function __construct(string $apiKey, LightSpeedService $lightSpeedService)
    {
        $this->apiKey = $apiKey;
        $this->lightSpeedService = $lightSpeedService;
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

    public function importProducts(string $brand)
    {
        try {
            logger()->info('start lightspeed:import-products command');
            $response = Http::get($this->getBrandItemsUrl($brand), $this->getParams())->json();
            $assortments = $response[0]['assortment'][0];
            logger()->info('found ' . count($assortments) . ' assortments');

            foreach ($assortments as $key => $assortment) {
                $assortmentResponse = Http::get($this->getAssortmentUrl($key), $this->getParams())->json();
                $skus = $assortmentResponse[0]['sku'][0];
                logger()->info('found ' . count($assortments) . ' skus');

                foreach ($skus as $k => $sku) {
                    $skuResponse = Http::get($this->getSkuUrl($k), $this->getParams())->json();
                    $lightSpeedClient = $this->lightSpeedService->getClient();
                    $product = $lightSpeedClient->products->create([
                        "visibility"    => "visible",
                        "data01"        => "",
                        "data02"        => "",
                        "data03"        => "",
                        "title"         => $skuResponse[0]['title'][0]['en-GB'],
                        "fulltitle"     => $skuResponse[0]['title'][0]['en-GB'],
                        "description"   => $skuResponse[0]['description'][0]['en-GB'],
                        "content"       => "",
                        "brand"         => 4446231 // brand id for LOWA
                    ]);

                    $variants = $lightSpeedClient->variants->get(null, ['product' => $product['id']]);
                    $lightSpeedClient->variants->update($variants[0]['id'], ['priceIncl' => $skuResponse[0]['price'][0]['sale']]);
                }
            }
            logger()->info('end lightspeed:import-products command');
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
        }
    }
}
