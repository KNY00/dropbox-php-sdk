<?php
use PHPUnit\Framework\TestCase;

class DropboxRequestTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var string $testedClassLoaded */
    protected static $testedClassLoaded;


    /** @var string  $method HTTP Method of the Request */
    protected static $method;

    /** @param string $endpoint API endpoint of the Request */
    protected static $endpoint;

    /** @param string $accessToken Access Token for the Request */
    protected static $accessToken;

    /** @param string $endpointType Endpoint type ['api'|'content'] */
    protected static $endpointType;

    /** @param mixed  $params Request Params */
    protected static $params;

    /** @param array  $headers Headers to send along with the Request */
    protected static $headers;

    /** @param mixed $contentType */
    protected static $contentType;

    public function setUp()
    {
        self::$testedClass  = 'Kunnu\Dropbox\DropboxRequest';

        self::$method       = 'POST';
        self::$endpoint     = 'XY';
        self::$accessToken  = '123456';
        self::$endpointType = 'api';
        self::$params       = [];
        self::$headers      = [];
        self::$headers      = [];
        self::$contentType  = null;

        $method       = self::$method;
        $endpoint     = self::$endpoint;
        $accessToken  = self::$accessToken;
        $endpointType = self::$endpointType;
        $params       = self::$params;
        $headers      = self::$headers;
        $contentType  = self::$contentType ;

        self::$testedClassLoaded = new self::$testedClass(
            $method,
            $endpoint,
            $accessToken,
            $endpointType,
            $params,
            $headers,
            $contentType
        );
    }

    public function testConstructorCallsInternalMethods()
    {
        $method       = 'POST';
        $endpoint     = 'XY';
        $accessToken  = '123456';
        $endpointType = 'api';
        $params       = [];
        $headers      = [];
        $headers      = [];
        $contentType  = 'text/xml';

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();
        $return = $constructor->invoke($instance,
        $method,
        $endpoint,
        $accessToken,
        $endpointType,
        $params,
        $headers,
        $contentType);

        $this->assertNull($return);
    }

    public function testGetMethod()
    {
        $this->assertEquals(self::$testedClassLoaded->getMethod(), self::$method);
    }

    public function testSetMethod()
    {
        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setMethod('GET'));
    }

    public function testGetAccessToken()
    {
        $this->assertEquals(self::$testedClassLoaded->getAccessToken(), self::$accessToken);
    }

    public function testSetAccessToken()
    {
        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setAccessToken('567'));
    }

    public function testGetEndpoint()
    {
        $this->assertEquals(self::$testedClassLoaded->getEndpoint(), self::$endpoint);
    }

    public function testSetEndpoint()
    {
        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setEndpoint('XY'));
    }

    public function testGetEndpointType()
    {
        $this->assertEquals(self::$testedClassLoaded->getEndpointType(), self::$endpointType);
    }

    public function testSetEndpointType()
    {
        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setEndpointType('api'));
    }

    public function testGetContentType()
    {
        // when null declared in constructor
        // keeps its default value from protected previous declaration
        $this->assertEquals(self::$testedClassLoaded->getContentType(), 'application/json');
    }

    public function testSetContentType()
    {
        $contentType = 'text/xml';

        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setContentType($contentType));
    }

    public function testGetHeaders()
    {
        $this->assertEquals(self::$testedClassLoaded->getHeaders(), []);
    }

    public function testSetHeaders()
    {
        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setHeaders([]));
    }

    public function testGetJsonBody()
    {
        $classCalled = 'Kunnu\Dropbox\Http\RequestBodyJsonEncoded';

        $this->assertInstanceOf($classCalled, self::$testedClassLoaded->getJsonBody([]));
    }

    public function testGetParams()
    {
        $this->assertEquals(self::$testedClassLoaded->getParams(), self::$params);
    }

    public function testSetParams(array $params = [])
    {
        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setParams([]));
    }

    public function testGetStreamBody()
    {
        $mock = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        // setFile
        self::$testedClassLoaded->setFile($mock);

        $classCalled = '\Kunnu\Dropbox\Http\RequestBodyStream';

        $this->assertInstanceOf($classCalled, self::$testedClassLoaded->getStreamBody());
    }

    public function testGetFile()
    {
        $mock = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        // setFile
        self::$testedClassLoaded->setFile($mock);

        $classCalled = '\Kunnu\Dropbox\Http\RequestBodyStream';

        $this->assertSame($mock, self::$testedClassLoaded->getFile());
    }

    public function testSetFile()
    {
        $mock = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $this->assertInstanceOf(self::$testedClass, self::$testedClassLoaded->setFile($mock));
    }


    public function testHasFile()
    {
        $this->assertFalse(self::$testedClassLoaded->hasFile());
    }

    public function testValidateResponse()
    {
        $this->assertTrue(self::$testedClassLoaded->validateResponse());
    }

    public function testProcessParams()
    {
        $mockDropboxFile = $this->getMockBuilder('Kunnu\Dropbox\DropboxFile')
        ->disableOriginalConstructor()
        ->getMock();

        $method       = 'POST';
        $endpoint     = 'XY';
        $accessToken  = '123456';
        $endpointType = 'api';
        $params       = [];
        $headers      = [];
        $headers      = [];
        $contentType  = null;

        $params['file'] = $mockDropboxFile;
        $params['validateResponse'] = true;

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([
            $method,
            $endpoint,
            $accessToken,
            $endpointType,
            $params,
            $headers,
            $contentType
        ]);

        $processParams = $reflectedClass->getMethod('processParams');
        $processParams->setAccessible(true);

        $return = $processParams->invoke($instance, $params);

        $this->assertEquals($return, []);
    }

}
