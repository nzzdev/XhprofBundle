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
    protected $apc_key;

    public function __construct(LoggerInterface $logger = null, $apc_key)
    {
        $this->logger = $logger;
        $this->apc_key = $apc_key;
    }

    public function onCoreRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->enableXhprof())
            {
                xhprof_enable();
            }
            if ($this->logger)
            {
                $this->logger->debug('Enabled XHProf');
            }
        }
    }

    protected function enableXhprof()
    {
        if (function_exists('apc_fetch'))
        {
            return true === apc_fetch($this->apc_key);
        }
        else
        {
            return false;
        }
    }
}
