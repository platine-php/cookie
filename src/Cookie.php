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
 *  @file Cookie.php
 *
 *  The Cookie class
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

class Cookie implements CookieInterface
{

    /**
     * The name of the cookie
     *
     * @var string
     */
    protected string $name;

    /**
     * The value of the cookie
     *
     * @var string
     */
    protected string $value;

    /**
     * The expires of the cookie
     *
     * @var int
     */
    protected int $expires;

    /**
     * The domain of the cookie
     *
     * @var string|null
     */
    protected ?string $domain;

    /**
     * The path of the cookie
     *
     * @var string|null
     */
    protected ?string $path;

    /**
     * Whether the cookie is transmetted under secure
     * connection
     *
     * @var bool|null
     */
    protected ?bool $secure;

    /**
     * Whether the cookie is accessed under HTTP
     * protocol
     *
     * @var bool|null
     */
    protected ?bool $httpOnly;

    /**
     * The value of the same site
     *
     * @var string|null
     */
    protected ?string $sameSite;

    /**
     * Create new instance
     *
     * @param string $name
     * @param string $value
     * @param int|string|\DateTimeInterface|null $expire
     * @param string |null $domain
     * @param string |null $path
     * @param bool|null $secure
     * @param bool|null $httpOnly
     * @param string|null $sameSite
     */
    public function __construct(
        string $name,
        string $value = '',
        $expire = null,
        ?string $domain = null,
        ?string $path = '/',
        ?bool $secure = true,
        ?bool $httpOnly = true,
        ?string $sameSite = self::SAME_SITE_LAX
    ) {
        $this->setName($name);
        $this->setValue($value);
        $this->setExpires($expire);
        $this->setDomain($domain);
        $this->setPath($path);
        $this->setSecure($secure);
        $this->setHttpOnly($httpOnly);
        $this->setSameSite($sameSite);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function withValue(string $value): self
    {
        if ($value === $this->value) {
            return $this;
        }

        $that = clone $this;

        $that->setValue($value);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxAge(): int
    {
        $maxAge = $this->expires - time();

        return $maxAge > 0 ? $maxAge : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired(): bool
    {
        return (!$this->isSession() && $this->expires < time());
    }

    /**
     * {@inheritdoc}
     */
    public function expire(): self
    {
        if ($this->isExpired()) {
            return $this;
        }

        $that = clone $this;

        $that->expires = time() - 31536001;

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function withExpires($expire = null): self
    {
        if ($expire === $this->expires) {
            return $this;
        }

        $that = clone $this;

        $that->setExpires($expire);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function withDomain(?string $domain): self
    {
        if ($domain === $this->domain) {
            return $this;
        }

        $that = clone $this;

        $that->setDomain($domain);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath(?string $path): self
    {
        if ($path === $this->path) {
            return $this;
        }

        $that = clone $this;

        $that->setPath($path);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure(): bool
    {
        return $this->secure ? $this->secure : false;
    }

    /**
     * {@inheritdoc}
     */
    public function withSecure(bool $secure = true): self
    {
        if ($secure === $this->secure) {
            return $this;
        }

        $that = clone $this;

        $that->setSecure($secure);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly ? $this->httpOnly : false;
    }

    /**
     * {@inheritdoc}
     */
    public function withHttpOnly(bool $httpOnly = true): self
    {
        if ($httpOnly === $this->httpOnly) {
            return $this;
        }

        $that = clone $this;

        $that->setHttpOnly($httpOnly);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function getSameSite(): ?string
    {
        return $this->sameSite;
    }

    /**
     * {@inheritdoc}
     */
    public function withSameSite(?string $sameSite): self
    {
        if ($sameSite === $this->sameSite) {
            return $this;
        }

        $that = clone $this;

        $that->setSameSite($sameSite);

        return $that;
    }

    /**
     * {@inheritdoc}
     */
    public function isSession(): bool
    {
        return $this->expires === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $cookie = $this->name . '=' . rawurlencode($this->value);

        if (!$this->isSession()) {
            $cookie .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', $this->expires);
            $cookie .= '; Max-Age=' . $this->getMaxAge();
        }

        if ($this->domain !== null) {
            $cookie .= '; Domain=' . $this->domain;
        }

        if ($this->path !== null) {
            $cookie .= '; Path=' . $this->path;
        }

        if ($this->secure === true) {
            $cookie .= '; Secure';
        }

        if ($this->httpOnly === true) {
            $cookie .= '; HttpOnly';
        }

        if ($this->sameSite !== null) {
            $cookie .= '; SameSite=' . $this->sameSite;
        }

        return $cookie;
    }

    /**
     * Set the cookie name
     * @param string $name
     */
    private function setName(string $name): self
    {
        if (empty($name)) {
            throw new \InvalidArgumentException(sprintf(
                'The cookie name [%s] could not be empty',
                $name
            ));
        }

        if (!preg_match('/^[a-zA-Z0-9!#$%&\' *+\-.^_`|~]+$/', $name)) {
            throw new \InvalidArgumentException(sprintf(
                'The cookie name [%s] contains invalid characters; must contain any US-ASCII'
                . ' characters, except control and separator characters, spaces, or tabs.',
                $name
            ));
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set the cookie value
     * @param string $value
     */
    private function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the cookie expires
     * @param mixed $expire
     */
    private function setExpires($expire): self
    {
        if (
            $expire !== null
            && !is_int($expire)
            && !is_string($expire)
            && !$expire instanceof \DateTimeInterface
        ) {
            throw new \InvalidArgumentException(sprintf(
                'The cookie expire time is not valid; must be null, or string,'
                . ' or integer, or DateTimeInterface instance; received [%s]',
                is_object($expire) ? get_class($expire) : gettype($expire)
            ));
        }

        if (empty($expire)) {
            $expire = 0;
        }

        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $strExpire = $expire;
            $expire = strtotime($strExpire);

            if ($expire === false) {
                throw new \InvalidArgumentException(sprintf(
                    'The string representation of the cookie expire time [%s] is not valid',
                    $strExpire
                ));
            }
        }

        $this->expires = ($expire > 0) ? (int) $expire : 0;

        return $this;
    }

    /**
     * Set the cookie domain
     * @param string|null $domain
     */
    private function setDomain(?string $domain): self
    {
        $this->domain = empty($domain) ? null : $domain;

        return $this;
    }

    /**
     * Set the cookie path
     * @param string|null $path
     */
    private function setPath(?string $path): self
    {
        $this->path = empty($path) ? null : $path;

        return $this;
    }

    /**
     * Set the cookie secure value
     * @param bool|null $secure
     */
    private function setSecure(?bool $secure): self
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * Set the cookie HTTP protocol flag
     * @param bool|null $httpOnly
     */
    private function setHttpOnly(?bool $httpOnly): self
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * Set the cookie same site value
     * @param string|null $sameSite
     */
    private function setSameSite(?string $sameSite): self
    {
        $sameSite = empty($sameSite) ? null : ucfirst(strtolower($sameSite));

        $sameSiteList = [self::SAME_SITE_NONE, self::SAME_SITE_LAX, self::SAME_SITE_STRICT];

        if ($sameSite !== null && !in_array($sameSite, $sameSiteList, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The same site attribute `%s` is not valid; must be one of (%s).',
                $sameSite,
                implode(', ', $sameSiteList)
            ));
        }

        $this->sameSite = $sameSite;

        return $this;
    }
}
