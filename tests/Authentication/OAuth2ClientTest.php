<?php
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Kunnu\Dropbox\Authentication\OAuth2Client
 */
class OAuth2ClientTest extends TestCase
{
    /** @var string $testedClass*/
    protected static $testedClass;

    /** @var \Kunnu\Dropbox\DropboxApp $dropboxAppMock */
    protected static $dropboxAppMock;

    /** @var \Kunnu\Dropbox\DropboxClient $dropboxClientMock */
    protected static $dropboxClientMock;

    /** @var \Kunnu\Dropbox\\Kunnu\Dropbox\Security\RandomStringGeneratorInterface $randomStringGeneratorInterfaceMock */
    protected static $randomStringGeneratorInterfaceMock;

    /** @var \Kunnu\Dropbox\Authentication\OAuth2Client $testedClassMock */
    protected static $testedClassMock;

    protected function setUp()
    {
        self::$testedClass = '\Kunnu\Dropbox\Authentication\OAuth2Client';

        // mock DropboxApp
        self::$dropboxAppMock = $this->createMockHelper('\Kunnu\Dropbox\DropboxApp');

        // mock DropboxClient
        self::$dropboxClientMock = $this->createMockHelper('\Kunnu\Dropbox\DropboxClient');

        // mock DropboxClient
        self::$randomStringGeneratorInterfaceMock = $this->createMockHelper('\Kunnu\Dropbox\Security\RandomStringGeneratorInterface');

        // mock tested class
        self::$testedClassMock = $this->createMockHelper(self::$testedClass);
    }

    /**
     * creates a mock of \Kunnu\Dropbox\Authentication\OAuth2Client
     *
     * @param string $path
     *
     * @return object mock of $oAuth2Client
     */
    private function createMockHelper($path)
    {
        $mock = $this->getMockBuilder($path)
        ->disableOriginalConstructor()
        ->getMock();

        return $mock;
    }

    /**
     * returns constructor's output
     *
     * @param \Kunnu\Dropbox\DropboxApp $app
     * @param \Kunnu\Dropbox\DropboxClient $client
     * @param \Kunnu\Dropbox\Security\RandomStringGeneratorInterface $randStrGenerator
     *
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     *
     */
    private function reflectConstructor($app, $client, $randStrGenerator = null)
    {
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        return $constructor->invoke($instance, $app, $client, $randStrGenerator);
    }

    public function testConstructorCallsInternalMethods()
    {
        $this->assertNull($this->reflectConstructor(self::$dropboxAppMock, self::$dropboxClientMock));
    }

    public function testBuildUrl()
    {
        $endpoint = '/test.php';
        $params = [
            'test' => 1,
            'send' => 'hello'
        ];
        $base_url = 'https://dropbox.com';

        $queryParams = http_build_query($params);

        $builtIn = $base_url . $endpoint . '?' . $queryParams;

        $class = new ReflectionClass(self::$testedClass);
        $instance = $class->newInstanceArgs([self::$dropboxAppMock, self::$dropboxClientMock]);

        // Reflect protected Method buildUrl()
        $method = $class->getMethod('buildUrl');
        $method->setAccessible(true);

        $output = $method->invoke($instance, $endpoint, $params);

        $this->assertEquals($output, $builtIn);
    }

    /**
     * @covers ::getApp
     * @covers ::getClient
     */
    public function testGetAppOrClient()
    {
        $class = new self::$testedClass(self::$dropboxAppMock, self::$dropboxClientMock);

        $outputGetApp = $class->getApp();
        $outputGetClient = $class->getClient();

        $this->assertSame($outputGetApp, self::$dropboxAppMock);
        $this->assertSame($outputGetClient, self::$dropboxClientMock);
    }

    public function testGetAuthorizationUrl()
    {
        $redirectUri = 'xx';
        $state = 'Colorado';
        $params = [];

        $dropboxAppMock = self::$dropboxAppMock;
        $dropboxAppMock->expects($this->once())
        ->method('getClientId')
        ->will(
            $this->returnValue('22XX33')
        );

        // what function does
        // it mostly builds an array and
        // calls method buildUrl() with the built array as 2nd parameter
        $params_f = array_merge([
            'client_id' => '22XX33',
            'response_type' => 'code',
            'state' => $state,
            ], $params);

        $params_f['redirect_uri'] = $redirectUri;

        // mock tested class with method buildUrl(), getApp()
        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['buildUrl', 'getApp'])
        ->getMock();

