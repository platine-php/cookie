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
 *  @file CookieManager.php
 *
 *  The CookieManager class is used to manage the cookies
 *
 *  @package    Platine\Cookie
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Cookie;

use InvalidArgumentException;
use Platine\Http\ResponseInterface;

class CookieManager implements CookieManagerInterface
{
    /**
     * The list of CookieInterface
     * @var array<CookieInterface>
     */
    protected array $cookies = [];

    /**
     * Create new instance
     * @param array<CookieInterface> $cookies the default cookies to store
     */
    public function __construct(array $cookies = [])
    {
        foreach ($cookies as $cookie) {
            if (!$cookie instanceof CookieInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Each cookie must implement interface [%s]',
                    CookieInterface::class
                ));
            }

            $this->cookies[$cookie->getName()] = $cookie;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(CookieInterface $cookie): void
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): ?CookieInterface
    {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(string $name): ?string
    {
        return isset($this->cookies[$name]) ? $this->cookies[$name]->getValue() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): ?CookieInterface
    {
        if (!isset($this->cookies[$name])) {
            return null;
        }

        $removed = $this->cookies[$name];

        unset($this->cookies[$name]);

        return $removed;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->cookies = [];
    }

    /**
     * {@inheritdoc}
     */
    public function send(
        ResponseInterface $response,
        bool $removeResponseCookies = true
    ): ResponseInterface {
        if ($removeResponseCookies) {
            $response = $response->withoutHeader('Set-Cookie');
        }

        foreach ($this->cookies as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', (string) $cookie);
        }

        return $response;
    }
}
