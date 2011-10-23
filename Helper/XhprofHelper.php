<?php

namespace Jns\Bundle\XhprofBundle\Helper;

class XhprofHelper
{
    protected $apc_key;

    public function __construct($apc_key)
    {
        $this->apc_key = $apc_key;
    }

    public function xhprofEnabled()
    {
        if (function_exists('apc_fetch'))
        {
            return true === apc_fetch($this->apc_key);
        }

        return false;
    }

    public function toggleXhprofStatus($enable, $ttl)
    {
        if (function_exists('apc_store'))
        {
            return true === apc_store($this->apc_key, (bool) $enable, $ttl);
        }

        return false;
    }
}