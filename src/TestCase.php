<?php

namespace Omnipay\Tests;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Omnipay\Common\Http\Client;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\RequestInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionObject;
use Http\Mock\Client as MockClient;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use function GuzzleHttp\Psr7\parse_response;

/**
 * Base class for all Omnipay tests
 *
 * Guzzle mock methods area based on those in GuzzleTestCase
 */
abstract class TestCase extends PHPUnitTestCase
{
    use MockeryPHPUnitIntegration;

    /** @var  RequestInterface */
    private $mockRequest;

    /** @var  MockClient */
    private $mockClient;

    /** @var ClientInterface */
    private $httpClient;

    /** @var HttpRequest */
    private $httpRequest;

    /**
     * Converts a string to camel case
     *
     * @param string $str
     * @return string
     */
    public function camelCase(string $str): string
    {
        return preg_replace_callback(
            '/_([a-z])/',
            static function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Get all of the mocked requests
     *
     * @return PsrRequestInterface[]
     */
    public function getMockedRequests(): array
    {
        return $this->mockClient->getRequests();
    }

    /**
     * Get a mock response for a client by mock file name
     *
     * @param string $path Relative path to the mock response file
     *
     * @return ResponseInterface
     */
    public function getMockHttpResponse(string $path): ResponseInterface
    {
        if ($path instanceof ResponseInterface) {
            return $path;
        }

        $ref = new ReflectionObject($this);
        $dir = dirname($ref->getFileName());

        // if mock file doesn't exist, check parent directory
        if (!file_exists($dir.'/Mock/'.$path) && file_exists($dir.'/../Mock/'.$path)) {
            return parse_response(file_get_contents($dir.'/../Mock/'.$path));
        }

        return parse_response(file_get_contents($dir.'/Mock/'.$path));
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
     * @return void returns the created mock plugin
     */
    public function setMockHttpResponse($paths): void
    {
        foreach ((array) $paths as $path) {
            $this->mockClient->addResponse($this->getMockHttpResponse($path));
        }
    }

    /**
     * Helper method used by gateway test classes to generate a valid test credit card
     */
    public function getValidCard(): array
    {
        return [
            'firstName' => 'Example',
            'lastName' => 'User',
            'number' => '4111111111111111',
            'expiryMonth' => mt_rand(1, 12),
            'expiryYear' => gmdate('Y') + mt_rand(1, 5),
            'cvv' => mt_rand(100, 999),
            'billingAddress1' => '123 Billing St',
            'billingAddress2' => 'Billsville',
            'billingCity' => 'Billstown',
            'billingPostcode' => '12345',
            'billingState' => 'CA',
            'billingCountry' => 'US',
            'billingPhone' => '(555) 123-4567',
            'shippingAddress1' => '123 Shipping St',
            'shippingAddress2' => 'Shipsville',
            'shippingCity' => 'Shipstown',
            'shippingPostcode' => '54321',
            'shippingState' => 'NY',
            'shippingCountry' => 'US',
            'shippingPhone' => '(555) 987-6543',
        ];
    }

    public function getMockRequest(): RequestInterface
    {
        if (null === $this->mockRequest) {
            $this->mockRequest = m::mock(RequestInterface::class);
        }

        return $this->mockRequest;
    }

    public function getMockClient(): MockClient
    {
        if (null === $this->mockClient) {
            $this->mockClient = new MockClient();
        }

        return $this->mockClient;
    }

    public function getHttpClient(): ClientInterface
    {
        if (null === $this->httpClient) {
            $this->httpClient = new Client(
                $this->getMockClient()
            );
        }

        return $this->httpClient;
    }

    public function getHttpRequest(): HttpRequest
    {
        if (null === $this->httpRequest) {
            $this->httpRequest = new HttpRequest;
        }

        return $this->httpRequest;
    }
}
