<?php

namespace Jns\Bundle\XhprofBundle\Helper;

class XhprofHelper
{
    protected $apc_key;

    public function __construct($apc_key)
    {
        $this->apc_key = $apc_key;
    }

    public function enableXhprof()
    {
        if (function_exists('apc_fetch'))
        {
            return true === apc_fetch($this->apc_key);
        }

        return false;
    }
}