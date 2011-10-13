<?php

namespace Jns\Bundle\XhprofBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class XhprofController
{
    protected $apc_key;

    public function __construct($apc_key)
    {
        $this->apc_key = $apc_key;
    }

    //TODO create a class that handles enabling from different sources, not APC only.
    public function activateAction(Request $request)
    {
        $enable = $request->query->get('enable', false);
        $ttl = $request->query->get('ttl', 0);

        if (function_exists('apc_store'))
        {
            if (true === apc_store($this->apc_key, (bool) $enable, $ttl))
            {
                $content = sprintf("XHProf %s via APC.", $enable ? 'enabled' : 'disabled');
            } 
            else
            {
                $content = "Can't enable xhprof via APC.";
            }
        }
        else
        {
            $content = "Can't enable XHProf via APC. APC not installed.";
        }
        
        return new Response($content, 200, array('Content-Type' => 'text/plain'));
    }
}