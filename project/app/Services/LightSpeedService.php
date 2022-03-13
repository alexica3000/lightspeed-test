<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;

class LightSpeedService
{
    private string $apiKey;
    private string $secret;
    private string $language;
    private string $cluster;

    public function __construct(string $cluster, string $apiKey, string $secret, string $language)
    {
        $this->apiKey = $apiKey;
        $this->secret = $secret;
        $this->language = $language;
        $this->cluster = $cluster;
    }

    /**
     * @throws \WebshopappApiException
     */
    public function getClient(): \WebshopappApiClient
    {
        return new \WebshopappApiClient($this->cluster, $this->apiKey, $this->secret, $this->language);
    }
}
