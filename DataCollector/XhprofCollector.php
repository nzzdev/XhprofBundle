<?php


namespace Jns\Bundle\XhprofBundle\DataCollector;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use XHProfRuns_Default;

/**
 * XhprofDataCollector.
 *
 * @author Jonas Wouters <hello@jonaswouters.be>
 */
class XhprofCollector extends DataCollector
{
    protected $container;
    protected $logger;

    protected $xhprofHelper;

    protected $runId;
    protected $profiling = false;

    public function __construct(ContainerInterface $container, LoggerInterface $logger = null, $xhprofHelper)
    {
        $this->container = $container;
        $this->logger = $logger;
        $this->xhprofHelper = $xhprofHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (!$this->runId) {
            $this->stopProfiling();
        }

        $this->data = array(
            'xhprof' => $this->runId,
            'xhprof_url' => $this->container->getParameter('jns_xhprof.location_web'),
        );
    }

    public function startProfiling()
    {
        if ($this->xhprofHelper->enableXhprof())
        {
            $this->profiling = true;
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
            if ($this->logger) {
                $this->logger->debug('Enabled XHProf');
            }
        }
    }

    public function stopProfiling()
    {
        //TODO: Needed only if using preinheimer fork.
        // global $_xhprof;

        //TODO add a configurable way to enable XHprof, probably a service and an Interface with enableXhprof.
        if ($this->xhprofHelper->enableXhprof())
        {
            if (!$this->profiling) {
                return;
            }

            $this->profiling = false;
            
            //TODO: Needed only if using preinheimer fork.
            //require_once $this->container->getParameter('jns_xhprof.location_config');
            require_once $this->container->getParameter('jns_xhprof.location_lib');
            require_once $this->container->getParameter('jns_xhprof.location_runs');

            $xhprof_data = xhprof_disable();
            
            if (!empty($xhprof_data))
            {
                $xhprof_runs = new XHProfRuns_Default('/tmp');
                $this->runId = $xhprof_runs->save_run($xhprof_data, $this->getCurrentRoute());
            }
            else
            {
                $this->logger->debug('XHProf collected data was empty');
                $this->runId = "none";
            }

            if ($this->logger)
            {
                $this->logger->debug('Stopped collecting data with XHProf for current request');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'xhprof';
    }

    /**
     * Gets the run id.
     *
     * @return integer The run id
     */
    public function getXhprof()
    {
        return $this->data['xhprof'];
    }

    /**
     * Gets the XHProf url.
     *
     * @return integer The XHProf url
     */
    public function getXhprofUrl()
    {
        return $this->data['xhprof_url'] . '?run=' . $this->data['xhprof'] . '&source=Symfony';
    }

    protected function getCurrentRoute()
    {
        return $this->container->get('request')->get('_route', 'Symfony');
    }
}
