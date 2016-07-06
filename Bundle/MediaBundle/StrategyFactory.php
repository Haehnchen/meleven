<?php

namespace SmMeleven\Bundle\MediaBundle;

use Shopware\Bundle\MediaBundle\Strategy\StrategyFactory as BaseStrategyFactory;

class StrategyFactory extends BaseStrategyFactory
{
    /**
     * @var BaseStrategyFactory
     */
    private $factory;
    
    /**
     * @var MelevenStrategy
     */
    private $strategy;

    public function __construct(BaseStrategyFactory $factory, MelevenStrategy $strategy)
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