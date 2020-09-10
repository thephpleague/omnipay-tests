<?php

namespace Omnipay\Tests;

use Omnipay\Tests\Traits\HasTestUtilMethods;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base class for all Omnipay tests
 *
 * Guzzle mock methods area based on those in GuzzleTestCase
 */
abstract class TestCase extends PHPUnitTestCase
{
    use HasTestUtilMethods;
}
