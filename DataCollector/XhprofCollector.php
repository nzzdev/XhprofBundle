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
        //TODO add a configurable way to enable XHprof, probably a service and an Interface with enableXhprof.
        if ($this->xhprofHelper->enableXhprof())
        {
            require_once $this->container->getParameter('jns_xhprof.location_lib');
            require_once $this->container->getParameter('jns_xhprof.location_runs');

            $xhprof_data = xhprof_disable();
            
            if (!empty($xhprof_data))
            {
                $xhprof_runs = new XHProfRuns_Default('/tmp');
                $run_id = $xhprof_runs->save_run($xhprof_data, $request->get('_route', 'default'));
            }
            else
            {
                $this->logger->debug('XHProf collected data was empty');
                $run_id = "none";
            }

            if ($this->logger)
            {
                $this->logger->debug('Stopped collecting data with XHProf for current request');
            }
            
            $this->data = array(
                'xhprof' => $run_id,
                'xhprof_url' => $this->container->getParameter('jns_xhprof.location_web'),
            );
        }
        else
        {
            $this->data = array();
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
}
