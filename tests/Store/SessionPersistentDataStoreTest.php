<?php
session_start();

use PHPUnit\Framework\TestCase;

class PersistentDataStoreTest extends TestCase
{
    /** @var string $prefix */
    public static $prefix = 'TEST_';

    /** @var string $prefix */
    public static $key = '123';

    /** @var string $prefix */
    public static $value = 'loprem';

    protected function setUp()
    {
        self::$prefix = true;
        self::$key = '123';
        self::$value = 'loprem';
    }

    protected function tearDown()
    {
        // Deletes session after every test
        if (isset($_SESSION[self::$prefix . self::$key])) {
            unset($_SESSION[self::$prefix . self::$key]);
        }
    }

    public function testGet()
    {
        $class = new Kunnu\Dropbox\Store\SessionPersistentDataStore(self::$prefix);

        $this->assertNull($class->get(self::$key));

        $_SESSION[self::$prefix . self::$key] = self::$value;

        $this->assertSame( $class->get(self::$key), $_SESSION[self::$prefix . self::$key]);
    }

    public function testSet()
    {
        $class = new Kunnu\Dropbox\Store\SessionPersistentDataStore(self::$prefix);

        $class->set(self::$key, self::$value);

        $this->assertEquals($_SESSION[self::$prefix . self::$key], self::$value);
    }

    public function testClear()
    {
        $class = new Kunnu\Dropbox\Store\SessionPersistentDataStore(self::$prefix);

        $_SESSION[self::$prefix . self::$key] = self::$value;

        $class->clear(self::$key);

        $this->assertFalse(isset($_SESSION[self::$prefix . self::$key]));
    }
}
