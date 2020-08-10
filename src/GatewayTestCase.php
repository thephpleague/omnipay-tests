<?php

namespace Omnipay\Tests;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

/**
 * Base Gateway Test class
 *
 * Ensures all gateways conform to consistent standards
 */
abstract class GatewayTestCase extends TestCase
{
    /** @var AbstractGateway */
    protected $gateway;

    public function testGetNameNotEmpty(): void
    {
        $name = $this->gateway->getName();
        self::assertNotEmpty($name);
        self::assertIsString($name);
    }

    public function testGetShortNameNotEmpty(): void
    {
        $shortName = $this->gateway->getShortName();
        self::assertNotEmpty($shortName);
        self::assertIsString($shortName);
    }

    public function testGetDefaultParametersReturnsArray(): void
    {
        $settings = $this->gateway->getDefaultParameters();
        self::assertIsArray($settings);
    }

    public function testDefaultParametersHaveMatchingMethods(): void
    {
        $settings = $this->gateway->getDefaultParameters();
        foreach ($settings as $key => $default) {
            $getter = 'get'.ucfirst($this->camelCase($key));
            $setter = 'set'.ucfirst($this->camelCase($key));
            $value = uniqid('', true);

            self::assertTrue(method_exists($this->gateway, $getter), "Gateway must implement $getter()");
            self::assertTrue(method_exists($this->gateway, $setter), "Gateway must implement $setter()");

            // setter must return instance
            self::assertSame($this->gateway, $this->gateway->$setter($value));
            self::assertSame($value, $this->gateway->$getter());
        }
    }

    public function testTestMode(): void
    {
        self::assertSame($this->gateway, $this->gateway->setTestMode(false));
        self::assertFalse($this->gateway->getTestMode());

        self::assertSame($this->gateway, $this->gateway->setTestMode(true));
        self::assertTrue($this->gateway->getTestMode());
    }

    public function testCurrency(): void
    {
        // currency is normalized to uppercase
        self::assertSame($this->gateway, $this->gateway->setCurrency('eur'));
        self::assertSame('EUR', $this->gateway->getCurrency());
    }

    public function testSupportsAuthorize(): void
    {
        $supportsAuthorize = $this->gateway->supportsAuthorize();
        self::assertIsBool($supportsAuthorize);

        if ($supportsAuthorize) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->authorize());
        } else {
            self::assertFalse(method_exists($this->gateway, 'authorize'));
        }
    }

    public function testSupportsCompleteAuthorize(): void
    {
        $supportsCompleteAuthorize = $this->gateway->supportsCompleteAuthorize();
        self::assertIsBool($supportsCompleteAuthorize);

        if ($supportsCompleteAuthorize) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->completeAuthorize());
        } else {
            self::assertFalse(method_exists($this->gateway, 'completeAuthorize'));
        }
    }

    public function testSupportsCapture(): void
    {
        $supportsCapture = $this->gateway->supportsCapture();
        self::assertIsBool($supportsCapture);

        if ($supportsCapture) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->capture());
        } else {
            self::assertFalse(method_exists($this->gateway, 'capture'));
        }
    }

    public function testSupportsPurchase(): void
    {
        $supportsPurchase = $this->gateway->supportsPurchase();
        self::assertIsBool($supportsPurchase);

        if ($supportsPurchase) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->purchase());
        } else {
            self::assertFalse(method_exists($this->gateway, 'purchase'));
        }
    }

    public function testSupportsCompletePurchase(): void
    {
        $supportsCompletePurchase = $this->gateway->supportsCompletePurchase();
        self::assertIsBool($supportsCompletePurchase);

        if ($supportsCompletePurchase) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->completePurchase());
        } else {
            self::assertFalse(method_exists($this->gateway, 'completePurchase'));
        }
    }

    public function testSupportsRefund(): void
    {
        $supportsRefund = $this->gateway->supportsRefund();
        self::assertIsBool($supportsRefund);

        if ($supportsRefund) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->refund());
        } else {
            self::assertFalse(method_exists($this->gateway, 'refund'));
        }
    }

    public function testSupportsVoid(): void
    {
        $supportsVoid = $this->gateway->supportsVoid();
        self::assertIsBool($supportsVoid);

        if ($supportsVoid) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->void());
        } else {
            self::assertFalse(method_exists($this->gateway, 'void'));
        }
    }

    public function testSupportsCreateCard(): void
    {
        $supportsCreate = $this->gateway->supportsCreateCard();
        self::assertIsBool($supportsCreate);

        if ($supportsCreate) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->createCard());
        } else {
            self::assertFalse(method_exists($this->gateway, 'createCard'));
        }
    }

    public function testSupportsDeleteCard(): void
    {
        $supportsDelete = $this->gateway->supportsDeleteCard();
        self::assertIsBool($supportsDelete);

        if ($supportsDelete) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->deleteCard());
        } else {
            self::assertFalse(method_exists($this->gateway, 'deleteCard'));
        }
    }

    public function testSupportsUpdateCard(): void
    {
        $supportsUpdate = $this->gateway->supportsUpdateCard();
        self::assertIsBool($supportsUpdate);

        if ($supportsUpdate) {
            self::assertInstanceOf(RequestInterface::class, $this->gateway->updateCard());
        } else {
            self::assertFalse(method_exists($this->gateway, 'updateCard'));
        }
    }

    public function testAuthorizeParameters(): void
    {
        if ($this->gateway->supportsAuthorize()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->authorize();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testCompleteAuthorizeParameters(): void
    {
        if ($this->gateway->supportsCompleteAuthorize()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->completeAuthorize();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testCaptureParameters(): void
    {
        if ($this->gateway->supportsCapture()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->capture();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testPurchaseParameters(): void
    {
        if ($this->gateway->supportsPurchase()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->purchase();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testCompletePurchaseParameters(): void
    {
        if ($this->gateway->supportsCompletePurchase()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->completePurchase();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testRefundParameters(): void
    {
        if ($this->gateway->supportsRefund()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->refund();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testVoidParameters(): void
    {
        if ($this->gateway->supportsVoid()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->void();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testCreateCardParameters(): void
    {
        if ($this->gateway->supportsCreateCard()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->createCard();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testDeleteCardParameters(): void
    {
        if ($this->gateway->supportsDeleteCard()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->deleteCard();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    public function testUpdateCardParameters(): void
    {
        if ($this->gateway->supportsUpdateCard()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                // set property on gateway
                $getter = 'get'.ucfirst($this->camelCase($key));
                $setter = 'set'.ucfirst($this->camelCase($key));
                $value = uniqid('', true);
                $this->gateway->$setter($value);

                // request should have matching property, with correct value
                $request = $this->gateway->updateCard();
                self::assertSame($value, $request->$getter());
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
    }
}
