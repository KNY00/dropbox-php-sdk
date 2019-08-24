<?php
use PHPUnit\Framework\TestCase;

/**
 * coversDefaultClass Kunnu\Dropbox\Authentication\DropboxAuthHelper
 */
class DropboxAuthHelperTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass = '\Kunnu\Dropbox\Authentication\DropboxAuthHelper';

    /** @var \Kunnu\Dropbox\Authentication\OAuth2Client $oAuth2ClientMock */
    protected static $oAuth2ClientMock;

    /** @var \Kunnu\Dropbox\Security\RandomStringGeneratorInterface $oAuth2ClientMock */
    protected static $randomStringGeneratorInterfaceMock;

    /** @var \Kunnu\Dropbox\Store\PersistentDataStoreInterface $persistentDataStoreInterfacMock */
    protected static $persistentDataStoreInterfaceMock;

    /** @var \Kunnu\Dropbox\Authentication\DropboxAuthHelper $testedClassMock */
    protected static $testedClassMock;

    protected function setUp()
    {
        self::$testedClass = '\Kunnu\Dropbox\Authentication\DropboxAuthHelper';

        // mock OAuth2Client first argument of constructor's tested Class
        self::$oAuth2ClientMock = $this->createMockHelper('\Kunnu\Dropbox\Authentication\OAuth2Client');

        // mock randomStringGeneratorInterface second argument of constructor's tested Class
        self::$randomStringGeneratorInterfaceMock = $this->createMockHelper('\Kunnu\Dropbox\Security\RandomStringGeneratorInterface');

        // mock persistentDataStoreInterface third argument of constructor's tested Class
        self::$persistentDataStoreInterfaceMock = $this->createMockHelper('\Kunnu\Dropbox\Store\PersistentDataStoreInterface');

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
     * @param \Kunnu\Dropbox\Authentication\OAuth2Client $oAuth2Client mock
     * @param \Kunnu\Dropbox\Security\RandomStringGeneratorInterface $randomStringGenerator mock
     * @param \Kunnu\Dropbox\Store\PersistentDataStoreInterface $persistentDataStore mock
     *
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     *
     */
    private function reflectConstructor($oAuth2Client, $randomStringGenerator = null, $persistentDataStore = null)
    {
        $reflectedClass = new \ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        return $constructor->invoke($instance, $oAuth2Client, $randomStringGenerator, $persistentDataStore);
    }

    public function testConstructorCallsInternalMethods()
    {
        $this->assertNull($this->reflectConstructor(self::$oAuth2ClientMock));
    }

    public function testGetOAuth2Client()
    {
        $class = new self::$testedClass(self::$oAuth2ClientMock);

        $this->assertSame($class->getOAuth2Client(), self::$oAuth2ClientMock);
    }

    public function testGetRandomStringGenerator()
    {
        $class = new self::$testedClass(self::$oAuth2ClientMock, self::$randomStringGeneratorInterfaceMock);

        $this->assertSame($class->getRandomStringGenerator(), self::$randomStringGeneratorInterfaceMock);
    }

    public function testGetPersistentDataStore()
    {
        $class = new self::$testedClass(self::$oAuth2ClientMock, null, self::$persistentDataStoreInterfaceMock);

        $this->assertSame($class->getPersistentDataStore(), self::$persistentDataStoreInterfaceMock);
    }

    public function testGetCsrfToken()
    {
        $randomStringGeneratorInterface = self::$randomStringGeneratorInterfaceMock;
        // expects call of generateString() with 32 as argument
        $randomStringGeneratorInterface->expects($this->once())
                                       ->method('generateString')
                                       ->with(
                                           $this->equalTo(32)
                                       );

        // Reflect protected Method getCsrfToken()
        $class = new ReflectionClass(self::$testedClass);
        $instance = $class->newInstanceArgs([self::$oAuth2ClientMock, self::$randomStringGeneratorInterfaceMock]);

        $method = $class->getMethod('getCsrfToken');
        $method->setAccessible(true);
        $method->invoke($instance);
    }

    public function testGetAuthUrl()
    {
        $redirectUri = 'x';
        $params = ['param1','param2'];
        $urlState = 'z';
        $state = 'Colorado';

        // set 3 mocks,
        // these will be used as arguments for constructor of reflected class
        $oAuth2Client = self::$oAuth2ClientMock;
        $randomStringGeneratorInterface = self::$randomStringGeneratorInterfaceMock;
        $persistentDataStoreInterface = self::$persistentDataStoreInterfaceMock;

        // expected call of method getAuthorizationUrl()
        $oAuth2Client->expects($this->once())
        ->method('getAuthorizationUrl')
        ->with(
            $this->equalTo($redirectUri),
            $this->equalTo($state . '|' . $urlState),
            $this->equalTo($params)
        )
        ->will($this->returnValue(123));

        // expected call of method set()
        $persistentDataStoreInterface->expects($this->once())
        ->method('set')
        ->will(
            $this->returnValue($state)
        );

        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['getCsrfToken', 'getPersistentDataStore', 'getOAuth2Client'])
        ->getMock();

        $testedClassMock->expects($this->once())
        ->method('getCsrfToken')
        ->will(
            $this->returnValue($state)
        );

        $testedClassMock->expects($this->once())
        ->method('getPersistentDataStore')
        ->will(
            $this->returnValue($persistentDataStoreInterface)
        );

        $testedClassMock->expects($this->once())
        ->method('getOAuth2Client')
        ->will(
            $this->returnValue($oAuth2Client)
        );

        $class = new ReflectionClass(self::$testedClass);
        $class->newInstance($oAuth2Client, $randomStringGeneratorInterface, $persistentDataStoreInterface);

        $method = $class->getMethod('getAuthUrl');
        $method->invoke($testedClassMock, $redirectUri , $params, $urlState);
    }

    public function testDecodeState()
    {
        // set 3 mocks,
        // these will be used as arguments for constructor of reflected class
        $oAuth2Client = self::$oAuth2ClientMock;
        $randomStringGeneratorInterface = self::$randomStringGeneratorInterfaceMock;
        $persistentDataStoreInterface = self::$persistentDataStoreInterfaceMock;

        // protected method
        $class = new ReflectionClass(self::$testedClass);
        $instance = $class->newInstanceArgs([$oAuth2Client, $randomStringGeneratorInterface, $persistentDataStoreInterface]);

        $method = $class->getMethod('decodeState');
        $method->setAccessible(true);

        $state = '1234|abcd';
        $predicatedReturn = [
            'csrfToken' => '1234',
            'urlState' => 'abcd'
        ];

        $this->assertEquals($method->invoke($instance, $state), $predicatedReturn);
    }

    public function testValidateCSRFToken()
    {
        $csrfToken = '1a2b3c4d';

        // set 3 mocks,
        // these will be used as arguments for constructor of reflected class
        $oAuth2Client = self::$oAuth2ClientMock;
        $randomStringGeneratorInterface = self::$randomStringGeneratorInterfaceMock;
        $persistentDataStoreInterface = self::$persistentDataStoreInterfaceMock;

        // get() will be called twice
        $persistentDataStoreInterface->expects($this->exactly(2))
        ->method('get')
        ->with(
            $this->equalTo('state')
        )
        ->will(
            $this->returnValue($csrfToken)
        );

        // clear() will be called only once
        // second call ends up with throw
        $persistentDataStoreInterface->expects($this->once())
        ->method('clear')
        ->with(
            $this->equalTo('state')
        );

        $class = new ReflectionClass(self::$testedClass);
        $instance = $class->newInstanceArgs([$oAuth2Client, $randomStringGeneratorInterface, $persistentDataStoreInterface]);

        // reflect protected method validateCSRFToken
        $method = $class->getMethod('validateCSRFToken');
        $method->setAccessible(true);

        // call it for the first time
        // everything is fine because :
        // $csrfToken = $tokenInStore
        // $tokenInStore is not false || !$csrfToken is not false too
        $method->invoke($instance, $csrfToken);

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);
        $this->expectExceptionMessage('Invalid CSRF Token. CSRF Token Mismatch.');

        // call for the second time
        // @thrown \Kunnu\Dropbox\Exceptions\DropboxClientException
        // $csrfToken !== $tokenInStore
        $method->invoke($instance, $csrfToken.'3');
    }

    public function testValidateCSRFTokenB()
    {
        $csrfToken = false;

        // set 3 mocks,
        // these will be used as arguments for constructor of reflected class
        $oAuth2Client = self::$oAuth2ClientMock;
        $randomStringGeneratorInterface = self::$randomStringGeneratorInterfaceMock;
        $persistentDataStoreInterface = self::$persistentDataStoreInterfaceMock;

        // method returns false
        $persistentDataStoreInterface->expects($this->once())
        ->method('get')
        ->with(
            $this->equalTo('state')
        )
        ->will(
            $this->returnValue(false)
        );

        // method clear() won't be called this time because
        // throw will stop function's execution

        // reflect protected method
        $class = new ReflectionClass(self::$testedClass);
        $instance = $class->newInstanceArgs([$oAuth2Client, $randomStringGeneratorInterface, $persistentDataStoreInterface]);

        $method = $class->getMethod('validateCSRFToken');
        $method->setAccessible(true);

        $this->expectException(\Kunnu\Dropbox\Exceptions\DropboxClientException::class);
        $this->expectExceptionMessage('Invalid CSRF Token. Unable to validate CSRF Token.');

        $method->invoke($instance, $csrfToken);
    }

    public function testGetAccessToken()
    {
        $state = '1234|abcd';
        $csrfToken = '1234';
        $urlState = 'abcd';

        $code = 'tintin';
        $redirectUri = 'xyx';

        $stateReturned = [
            'csrfToken' => $csrfToken,
            'urlState'  => $urlState
        ];

        // set 3 mocks,
        // these will be used as arguments for constructor of reflected class
        $oAuth2Client = self::$oAuth2ClientMock;
        $randomStringGeneratorInterface = self::$randomStringGeneratorInterfaceMock;
        $persistentDataStoreInterface = self::$persistentDataStoreInterfaceMock;

        $oAuth2Client->expects($this->once())
        ->method('getAccessToken')
        ->with(
            $this->equalTo($code),
            $this->equalTo($redirectUri)
        )
        ->will($this->returnValue([]));

        $testedClassMock = $this->getMockBuilder(self::$testedClass)
        ->disableOriginalConstructor()
        ->setMethods(['decodeState', 'validateCSRFToken', 'getOAuth2Client'])
        ->getMock();

        $testedClassMock->expects($this->once())
        ->method('decodeState')
        ->with(
            $this->equalTo($state)
        )
        ->will(
            $this->returnValue($stateReturned)
        );

        $testedClassMock->expects($this->once())
        ->method('validateCSRFToken')
        ->with(
            $this->equalTo($csrfToken)
        );

        $testedClassMock->expects($this->once())
        ->method('getOAuth2Client')
        ->will(
            $this->returnValue($oAuth2Client)
        );


        $class = new ReflectionClass(self::$testedClass);
        $class->newInstance($oAuth2Client, $randomStringGeneratorInterface, $persistentDataStoreInterface);

        $method = $class->getMethod('getAccessToken');
        $method->invoke($testedClassMock, $code, $state, $redirectUri);
    }

    public function testRevokeAccessToken()
    {
        $class = new ReflectionClass(self::$testedClass);

        self::$oAuth2ClientMock->expects($this->once())
        ->method('revokeAccessToken');

        self::$testedClassMock->expects($this->once())
        ->method('getOAuth2Client')
        ->will(
            $this->returnValue(self::$oAuth2ClientMock)
        );

        $instance = $class->newInstanceArgs([self::$oAuth2ClientMock, self::$randomStringGeneratorInterfaceMock, self::$persistentDataStoreInterfaceMock]);

        $method = $class->getMethod('revokeAccessToken');
        $method->invoke(self::$testedClassMock);
    }

    public function testGetUrlState()
    {
        $class = new self::$testedClass(self::$oAuth2ClientMock, self::$randomStringGeneratorInterfaceMock, self::$persistentDataStoreInterfaceMock);

        $this->assertNull($class->getUrlState());
    }
}
