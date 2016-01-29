<?php

namespace RstGroup\HttpMethodOverride;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class HttpMethodOverrideListener extends AbstractListenerAggregate
{
    /**
     * @var HttpMethodOverride
     */
    protected $httpMethodOverride;

    /**
     * @param HttpMethodOverride $httpMethodOverride
     */
    public function __construct(HttpMethodOverride $httpMethodOverride)
    {
        $this->httpMethodOverride = $httpMethodOverride;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'override'), 100);
    }

    /**
     * @param MvcEvent $event
     */
    public function override(MvcEvent $event)
    {
        $request = $event->getRequest();
        $this->httpMethodOverride->override($request);
    }
}
