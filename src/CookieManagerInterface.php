<?php

/**
 * Platine Cookie
 *
 * Platine Cookie is the cookie management in accordance with the RFC 6265 specification
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2020 Platine Cookie
 * Copyright (c) 2020 Evgeniy Zyubin
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
 *  @file CookieManagerInterface.php
 *
 *  The CookieManagerInterface interface
 *
 *  @package    Platine\Cookie
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   http://www.iacademy.cf
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Cookie;

use Platine\Http\ResponseInterface;

interface CookieManagerInterface
{

    /**
     * Set the cookie
     *
     * @param CookieInterface $cookie the cookie instance
     * @return void
     */
    public function add(CookieInterface $cookie): void;

    /**
     * Return the cookie for the given value
     * @return CookieInterface|null
     */
    public function get(string $name): ?CookieInterface;

    /**
     * Return all the cookies
     *
     * @return array<CookieInterface> the list of CookieInterface
     */
    public function all(): array;

    /**
     * Return the value of the named cookie.
     *
     * @param string $name the name of the cookie
     * @return string|null the value of the named cookie or `null` if the cookie does not exist.
     */
    public function getValue(string $name): ?string;

    /**
     * Whether a cookie with the specified name exists.
     *
     * @param string $name the name of the cookie
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Remove a cookie from the list.
     *
     * @param string $name the name of the cookie
     *
     * @return CookieInterface|null
     */
    public function remove(string $name): ?CookieInterface;

    /**
     * Remove all cookies.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Set all cookie to the response and return a clone instance of the response with the cookies set.
     *
     * This method must be called before emitting the response.
     *
     * @param  ResponseInterface $response
     * @param  bool      $removeResponseCookies whether to remove previously set cookies from the response
     * @return ResponseInterface
     */
    public function send(
        ResponseInterface $response,
        bool $removeResponseCookies = true
    ): ResponseInterface;
}
