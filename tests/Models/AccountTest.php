<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/ProfileProvider.php';

class AccountTest extends TestCase
{
    /** @var string $testedClass */
    protected static $testedClass;

    /** @var array $data */
    protected static $data;

    /** @var Kunnu\Dropbox\Models\Account $testedClassLoaded mock */
    protected static $testedClassLoaded;

    protected function setUp()
    {
        $data = new Test\Models\TestHelpers\ProfileProvider();

        $profile = $data->getProfile();

        self::$testedClass = 'Kunnu\Dropbox\Models\Account';

        self::$data = $profile;

        self::$testedClassLoaded = new self::$testedClass(self::$data);
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);

        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        // instantiate class with $data array
        $cst = $constructor->invoke($instance, self::$data);

        $this->assertNull($cst);
    }

    public function testGetAccountId()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getAccountId(), self::$data['account_id']);
    }

    public function testGetNameDetails()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getNameDetails(), self::$data['name']);
    }

    public function testGetDisplayName()
    {
        // display_name exists
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getDisplayName(), self::$data['name']['display_name']);

        // display_name does not exist
        unset(self::$data['name']['display_name']);

        $class = new self::$testedClass(self::$data);

        $this->assertSame($class->getDisplayName(), '');
    }

    public function testGetEmail()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getEmail(), self::$data['email']);
    }

    public function testEmailIsVerified()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->emailIsVerified(), self::$data['email_verified']);
    }

    public function testGetProfilePhotoUrl()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getProfilePhotoUrl(), self::$data['profile_photo_url']);
    }

    public function testIsDisabled()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->isDisabled(), self::$data['disabled']);
    }

    public function testGetCountry()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getCountry(), self::$data['country']);
    }

    public function testGetLocale()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getLocale(), self::$data['locale']);
    }

    public function testGetReferralLink()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getReferralLink(), self::$data['referral_link']);
    }

    public function testisPaired()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->isPaired(), self::$data['is_paired']);
    }

    public function testGetAccountType()
    {
        $class = self::$testedClassLoaded;

        $this->assertSame($class->getAccountType(), self::$data['account_type']['.tag']);
    }
}
