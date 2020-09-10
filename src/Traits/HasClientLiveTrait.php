<?php

namespace Omnipay\Tests\Traits;

use Omnipay\Common\Http\Client;
use Omnipay\Common\Http\ClientInterface;

/**
 * Trait HasClientTrait
 * @package Omnipay\Tests\Traits
 */
trait HasClientLiveTrait
{

    /** @var ClientInterface */
    private $httpClientLive;

    /**
     * @return Client
     */
    public function getHttpClientReal()
    {
        if (null === $this->httpClientLive) {
            $this->httpClientLive = new Client();
        }

        return $this->httpClientLive;
    }
}