<?php

namespace Jns\Bundle\XhprofBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class XhprofController
{
    protected $xhprofHelper;

    public function __construct($xhprofHelper)
    {
        $this->xhprofHelper = $xhprofHelper;
    }

    //TODO create a class that handles enabling from different sources, not APC only.
    public function activateAction(Request $request)
    {
        $enable = $request->query->get('enable', false);
        $ttl = $request->query->get('ttl', 0);

        $xhprofEnabled = $this->xhprofHelper->toggleXhprofStatus($enable, $ttl);

        $content = sprintf("XHProf %s via APC.", $xhprofEnabled ? 'enabled' : 'disabled');

        return new Response($content, 200, array('Content-Type' => 'text/plain'));
    }

    public function statusAction()
    {
        $status = $this->xhprofHelper->xhprofEnabled() ? 'enabled' : 'disabled';
        $content = sprintf('XHProf is currently %s', $status);
        return new Response($content, 200, array('Content-Type' => 'text/plain'));
    }
}