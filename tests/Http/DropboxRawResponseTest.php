<?php
use PHPUnit\Framework\TestCase;

class DropboxRawResponseTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    protected function setUp()
    {
        self::$testedClass = '\Kunnu\Dropbox\Http\DropboxRawResponse';
    }

    public function testConstructorCallsInternalMethods()
    {
        $headers = [
            'access' => 1
        ];
        $body = 'myBody';
        $statusCode = 200;

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($instance,  $headers, $body, $statusCode);

        $getHeaders          = $reflectedClass->getMethod('getHeaders');
        $getBody             = $reflectedClass->getMethod('getBody');
        $getHttpResponseCode = $reflectedClass->getMethod('getHttpResponseCode');

        $this->assertSame($getHeaders->invoke($instance), $headers);
        $this->assertSame($getBody->invoke($instance), $body);
        $this->assertSame($getHttpResponseCode->invoke($instance), $statusCode);
    }
}
