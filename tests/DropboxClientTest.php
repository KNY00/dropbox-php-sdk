<?php
use PHPUnit\Framework\TestCase;

class DropboxClientTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass;

    /** @var string $testedClassLoaded */
    public static $testedClassLoaded;

    /** @var Kunnu\Dropbox\Http\Clients\DropboxHttpClientInterface $mockClient */
    public static $mockClient;

    protected function setUp()
    {
        self::$mockClient = $this->getMockBuilder('Kunnu\Dropbox\Http\Clients\DropboxHttpClientInterface')
        ->getMock();

        self::$testedClass = 'Kunnu\Dropbox\DropboxClient';

        self::$testedClassLoaded = new self::$testedClass(self::$mockClient);
    }

    public function testGetHttpClient()
    {
        $this->assertSame(self::$mockClient ,self::$testedClassLoaded->getHttpClient());
    }

    public function testSetHttpClient()
    {
        $this->assertSame(self::$testedClassLoaded, self::$testedClassLoaded->setHttpClient(self::$mockClient));
    }

    public function testGetBasePath()
    {
        $this->assertSame('https://api.dropboxapi.com/2' ,self::$testedClassLoaded->getBasePath());
    }

    public function testGetContentPath()
    {
        $this->assertSame('https://content.dropboxapi.com/2' ,self::$testedClassLoaded->getContentPath());
    }

    public function testBuildAuthHeader()
    {
        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstance(self::$mockClient);

        $method = $reflectedClass->getMethod('buildAuthHeader');
        $method->setAccessible(true);

        $token = '111xxxyyy';

        $return = $method->invoke($instance, $token);

        $this->assertArrayHasKey('Authorization', $return);
    }

    public function testBuildContentTypeHeader()
    {
        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstance(self::$mockClient);

        $method = $reflectedClass->getMethod('buildContentTypeHeader');
        $method->setAccessible(true);

        $contentType = "application/json";

        $return = $method->invoke($instance, $contentType);

        $this->assertArrayHasKey('Content-Type', $return);
    }

    public function testBuildUrl()
    {

        $reflectedClass = new \ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstance(self::$mockClient);

        $method = $reflectedClass->getMethod('buildUrl');
        $method->setAccessible(true);

        $endpoint = '56';
        $type = 'content';

        $return = $method->invoke($instance, $endpoint, $type);

        $this->assertSame('https://content.dropboxapi.com/2' . $endpoint, $return);
    }


    public function testSendRequestA()
    {
        // mock DropboxrawResponse
        // returned from method send()
        $mockRawResponse = $this->getMockBuilder('\Kunnu\Dropbox\Http\DropboxRawResponse')
        ->disableOriginalConstructor()
        ->setMethods(['getHttpResponseCode', 'getHeaders'])
        ->getMock();
        $mockRawResponse->expects($this->any())
        ->method('getHttpResponseCode')
        ->will(
            $this->returnValue(200)
        );
        $mockRawResponse->expects($this->any())
        ->method('getHeaders')
        ->will(
            $this->returnValue([])
        );


        // mock client
        // this is passed to constructor of tested class
        $mockClient = self::$mockClient;
        $mockClient->expects($this->any())
        ->method('send')
        ->will(
            $this->returnValue($mockRawResponse)
        );


        // first parameter of tested function
        $mockRequest = $this->getMockBuilder('\Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();
        // first method called
        $mockRequest->expects($this->any())
        ->method('getMethod')
        ->will(
            $this->returnValue('GET')
        );


        // second parameter of tested function
        $mockResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponseToFile')
        ->disableOriginalConstructor()
        ->setMethods(['getFilePath', 'setHttpResponseCode', 'setHeaders', 'setBody'])
        ->getMock();
        // method prepareRequest
        $mockResponse->expects($this->any())
        ->method('getFilePath')
        ->will(
            $this->returnValue('ttx/file.txt')
        );
        $mockResponse->expects($this->any())
        ->method('setHttpResponseCode')
        ->with(
            $this->equalTo(200)
        );
        $mockResponse->expects($this->any())
        ->method('setHeaders')
        ->with(
            $this->equalTo([])
        );
        $mockResponse->expects($this->any())
        ->method('setBody')
        ->with(
            $this->equalTo('body')
        );


        // mock tested class to instantiate with
        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['prepareRequest', 'getHttpClient'])
        ->getMock();

        // method prepareRequest
        $mockTestedClass->expects($this->any())
        ->method('prepareRequest')
        ->with(
            $this->equalTo($mockRequest)
        )
        ->will(
            $this->returnValue(['https://api.got.com/', [], 'body'])
        );

        // mock getHttpClient
        $mockTestedClass->expects($this->any())
        ->method('getHttpClient')
        ->will(
            $this->returnValue($mockClient)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('sendRequest');

        $return = $method->invoke($mockTestedClass, $mockRequest, $mockResponse);

        $this->assertInstanceOf('\Kunnu\Dropbox\DropboxResponseToFile', $return);
    }

    public function testSendRequestB()
    {
        // mock DropboxrawResponse
        // returned from method send()
        $mockRawResponse = $this->getMockBuilder('\Kunnu\Dropbox\Http\DropboxRawResponse')
        ->disableOriginalConstructor()
        ->setMethods(['getHttpResponseCode', 'getHeaders', 'getBody'])
        ->getMock();
        $mockRawResponse->expects($this->any())
        ->method('getHttpResponseCode')
        ->will(
            $this->returnValue(200)
        );
        $mockRawResponse->expects($this->any())
        ->method('getHeaders')
        ->will(
            $this->returnValue([])
        );
        $mockRawResponse->expects($this->any())
        ->method('getBody')
        ->will(
            $this->returnValue('body')
        );


        // mock client
        // this is passed to constructor of tested class
        $mockClient = self::$mockClient;
        $mockClient->expects($this->any())
        ->method('send')
        ->will(
            $this->returnValue($mockRawResponse)
        );


        // first parameter of tested function
        $mockRequest = $this->getMockBuilder('\Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->getMock();
        // first method called
        $mockRequest->expects($this->any())
        ->method('getMethod')
        ->will(
            $this->returnValue('GET')
        );


        // second parameter of tested function
        $mockResponse = $this->getMockBuilder('\Kunnu\Dropbox\DropboxResponse')
        ->disableOriginalConstructor()
        ->setMethods(['getFilePath', 'setHttpResponseCode', 'setHeaders', 'setBody'])
        ->getMock();
        $mockResponse->expects($this->any())
        ->method('setHttpResponseCode')
        ->with(
            $this->equalTo(200)
        );
        $mockResponse->expects($this->any())
        ->method('setHeaders')
        ->with(
            $this->equalTo([])
        );
        $mockResponse->expects($this->any())
        ->method('setBody')
        ->with(
            $this->equalTo('body')
        );


        // mock tested class to instantiate with
        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['prepareRequest', 'getHttpClient'])
        ->getMock();

        // method prepareRequest
        $mockTestedClass->expects($this->any())
        ->method('prepareRequest')
        ->with(
            $this->equalTo($mockRequest)
        )
        ->will(
            $this->returnValue(['https://api.got.com/', [], 'body'])
        );

        // mock getHttpClient
        $mockTestedClass->expects($this->any())
        ->method('getHttpClient')
        ->will(
            $this->returnValue($mockClient)
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('sendRequest');

        $return = $method->invoke($mockTestedClass, $mockRequest, $mockResponse);

        $this->assertInstanceOf('\Kunnu\Dropbox\DropboxResponse', $return);
    }

    public function testPrepareRequest()
    {
        $bodyMock = $this->getMockBuilder('\Kunnu\Dropbox\Http\DropboxRawResponse')
        ->disableOriginalConstructor()
        ->setMethods(['getBody'])
        ->getMock();

        $bodyMock->expects($this->any())
        ->method('getBody')
        ->will(
            $this->returnValue('body')
        );


        $mockTestedClass = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['buildUrl'])
        ->getMock();

        $mockTestedClass->expects($this->any())
        ->method('buildUrl')
        ->with(
            $this->logicalOr(
                $this->equalTo('endpoint'),
                [$this->equalTo('content'), $this->equalTo('content'), $this->equalTo('not-content')]
            )
        )
        ->will(
            $this->returnValue('url://')
        );

        $arrayFunctions = [
            'getEndpoint',
            'getEndpointType',
            'setHeaders',
            'getParams',
            'hasFile',
            'setContentType',
            'getStreamBody',
            'getJsonBody',
            'getAccessToken',
            'getContentType',
            'getHeaders'
        ];

        $mockRequest = $this->getMockBuilder('\Kunnu\Dropbox\DropboxRequest')
        ->disableOriginalConstructor()
        ->setMethods($arrayFunctions)
        ->getMock();

        $mockRequest->expects($this->any())
        ->method('getEndpoint')
        ->will(
            $this->returnValue('endpoint')
        );

        $mockRequest->expects($this->any())
        ->method('getEndpointType')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue('content'),
                $this->returnValue('content'),

                $this->returnValue('content'),
                $this->returnValue('content'),

                $this->returnValue('not-content'),
                $this->returnValue('not-content')
            )
        );

        $mockRequest->expects($this->any())
        ->method('setHeaders')
        ->will(
            $this->returnValue([])
        );

        $mockRequest->expects($this->any())
        ->method('getParams')
        ->will(
            $this->returnValue([])
        );

        $mockRequest->expects($this->any())
        ->method('hasFile')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue(true),
                $this->returnValue(false)
            )
        );

        $mockRequest->expects($this->any())
        ->method('setContentType')
        ->with(
            $this->logicalOr(
                $this->equalTo('application/octet-stream'),
                $this->equalTo('')
            )
        );

        $mockRequest->expects($this->any())
        ->method('getStreamBody')
        ->will(
            $this->returnValue($bodyMock)
        );

        $mockRequest->expects($this->any())
        ->method('getJsonBody')
        ->will(
            $this->returnValue($bodyMock)
        );

        $mockRequest->expects($this->any())
        ->method('getStreamBody')
        ->will(
            $this->returnValue($bodyMock)
        );

        $mockRequest->expects($this->any())
        ->method('getAccessToken')
        ->will(
            $this->returnValue('xx99yy')
        );


        $mockRequest->expects($this->any())
        ->method('getContentType')
        ->will(
            $this->onConsecutiveCalls(
                $this->returnValue('application/octet-stream'),
                $this->returnValue(''),
                $this->returnValue('')
            )
        );

        $mockRequest->expects($this->any())
        ->method('getHeaders')
        ->will(
            $this->returnValue([])
        );

        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $method = $reflectedClass->getMethod('prepareRequest');
        $method->setAccessible(true);

        $return = $method->invoke($mockTestedClass, $mockRequest);

        $array_expected =  [
            'url://',
            [
                'Authorization' => 'Bearer xx99yy',
                'Content-Type' => 'application/octet-stream'
            ],
            'body'
        ];

        $this->assertSame($array_expected, $return);


        $return = $method->invoke($mockTestedClass, $mockRequest);

        $array_expected =  [
            'url://',
            [
                'Authorization' => 'Bearer xx99yy',
                'Content-Type' => ''
            ],
            null
        ];

        $this->assertSame($array_expected, $return);


        $return = $method->invoke($mockTestedClass, $mockRequest);

        $array_expected =  [
            'url://',
            [
                'Authorization' => 'Bearer xx99yy',
                'Content-Type' => ''
            ],
            'body'
        ];

        $this->assertSame($array_expected, $return);
    }


}
