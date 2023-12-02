<?php

declare(strict_types=1);

namespace Platine\Test\Cookie\Middleware;

use Platine\Cookie\Cookie;
use Platine\Cookie\CookieManager;
use Platine\Cookie\Middleware\CookieSendMiddleware;
use Platine\Http\Response;
use Platine\Http\ServerRequest;
use Platine\Http\Handler\RequestHandler;
use Platine\Dev\PlatineTestCase;

/**
 * CookieSendMiddleware class tests
 *
 * @group core
 * @group cookie
 */
class CookieSendMiddlewareTest extends PlatineTestCase
{
    public function testConstructor(): void
    {
        $cm = $this->getMockBuilder(CookieManager::class)
                ->getMock();

        $m = new CookieSendMiddleware($cm, true);

        $rr = $this->getPrivateProtectedAttribute(CookieSendMiddleware::class, 'cookies');
        $amr = $this->getPrivateProtectedAttribute(CookieSendMiddleware::class, 'removeResponseCookies');

        $this->assertEquals($cm, $rr->getValue($m));
        $this->assertTrue($amr->getValue($m));
    }

    public function testSendRemovePreviousResponseCookies(): void
    {
        $responseMock = new Response();

        $responseMock = $responseMock->withHeader('Set-Cookie', 'name=value');

        $this->assertCount(1, $responseMock->getHeaders('Set-Cookie'));

        $requestHandler = $this->getMockBuilder(RequestHandler::class)
                ->getMock();

        $requestHandler->expects($this->any())
                ->method('handle')
                ->will($this->returnValue($responseMock));

        $request = $this->getMockBuilder(ServerRequest::class)
                ->getMock();

        $r = new Cookie('name', 'value');
        $cm = new CookieManager([$r]);

        $m = new CookieSendMiddleware($cm, true);

        $response = $m->process($request, $requestHandler);
        $this->assertCount(1, $response->getHeaders('Set-Cookie'));
        $this->assertEquals(
            'name=value; Path=/; Secure; HttpOnly; SameSite=Lax',
            $response->getHeaderLine('Set-Cookie')
        );
    }

    public function testSendDontRemovePreviousResponseCookies(): void
    {
        $responseMock = new Response();

        $responseMock = $responseMock->withHeader('Set-Cookie', 'name=value');

        $this->assertCount(1, $responseMock->getHeaders('Set-Cookie'));

        $requestHandler = $this->getMockBuilder(RequestHandler::class)
                ->getMock();

        $requestHandler->expects($this->any())
                ->method('handle')
                ->will($this->returnValue($responseMock));

        $request = $this->getMockBuilder(ServerRequest::class)
                ->getMock();

        $r = new Cookie('name', 'value');
        $cm = new CookieManager([$r]);

        $m = new CookieSendMiddleware($cm, false);

        $response = $m->process($request, $requestHandler);
        $this->assertCount(1, $response->getHeaders('Set-Cookie'));
        $this->assertEquals(
            'name=value, name=value; Path=/; Secure; HttpOnly; SameSite=Lax',
            $response->getHeaderLine('Set-Cookie')
        );
    }
}