        $testedClassMock->expects($this->once())
        ->method('buildUrl')
        ->with(
            $this->equalTo('/oauth2/authorize'),
            $this->equalTo($params_f)
        );

        $testedClassMock->expects($this->once())
        ->method('getApp')
        ->will(
            $this->returnValue($dropboxAppMock)
        );

        $class = new \ReflectionClass(self::$testedClass);
        $class->newInstance($dropboxAppMock, self::$dropboxClientMock);

        $method = $class->getMethod('getAuthorizationUrl');
        $method->invoke($testedClassMock, $redirectUri, $state, $params);
    }


    public function testGetAccessToken()
    {
        $clientId = '123';
        $clientSecret = 'xxyyzz';

        $code = 'boum';
        $redirectUri = 'redirect';
        $grant_type = 'authorization_code';


        $params = [
            'code'          => $code,
            'grant_type'    => $grant_type,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri
        ];

        $params = http_build_query($params);

        $apiUrl = 'https://api.dropboxapi.com/oauth2/token';
        $uri = $apiUrl . "?" . $params;


        // mock \Kunnu\Dropbox\DropboxApp
        $dropboxAppMock = self::$dropboxAppMock;

        $dropboxAppMock->expects($this->once())
        ->method('getClientId')
        ->will(
            $this->returnValue($clientId)
        );

        $dropboxAppMock->expects($this->once())
        ->method('getClientSecret')
        ->will(
            $this->returnValue($clientSecret)
        );

        // mock \Kunnu\Dropbox\Http\DropboxRawResponse
        $classPath = '\Kunnu\Dropbox\Http\DropboxRawResponse';
        $dropboxRawResponseMock = $this->createMockHelper($classPath);

        $dropboxRawResponseMock->expects($this->once())
        ->method('getBody')
        ->will(
            $this->returnValue('mybody')
        );

        // mock \Kunnu\Dropbox\Http\Clients\DropboxHttpClientInterface
        $classPath2 = '\Kunnu\Dropbox\Http\Clients\DropboxHttpClientInterface';
        $dropboxHttpClientInterfaceMock = $this->createMockHelper($classPath2);

        $dropboxHttpClientInterfaceMock->expects($this->once())
        ->method('send')
        ->with(
            $this->equalTo($uri),
            $this->equalTo('POST'),
            $this->equalTo(null)
            )
        ->will(
            $this->returnValue($dropboxRawResponseMock)
        );

        // mock \Kunnu\Dropbox\DropboxClient
        $dropboxClientMock = self::$dropboxClientMock;

        $dropboxClientMock->expects($this->once())
        ->method('getHttpClient')
        ->will(
            $this->returnValue($dropboxHttpClientInterfaceMock)
        );

        // mock tested class
        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['getApp', 'getClient'])
        ->getMock();

        $testedClassMock->expects($this->exactly(2))
        ->method('getApp')
        ->will(
            $this->returnValue($dropboxAppMock)
        );

        $testedClassMock->expects($this->once())
        ->method('getClient')
        ->will(
            $this->returnValue($dropboxClientMock)
        );

        $class = new \ReflectionClass(self::$testedClass);
        $class->newInstance($dropboxAppMock, $dropboxClientMock);

        $method = $class->getMethod('getAccessToken');
        $method->invoke($testedClassMock, $code, $redirectUri, $grant_type);
    }

    public function testRevokeAccessToken()
    {
        $token = '1a2b';
        $arr = ['validateResponse' => false];

        // mock \Kunnu\Dropbox\DropboxApp
        $dropboxAppMock = self::$dropboxAppMock;

        $dropboxAppMock->expects($this->once())
        ->method('getAccessToken')
        ->will(
            $this->returnValue($token)
        );

        // mock \Kunnu\Dropbox\DropboxClient
        $dropboxClientMock = self::$dropboxClientMock;

        $dropboxClientMock->expects($this->once())
        ->method('sendRequest')
        ->will(
            $this->returnValue(3)
        );


        // mock tested class
        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['getApp', 'getClient'])
        ->getMock();

        $testedClassMock->expects($this->once())
        ->method('getApp')
        ->will(
            $this->returnValue($dropboxAppMock)
        );

        $testedClassMock->expects($this->once())
        ->method('getClient')
        ->will(
            $this->returnValue($dropboxClientMock)
        );

        $class = new \ReflectionClass(self::$testedClass);
        $class->newInstance($dropboxAppMock, $dropboxClientMock);

        $method = $class->getMethod('revokeAccessToken');
        $method->invoke($testedClassMock);
    }
}
