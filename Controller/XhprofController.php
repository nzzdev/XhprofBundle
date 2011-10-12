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

    public function activateAction(Request $request)
    {
        $enable = $request->query->get('enable', true);
        $ttl = $request->query->get('ttl', 0);

        if (function_exists('apc_store'))
        {
            if (true === apc_store($this->apc_key, (bool) $enable, $ttl))
            {
                $content = "XHProf enabled via APC.";
            } 
            else
            {
                $content = "Can't enable xhprof via APC.";
            }
        }
        else
        {
            $content = "Can't enable XHProf via APC.";
        }
        
        return new Response($content, array('Content-Type' => 'text/plain'));
    }
}