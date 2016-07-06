<?php

namespace Shopware\SmMeleven\Exporter;

use Shopware\SmMeleven\Struct\MelevenConfig;

class PathEncoder
{

    /**
     * @var AliasFinder
     */
    private $aliasFinder;

    public function __construct(AliasFinder $aliasFinder)
    {
        $this->aliasFinder = $aliasFinder;
    }

    /**
     * Converts main and thumbnail from Shopware to meleven
     *
     * @param MelevenConfig $config
     * @param string $path
     * @return string
     */
    public function buildPath(MelevenConfig $config, $path)
    {
        // media/image/00/0e/39/SW10161_3_1280x1280@2x.jpg

        // //api.meleven.de/out/premiumstore/h_144,w_120,m_limit,o_pad,c_ffffff/51.87.f3.13746_front.png
        if (preg_match("#/(.*)_([\d]+)x([\d]+)(@2x)\.(.*)$#", $path, $matches)) {
            $basename = preg_replace('#_([\d]+)x([\d]+)(@2x)#', '', basename($path));
            $alias = $this->getAlias($basename);

            return 'out/' . $config->getChannel() . '/h_' . $matches[2] * 2 . ',w_' . $matches[3] * 2 . ',m_limit,o_pad,c_ffffff/' . $alias;
        } elseif (preg_match("/\/(.*)_([\d]+)x([\d]+)\.(.*)$/", $path, $matches)) {
            $basename = preg_replace('#_([\d]+)x([\d]+)#', '', basename($path));
            $alias = $this->getAlias($basename);

            return 'out/'. $config->getChannel() .'/h_' . $matches[2] . ',w_' . $matches[3] . ',m_limit,o_pad,c_ffffff/' . $alias;
        } elseif (preg_match("#image/(.*)\.(.*)$#", $path, $matches)) {
            return 'out/' . $config->getChannel() . '/' . $this->getAlias(basename($path));
        }

        return $path;
    }

    private function getAlias($basename)
    {
        if ($alias = $this->aliasFinder->findAliasForBasename($basename)) {
            return $alias;
        }

        return '00.00.00.' . $basename;
    }
}