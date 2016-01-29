<?php

namespace RstGroup\HttpMethodOverride;

use InvalidArgumentException;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\MessageInterface;

class HttpMethodOverride
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
    protected $overrideHeaders = array(
        self::OVERRIDE_HEADER_GOOGLE,
        self::OVERRIDE_HEADER_IBM,
        self::OVERRIDE_HEADER_MICROSOFT,
    );

    /**
     * @param array $methodMap
     */
    public function __construct(array $methodMap)
    {
        $this->methodMap = $methodMap;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function override(MessageInterface $request)
    {
        if (! $request instanceof Request) {
            return;
        }

        /* @var $headers Headers */
        $headers = $request->getHeaders();
        $method = $request->getMethod();

        if (! isset($this->methodMap[$method])) {
            return false;
        }
        foreach ($this->overrideHeaders as $overrideHeader) {
            if ($headers->has($overrideHeader)) {
                $header = $headers->get($overrideHeader);
                break;
            }
        }
        if (isset($header) && $header instanceof HeaderInterface) {
            $overridenMethodName = $this->getValueFromHeader($header);
        }
        if (isset($overridenMethodName)) {
            if (is_array($this->methodMap[$method])) {
                if (in_array($overridenMethodName, $this->methodMap[$method])) {
                    $request->setMethod($overridenMethodName);

                    return true;
                }
            } else {
                throw new InvalidArgumentException('Method map must be an array');
            }
        }

        return false;
    }

    /**
     * @param HeaderInterface $header
     *
     * @return string
     */
    protected function getValueFromHeader(HeaderInterface $header)
    {
        return $header->getFieldValue();
    }
}
