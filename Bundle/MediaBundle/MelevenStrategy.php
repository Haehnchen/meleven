<?php

namespace SmMeleven\Bundle\MediaBundle;

use Shopware\Bundle\MediaBundle\Strategy\PlainStrategy;
use Shopware\Bundle\MediaBundle\Strategy\StrategyInterface;
use SmMeleven\Exporter\PathEncoder;
use SmMeleven\Struct\MelevenConfig;

class MelevenStrategy implements StrategyInterface
{

    /**
     * @var PlainStrategy
     */
    private $strategy;
    
    /**
     * @var MelevenConfig
     */
    private $config;

    /**
     * @var PathEncoder
     */
    private $encoder;

    public function __construct(
        PlainStrategy $strategy,
        PathEncoder $encoder,
        MelevenConfig $config
    )
    {
        $this->strategy = $strategy;
        $this->config = $config;
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($path)
    {
        return $this->strategy->normalize($path);
    }

    /**
     * {@inheritdoc}
     */
    public function encode($path)
    {
        return $this->encoder->buildPath($this->config, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function isEncoded($path)
    {
        return strpos($path, 'api.meleven.de/') !== false;
    }
}