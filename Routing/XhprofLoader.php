<?php

namespace Jns\Bundle\XhprofBundle\Routing;

use Symfony\Component\Routing\Route;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\Loader;

class XhprofLoader extends Loader
{
    public function supports($resource, $type = null)
    {
        return $type === 'xhprof';
    }

    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();
        $collection->add('enable_xhprof', new Route('/xhprof/enable', array(
            '_controller' => 'XhprofBundle:Xhprof:activate',
        ), array('_method' => 'GET')));
        
        return $collection;
    }
}