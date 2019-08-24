<?php
use PHPUnit\Framework\TestCase;

class RequestBodyJsonEncoded extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass = 'Kunnu\Dropbox\Http\RequestBodyJsonEncoded';

    public function testConstructorCallsInternalMethods()
    {
        // empty array
        $paramsEmpty = [];

        // nonempty array
        $paramsNotEmpty = [1,2,3,4];

        $client = new GuzzleHttp\Client;

        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        // instantiate class with empty array
        $constructor->invoke($instance, $paramsEmpty);

        $getBody = $reflectedClass->getMethod('getBody');

        // will return null
        $this->assertNull($getBody->invoke($instance));

        // instantiate class with nonempty array
        $constructor->invoke($instance, $paramsNotEmpty);

        // assert that it will return a json_encode( $params )
        $this->assertEquals($getBody->invoke($instance), json_encode($paramsNotEmpty));
    }
}
