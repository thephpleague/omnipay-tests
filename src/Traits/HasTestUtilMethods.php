<?php

namespace Omnipay\Tests\Traits;

use Guzzle\Common\Event;
use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omnipay\Common\Http\Client;
use ReflectionObject;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Trait HasTestUtilMethods
 * @package Omnipay\Tests\Traits
 */
trait HasTestUtilMethods
{
    use MockeryPHPUnitIntegration;
    use HasRequestTrait;
    use HasClientTrait;

    /**
     * Mark a request as being mocked
     *
     * @param GuzzleRequestInterface $request
     *
     * @return self
     */
    public function addMockedHttpRequest(GuzzleRequestInterface $request)
    {
        $this->mockHttpRequests[] = $request;

        return $this;
    }

    /**
     * Get all of the mocked requests
     *
     * @return array
     */
    public function getMockedRequests()
    {
        return $this->mockHttpRequests;
    }

    /**
     * Get a mock response for a client by mock file name
     *
     * @param string $path Relative path to the mock response file
     *
     * @return Response
     */
    public function getMockHttpResponse($path)
    {
        if ($path instanceof Response) {
            return $path;
        }

        $ref = new ReflectionObject($this);
        $dir = dirname($ref->getFileName());

        // if mock file doesn't exist, check parent directory
        if (!file_exists($dir.'/Mock/'.$path) && file_exists($dir.'/../Mock/'.$path)) {
            return MockPlugin::getMockFile($dir.'/../Mock/'.$path);
        }

        return MockPlugin::getMockFile($dir.'/Mock/'.$path);
    }

    /**
     * Set a mock response from a mock file on the next client request.
     *
     * This method assumes that mock response files are located under the
     * Mock/ subdirectory of the current class. A mock response is added to the next
     * request sent by the client.
     *
     * An array of path can be provided and the next x number of client requests are
     * mocked in the order of the array where x = the array length.
     *
     * @param array|string $paths Path to files within the Mock folder of the service
     *
     * @return MockPlugin returns the created mock plugin
     */
    public function setMockHttpResponse($paths)
    {
        $this->mockHttpRequests = [];
        $that = $this;
        $mock = new MockPlugin(null, true);
        $this->getHttpClient()->getEventDispatcher()->removeSubscriber($mock);
        $mock->getEventDispatcher()->addListener('mock.request', function (Event $event) use ($that) {
            $that->addMockedHttpRequest($event['request']);
        });

        foreach ((array)$paths as $path) {
            $mock->addResponse($this->getMockHttpResponse($path));
        }

        $this->getHttpClient()->getEventDispatcher()->addSubscriber($mock);

        return $mock;
    }

    /**
     * @return m\MockInterface
     */
    public function getMockRequest()
    {
        if (null === $this->mockRequest) {
            $this->mockRequest = m::mock(\Omnipay\Common\Message\RequestInterface::class);
        }

        return $this->mockRequest;
    }


    /**
     * @return Client
     */
    public function getHttpClientReal()
    {
        if (null === $this->httpClientReal) {
            $this->httpClientReal = new Client();
        }

        return $this->httpClientReal;
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
