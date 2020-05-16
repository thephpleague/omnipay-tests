<?php

namespace Omnipay\Tests\Traits;

use Psr\Http\Message\ResponseInterface;

/**
 * Trait HasResponsesTrait
 * @package Omnipay\Tests\Traits
 */
trait HasResponsesTrait
{

    /**
     * Get a mock response for a client by mock file name
     *
     * @param string $path Relative path to the mock response file
     *
     * @return ResponseInterface
     */
    public function getMockHttpResponse($path)
    {
        if ($path instanceof ResponseInterface) {
            return $path;
        }

        $ref = new ReflectionObject($this);
        $dir = dirname($ref->getFileName());

        // if mock file doesn't exist, check parent directory
        if (!file_exists($dir.'/Mock/'.$path) && file_exists($dir.'/../Mock/'.$path)) {
            return \GuzzleHttp\Psr7\parse_response(file_get_contents($dir.'/../Mock/'.$path));
        }

        return \GuzzleHttp\Psr7\parse_response(file_get_contents($dir.'/Mock/'.$path));
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
    public function setMockHttpResponse($paths)
    {
        foreach ((array) $paths as $path) {
            $this->mockClient->addResponse($this->getMockHttpResponse($path));
        }
    }

}