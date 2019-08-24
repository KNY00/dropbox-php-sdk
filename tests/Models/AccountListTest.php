<?php
use PHPUnit\Framework\TestCase;

require_once 'Test_Helpers/ProfileProvider.php';

class AccountListTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass;

    /** @var array $accountsList */
    protected static $accountsList;

    protected function setUp()
    {
        $data = new Test\Models\TestHelpers\ProfileProvider();

        $getProfilesList = $data->getProfilesList();

        self::$testedClass = 'Kunnu\Dropbox\Models\AccountList';

        self::$accountsList = $getProfilesList;
    }

    public function testConstructorCallsInternalMethods()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();

        $this->assertNull($constructor->invoke($instance,  self::$accountsList));
    }

    public function testProcessItems()
    {
        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceArgs([self::$accountsList]);

        $processItems = $reflectedClass->getMethod('processItems');
        $processItems->setAccessible(true);

        $returnProcessItems = $processItems->invoke($instance, self::$accountsList);

        $processedItems = [];

        foreach (self::$accountsList as $entry) {
            $processedItems[] = new Kunnu\Dropbox\Models\Account($entry);
        }

        $this->assertEquals($returnProcessItems, $processedItems);

        foreach ($returnProcessItems as $entry) {
            $this->assertInstanceOf('Kunnu\Dropbox\Models\Account', $entry);
        }
    }
}
