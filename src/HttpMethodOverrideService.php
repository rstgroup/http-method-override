<?php

namespace RstGroup\HttpMethodOverride;

use InvalidArgumentException;

final class HttpMethodOverrideService
{
    const OVERRIDE_HEADER = self::OVERRIDE_HEADER_GOOGLE;
    const OVERRIDE_HEADER_GOOGLE = 'X-HTTP-Method-Override';
    const OVERRIDE_HEADER_MICROSOFT = 'X-HTTP-Method';
    const OVERRIDE_HEADER_IBM = 'X-Method-Override';

    /**
     * @var array
     */
    protected $methodMap;

    /**
     * @var array
     */
    protected $overrideHeaders = [
        self::OVERRIDE_HEADER_GOOGLE,
        self::OVERRIDE_HEADER_IBM,
        self::OVERRIDE_HEADER_MICROSOFT,
    ];

    /**
     * @param array $methodMap
     * @param array $overrideHeaders If any, they replace existing default headers list
     */
    public function __construct(array $methodMap, array $overrideHeaders = null)
    {
        $this->methodMap = $methodMap;

        if (!empty($overrideHeaders)) {
            $this->overrideHeaders = $overrideHeaders;
        }
    }

    /**
     * @param string $method Current method
     * @param array $headers Headers list
     *
     * @return string Overrided method name (if not overrided then return current)
     */
    public function getOverridedMethod($method, array $headers)
    {
        $headers = array_change_key_case($headers, CASE_LOWER);

        if (! isset($this->methodMap[$method])) {
            return $method;
        }
        foreach ($this->overrideHeaders as $overrideHeader) {
            $overrideHeader = strtolower($overrideHeader);
            if (isset($headers[$overrideHeader]) && $this->isMethodInMap($headers[$overrideHeader], $this->methodMap[$method])) {
                return $headers[$overrideHeader];
            }
        }
        return $method;
    }

    /**
     * @return array
     */
    public function getOverrideHeaders()
    {
        return $this->overrideHeaders;
    }

    /**
     * @param string $overridedMethodName
     * @param array $map
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    private function isMethodInMap($overridedMethodName, $map)
    {
        if (is_array($map)) {
            return in_array($overridedMethodName, $map, true);
        }
        throw new InvalidArgumentException('Method map must be an array');
    }
}
