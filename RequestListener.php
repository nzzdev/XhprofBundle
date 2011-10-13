<?php

namespace Jns\Bundle\XhprofBundle;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Jns\Bundle\XhprofBundle\Helper\XhprofHelper;

/**
 * RequestListener.
 *
 * The handle method must be connected to the core.request event.
 *
 * @author Jonas Wouters <hello@jonaswouters.be>
 */
class RequestListener
{
    protected $logger;
    protected $xhprofHelper;

    public function __construct(LoggerInterface $logger = null, $xhprofHelper)
    {
        $this->logger = $logger;
        $this->xhprofHelper = $xhprofHelper;
    }

    public function onCoreRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            //TODO add a configurable way to enable XHprof, probably a service and an Interface with enableXhprof.
            if ($this->xhprofHelper->enableXhprof())
            {
                xhprof_enable();
            }
            if ($this->logger)
            {
                $this->logger->debug('Enabled XHProf');
            }
        }
    }
}
