<?php

namespace Omnipay\Tests\Traits;

use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Mockery as m;

/**
 * Trait HasRequestTrait
 * @package Omnipay\Tests\Traits
 */
trait HasRequestTrait
{

    /** @var  RequestInterface */
    private $mockRequest;

    /** @var HttpRequest */
    private $httpRequest;

    /**
     * @return RequestInterface
     */
    public function getMockRequest()
    {
        if (null === $this->mockRequest) {
            $this->mockRequest = m::mock(RequestInterface::class);
        }

        return $this->mockRequest;
    }

    /**
     * @return HttpRequest
     */
    public function getHttpRequest()
    {
        if (null === $this->httpRequest) {
            $this->httpRequest = new HttpRequest;
        }

        return $this->httpRequest;
    }
}