<?php

namespace Omnipay\Tests\Traits;

use Http\Mock\Client as MockClient;
use Omnipay\Common\Http\Client;
use Omnipay\Common\Http\ClientInterface;

/**
 * Trait HasClientTrait
 * @package Omnipay\Tests\Traits
 */
trait HasClientTrait
{

    /** @var  MockClient */
    private $mockClient;

    /** @var ClientInterface */
    private $httpClient;


    /**
     * Get all of the mocked requests
     *
     * @return array
     */
    public function getMockedRequests()
    {
        return $this->mockClient->getRequests();
    }

    /**
     * @return MockClient
     */
    public function getMockClient()
    {
        if (null === $this->mockClient) {
            $this->mockClient = new MockClient();
        }

        return $this->mockClient;
    }

    /**
     * @return Client|ClientInterface
     */
    public function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new Client(
                $this->getMockClient()
            );
        }

        return $this->httpClient;
    }
}