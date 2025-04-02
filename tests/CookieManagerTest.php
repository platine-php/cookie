<?php

declare(strict_types=1);

namespace Platine\Test\Cookie;

use Platine\Cookie\Cookie;
use Platine\Cookie\CookieManager;
use Platine\Dev\PlatineTestCase;
use Platine\Http\Response;

/**
 * CookieManager class tests
 *
 * @group core
 * @group cookie
 */
class CookieManagerTest extends PlatineTestCase
{
    public function testConstructor(): void
    {
        //Default
        $c = new CookieManager();
        $this->assertEmpty($c->all());
        $this->assertCount(0, $c->all());

        $o = new CookieManager([new Cookie('name', 'value')]);

        $this->assertCount(1, $o->all());
    }


    public function testAdd(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();
        $this->assertEmpty($c->all());

        $c->add($r);

        $this->assertCount(1, $c->all());
    }

    public function testGet(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();

        $c->add($r);

        $this->assertEquals($r, $c->get('name'));

        //Cookie Not found exists
        $this->assertNull($c->get('not found Cookie'));
    }

    public function testGetValue(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();

        $c->add($r);

        $this->assertEquals('value', $c->getValue('name'));

        //Cookie Not found exists
        $this->assertNull($c->getValue('not found Cookie'));
    }

    public function testHas(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();

        $c->add($r);

        $this->assertTrue($c->has('name'));
        $this->assertFalse($c->has('not found Cookie'));
    }

    public function testRemove(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();
        $this->assertEmpty($c->all());

        $c->add($r);

        $this->assertCount(1, $c->all());

        $this->assertEquals($r, $c->remove('name'));

        $this->assertCount(0, $c->all());

        //Cookie Not found exists
        $this->assertNull($c->remove('not found Cookie'));
    }

    public function testClear(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();
        $this->assertEmpty($c->all());

        $c->add($r);

        $this->assertCount(1, $c->all());

        $c->clear();

        $this->assertCount(0, $c->all());
    }

    public function testSendRemovePreviousResponseCookies(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();

        $c->add($r);

        $response = new Response();

        $response = $response->withHeader('Set-Cookie', 'name=value');

        $this->assertCount(1, $response->getHeaders('Set-Cookie'));

        $response = $c->send($response, true);
        $this->assertCount(1, $response->getHeaders('Set-Cookie'));
        $this->assertEquals(
            'name=value; Path=/; Secure; HttpOnly; SameSite=Lax',
            $response->getHeaderLine('Set-Cookie')
        );
    }

    public function testSendNotPreviousResponseCookies(): void
    {
        $r = new Cookie('name', 'value');
        $c = new CookieManager();

        $c->add($r);

        $response = new Response();

        $response = $response->withHeader('Set-Cookie', 'name=value');

        $this->assertCount(1, $response->getHeaders('Set-Cookie'));

        $response = $c->send($response, false);
        $this->assertCount(1, $response->getHeaders('Set-Cookie'));
        $this->assertEquals(
            'name=value, name=value; Path=/; Secure; HttpOnly; SameSite=Lax',
            $response->getHeaderLine('Set-Cookie')
        );
    }
}
