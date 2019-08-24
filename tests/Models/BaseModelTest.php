<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/ProfileProvider.php';

class BaseModel extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass;

    /** @var array $data */
    protected static $data;

    protected function setUp()
    {
        self::$testedClass = 'Kunnu\Dropbox\Models\BaseModel';

        $class = new Test\Models\TestHelpers\ProfileProvider;

        self::$data = $class->getProfile();
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($instance,  self::$data);

        $property = $reflectedClass->getProperty('data');
        $property->setAccessible(true);
        $data = $property->getValue($instance);

        $this->assertSame($data, self::$data);
    }

    public function testGetData()
    {
        $class = new self::$testedClass(self::$data);

        $this->assertSame($class->getData(), self::$data);
    }

    public function testGetDataProperty()
    {
        $class = new self::$testedClass(self::$data);
        $this->assertSame($class->getDataProperty('account_id'), self::$data['account_id']);

        // property that does not exist
        $this->assertNull($class->getDataProperty('account_iddd'));
    }

    public function testMagicMethods()
    {
        $getProperty = 'account_id';
        $getValue = '123';

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$data]);

        $property = $reflectedClass->getProperty('data');
        $property->setAccessible(true);
        $data = $property->getValue($instance);

        // test get
        // existent property
        // will return what array $data[property] contain
        $this->assertEquals($instance->$getProperty, $data[$getProperty]);

        // nonExistent property
        // will return null
        $this->assertNull($instance->t);

        // test set
        // this will create a new key in array of $data
        $instance->account_expected = $getValue;

        // we reload $data var
        $data = $property->getValue($instance);

        $this->assertEquals($instance->account_expected, $data['account_expected']);
    }
}
