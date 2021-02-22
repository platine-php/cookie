<?php

declare(strict_types=1);

namespace Platine\Test\Cookie;

use Platine\Cookie\Cookie;
use Platine\PlatineTestCase;

/**
 * Cookie class tests
 *
 * @group core
 * @group cookie
 */
class CookieTest extends PlatineTestCase
{

    public function testConstructor(): void
    {
        $c = new Cookie('name', 'value');
        $this->assertEquals('name', $c->getName());
        $this->assertEquals(0, $c->getMaxAge());
        $this->assertEquals(0, $c->getExpires());
        $this->assertFalse($c->isExpired()); //is session can't expired
        $this->assertTrue($c->isSession());
        $this->assertTrue($c->isSession());
        $this->assertTrue($c->isHttpOnly());
        $this->assertNull($c->getDomain());
        $this->assertEquals('/', $c->getPath());
        $this->assertEquals(Cookie::SAME_SITE_LAX, $c->getSameSite());
    }

    public function testWithValue(): void
    {
        $c = new Cookie('name', 'value');

        //Value is the same
        $copy = $c->withValue('value');
        $this->assertEquals($c, $copy);
        $this->assertEquals('value', $copy->getValue());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withValue('value1');
        $this->assertEquals('value1', $copy->getValue());
        $this->assertEquals('name', $copy->getName());
    }

    public function testExpire(): void
    {
        //Cookie already expired
        $c = new Cookie('name', 'value', time() - 86400);
        $this->assertTrue($c->isExpired());
        $copy = $c->expire();
        $this->assertEquals($c, $copy);

        //Cookie not yet expired
        $c = new Cookie('name', 'value', time() + 86400);
        $this->assertFalse($c->isExpired());
        $copy = $c->expire();
        $this->assertTrue($copy->isExpired());
    }

    public function testWithExpires(): void
    {
        $c = new Cookie('name', 'value', 10);

        //Value is the same
        $copy = $c->withExpires(10);
        $this->assertEquals($c, $copy);
        $this->assertEquals(10, $copy->getExpires());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withExpires(20);
        $this->assertEquals(20, $copy->getExpires());
        $this->assertEquals('name', $copy->getName());
    }

    public function testWithDomain(): void
    {
        $c = new Cookie('name', 'value', null, 'domain');

        //Value is the same
        $copy = $c->withDomain('domain');
        $this->assertEquals($c, $copy);
        $this->assertEquals('domain', $copy->getDomain());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withDomain('domain1');
        $this->assertEquals('domain1', $copy->getDomain());
        $this->assertEquals('name', $copy->getName());
    }

    public function testWithPath(): void
    {
        $c = new Cookie('name', 'value', null, 'domain', 'path');

        //Value is the same
        $copy = $c->withPath('path');
        $this->assertEquals($c, $copy);
        $this->assertEquals('path', $copy->getPath());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withPath('path1');
        $this->assertEquals('path1', $copy->getPath());
        $this->assertEquals('name', $copy->getName());
    }

    public function testWithSecure(): void
    {
        $c = new Cookie('name', 'value', null, 'domain', 'path');

        //Value is the same
        $copy = $c->withSecure(true);
        $this->assertEquals($c, $copy);
        $this->assertEquals(true, $copy->isSecure());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withSecure(false);
        $this->assertEquals(false, $copy->isSecure());
        $this->assertEquals('name', $copy->getName());
    }

    public function testWithHttpOnly(): void
    {
        $c = new Cookie('name', 'value', null, 'domain', 'path');

        //Value is the same
        $copy = $c->withHttpOnly(true);
        $this->assertEquals($c, $copy);
        $this->assertEquals(true, $copy->isHttpOnly());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withHttpOnly(false);
        $this->assertEquals(false, $copy->isHttpOnly());
        $this->assertEquals('name', $copy->getName());
    }

    public function testWithSameSite(): void
    {
        $c = new Cookie('name', 'value', null, 'domain', 'path');

        //Value is the same
        $copy = $c->withSameSite(Cookie::SAME_SITE_LAX);
        $this->assertEquals($c, $copy);
        $this->assertEquals(Cookie::SAME_SITE_LAX, $copy->getSameSite());
        $this->assertEquals('name', $copy->getName());

        //Value is not the same
        $copy = $c->withSameSite(Cookie::SAME_SITE_STRICT);
        $this->assertEquals(Cookie::SAME_SITE_STRICT, $copy->getSameSite());
        $this->assertEquals('name', $copy->getName());
    }

    public function testToString(): void
    {
        $c = new Cookie('name', 'value');

        $this->assertEquals('name=value; Path=/; Secure; HttpOnly; SameSite=Lax', $c->__toString());

        $expire = '2020-07-01';
        $c = new Cookie('name', 'value', $expire, 'domain');

        $this->assertEquals(
            'name=value; '
                . 'Expires=Wed, 01-Jul-2020 00:00:00 GMT; '
                . 'Max-Age=0; Domain=domain; Path=/; Secure; '
                . 'HttpOnly; SameSite=Lax',
            $c->__toString()
        );
    }

    public function testCookieNameIsEmptyOrNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new Cookie('', 'value');
    }

    public function testCookieNameInvalidCaracter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new Cookie('foo@bar', 'value');
    }

    public function testCookieExpiresInvalidParam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new Cookie('foobar', 'value', new \stdClass());
    }

    public function testCookieExpiresInvalidDateString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new Cookie('foobar', 'value', 'not_good');
    }

    public function testCookieExpiresUsingDatetime(): void
    {
        $c = new Cookie('foobar', 'value', new \DateTime('2000-09-09'));
        $this->assertTrue($c->isExpired());
    }

    public function testCookieSameSiteInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $c = new Cookie('foobar', 'value', null, null, '/', true, true, 'foo_same_site');
    }
}
