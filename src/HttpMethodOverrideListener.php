<?php

namespace RstGroup\HttpMethodOverride;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;

final class HttpMethodOverrideListener extends AbstractListenerAggregate
{
    /**
     * @var HttpMethodOverrideService
     */
    protected $httpMethodOverrideService;

    /**
     * @param HttpMethodOverrideService $httpMethodOverride
     */
    public function __construct(HttpMethodOverrideService $httpMethodOverride)
    {
        $this->httpMethodOverrideService = $httpMethodOverride;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'override'], 100);
    }

    /**
     * @param MvcEvent $event
     */
    public function override(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof Request) {
            return;
        }

        $method = $request->getMethod();
        $headers = $request->getHeaders()->toArray();

        $overridedMethod = $this->httpMethodOverrideService->getOverridedMethod($method, $headers);

        if ($overridedMethod !== $method) {
            $request->setMethod($overridedMethod);
            $event->setParam('overrided-method', $method);
        }
    }
}
