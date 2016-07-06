<?php

namespace Shopware\SmMeleven\Utils;

class MelevenUtil
{
    /**
     * @param string $path "foo/foo/00.00.00.foo.jpg"
     * @return string
     */
    public static function normalizePath($path)
    {
        return str_replace('00.00.00.', '', $path);
    }

    /**
     * @param string $path http://api.meleven.de/out/foo/h_1600,w_1600,m_limit,o_resize/77.c9.a2.115655_front.jpg
     * @return string
     */
    public static function isDerivativesPath($path)
    {
        if(preg_match('#^out/[\w-]+/[^/]*,[^/]*/[\w+]{2}\.[\w+]{2}\.[\w+]{2}\..*\.[\w]{1,5}$#i', $path, $result)) {
           return true;
        }

        return false;
    }
}