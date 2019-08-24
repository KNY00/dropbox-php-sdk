<?php
use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{
    /** @var string $testedClass */
    public static $testedClass = 'Kunnu\Dropbox\Models\AccessToken';

    /**
     * TODO: test and remove bearer property
     *
     */
    public function testConstructorCallsInternalMethods()
    {
        $data = [
            'access_token' => 'ABCDEFG',
            'token_type' => 'bearer',
            'account_id' => 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc',
            'uid' => '12345',
            'bearer' => 'three',
            'team_id' => 'six',
        ];

        $reflectedClass = new ReflectionClass(self::$testedClass);
        $instance = $reflectedClass->newInstanceWithoutConstructor();

        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($instance,  $data);

        $getToken     = $reflectedClass->getMethod('getToken');
        $getTokenType = $reflectedClass->getMethod('getTokenType');
        $getBearer    = $reflectedClass->getMethod('getBearer');
        $getUid       = $reflectedClass->getMethod('getUid');
        $getAccountId = $reflectedClass->getMethod('getAccountId');
        $getTeamId    = $reflectedClass->getMethod('getTeamId');

        // testing output of methods
        $this->assertSame($getToken->invoke($instance), 'ABCDEFG');
        $this->assertSame($getTokenType->invoke($instance), 'bearer');
        $this->assertSame($getBearer->invoke($instance), 'three');
        $this->assertSame($getUid->invoke($instance), '12345');
        $this->assertSame($getAccountId->invoke($instance), 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc');
        $this->assertSame($getTeamId->invoke($instance), 'six');
    }
}
