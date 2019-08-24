<?php
use PHPUnit\Framework\TestCase;

class PersistentDataStoreFactoryTest extends TestCase
{
    public function testMakePersistentDataStore($store = null)
    {
        $class = new Kunnu\Dropbox\Store\PersistentDataStoreFactory;

        $SessionPersistentDataStore = new Kunnu\Dropbox\Store\SessionPersistentDataStore;

        $this->assertEquals($class->makePersistentDataStore(null), $SessionPersistentDataStore);

        $this->assertEquals($class->makePersistentDataStore('session'), $SessionPersistentDataStore);

        $this->assertSame($class->makePersistentDataStore($SessionPersistentDataStore), $SessionPersistentDataStore);

        $classInjected = new stdClass;

        $this->expectException(InvalidArgumentException::class);

        $class->makePersistentDataStore($classInjected);
    }
}
