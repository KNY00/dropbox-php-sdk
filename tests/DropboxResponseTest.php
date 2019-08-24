<?php
use PHPUnit\Framework\TestCase;

class DropboxResponseTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass;

    /** @var Kunnu\Dropbox\DropboxResponse $data */
    protected static $testedClassLoaded;

    /** @var Kunnu\Dropbox\DropboxRequest $request */
    protected static $request;

    protected function setUp()
    {
        $mockRequest = $this->getMockBuilder('Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();

        self::$request = $mockRequest;

        $request = self::$request;
        $body = null;
        $httpStatusCode = null;
        $headers = [];

        self::$testedClass = 'Kunnu\Dropbox\DropboxResponse';

        self::$testedClassLoaded = new self::$testedClass($request, $body, $httpStatusCode, $headers);
    }

    public function testSetBody()
    {
        $class = self::$testedClassLoaded;

        $class->setBody('hello world');

        $this->assertSame('hello world', $class->getBody());
    }


    public function testSetHttpStatusCode()
    {
        $class = self::$testedClassLoaded;

        $class->setHttpStatusCode(200);

        $this->assertSame(200, $class->getHttpStatusCode());

        $this->assertInternalType("int", $class->getHttpStatusCode());
    }

    public function testSetHeaders()
    {
        $class = self::$testedClassLoaded;

        $class->setHeaders([1,2,3,4]);

        $this->assertSame([1,2,3,4], $class->getHeaders());
    }

    public function testGetRequest()
    {
        $class = self::$testedClassLoaded;

        $this->assertInstanceOf('Kunnu\Dropbox\DropboxRequest', $class->getRequest());
    }

    public function testGetDecodedBody()
    {
        $class = self::$testedClassLoaded;

        $this->assertEquals([], $class->getDecodedBody());
    }

    public function testGetAccessToken()
    {
        $token = '123XYZ';

        $mockRequest = $this->getMockBuilder('Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();

        $mockRequest->expects($this->once())
        ->method('getAccessToken')
        ->will(
            $this->returnValue($token)
        );

        // every other var is set to default
        $class = new self::$testedClass($mockRequest);
        $this->assertEquals($class->getAccessToken(), $token);
    }

    public function testDecodeBody()
    {
        $mockRequest = $this->getMockBuilder('Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();

        $mockRequest->expects($this->once())
        ->method('validateResponse')
        ->will(
            $this->returnValue(true)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $headers['Content-Type'] = ['application/json'];

        $instance = $reflectedClass->newInstanceArgs([$mockRequest, null, null, $headers]);

        $validateResponse = $reflectedClass->getMethod('decodeBody');
        $validateResponse->setAccessible(true);

        $this->assertNull($validateResponse->invoke($instance));
    }

    public function testValidateResponse()
    {
        $mockRequest = $this->getMockBuilder('Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceArgs([$mockRequest]);

        $validateResponse = $reflectedClass->getMethod('validateResponse');
        $validateResponse->setAccessible(true);

        $this->assertNull($validateResponse->invoke($instance));

        $property = $reflectedClass->getProperty('decodedBody');
        $property->setAccessible(true);
        $property->setValue($instance, null);

        $validateResponse = $reflectedClass->getMethod('validateResponse');
        $validateResponse->setAccessible(true);

        $this->expectException(Kunnu\Dropbox\Exceptions\DropboxClientException::class);

        $validateResponse->invoke($instance);
    }
}
