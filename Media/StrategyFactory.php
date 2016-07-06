<?php

namespace Shopware\SmMeleven\Media;

use Shopware\Bundle\MediaBundle\Strategy\StrategyFactory as BaseStrategyFactory;

class StrategyFactory extends BaseStrategyFactory
{
    /**
     * @var BaseStrategyFactory
     */
    private $factory;
    
    /**
     * @var MediaStrategy
     */
    private $strategy;

    public function __construct(BaseStrategyFactory $factory, MediaStrategy $strategy)
    {
        $this->factory = $factory;
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($strategy)
    {
        if($strategy === 'meleven') {
            return $this->strategy;
        }
        
        return $this->factory->factory($strategy);
    }
}