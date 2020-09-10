<?php

namespace Omnipay\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base Gateway Test class
 *
 * Ensures all gateways conform to consistent standards
 */
abstract class GatewayTestCase extends PHPUnitTestCase
{
    use Traits\HasBaseGatewayTestsTrait;
}
