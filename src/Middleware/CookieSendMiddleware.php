<?php

/**
 * Platine Cookie
 *
 * Platine Cookie is the cookie management in accordance with the RFC 6265 specification
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2020 Platine Cookie
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 *  @file CookieSendMiddleware.php
 *
 *  The CookieSendMiddleware class is used to match the request
 *
 *  @package    Platine\Cookie\Middleware
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   http://www.iacademy.cf
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Cookie\Middleware;

use Platine\Cookie\CookieManagerInterface;
use Platine\Http\ResponseInterface;
use Platine\Http\ServerRequestInterface;
use Platine\Http\Handler\RequestHandlerInterface;
use Platine\Http\Handler\Middleware\MiddlewareInterface;

class CookieSendMiddleware implements MiddlewareInterface
{

    /**
     * The cookie manager instance
     * @var CookieManagerInterface
     */
    protected CookieManagerInterface $cookies;

    /**
     * Whether to remove previously set cookies from the response.
     * @var bool
     */
    protected bool $removeResponseCookies;

    /**
     * Create new instance
     * @param CookieManagerInterface $cookies
     * @param bool           $removeResponseCookies
     */
    public function __construct(
        CookieManagerInterface $cookies,
        bool $removeResponseCookies = true
    ) {
        $this->cookies = $cookies;
        $this->removeResponseCookies = $removeResponseCookies;
    }

    /**
     * {@inheritdoc}
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        return $this->cookies->send($response, $this->removeResponseCookies);
    }
}
