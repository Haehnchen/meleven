<?php

use Shopware\Bundle\MediaBundle\Strategy\PlainStrategy;
use Shopware\SmMeleven\Exporter\PathEncoder;
use Shopware\SmMeleven\Media\MediaAdapter;
use Shopware\SmMeleven\Media\MediaStrategy;
use Shopware\SmMeleven\Media\StrategyFactory;
use Shopware\SmMeleven\Struct\MelevenConfig;

class Shopware_Plugins_Frontend_SmMeleven_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getVersion() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        return 'Meleven Image-Cloud';
    }

    public function uninstall()
    {
        return true;
    }

    public function update($oldVersion)
    {
        return true;
    }

    /**
     * Returns plugin info
     *
     * @return array
     */
    public function getInfo()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        return array_merge(parent::getInfo(), [
            'author' => '<a href="' . $info['link'] . '">' . $info['author'] . '</a>',
        ]);
    }
    
    public function install()
    {
        if (!$this->assertMinimumVersion('5.1.0')) {
            throw new \RuntimeException('At least Shopware 4.3.0 is required');
        }

        $this->subscribeEvent(
            'Shopware_Collect_MediaAdapter_meleven',
            'createMelevenAdapter'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_AfterInitResource_shopware_media.strategy_factory',
            'decorateMediaFactory'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_AfterInitResource_shopware_storefront.media_hydrator_dbal',
            'decorateMediaHydratorDbal'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

        /** @var \Shopware\Bundle\AttributeBundle\Service\CrudService $crudService */
        $crudService = $this->get('shopware_attribute.crud_service');
        $crudService->update('s_media_attributes', 'meleven_id', 'string');

        return true;
    }

    public function decorateMediaHydratorDbal(\Enlight_Event_EventArgs $args)
    {
        $mediaHydrator = Shopware()->Container()->get('shopware_storefront.media_hydrator_dbal');

//        $melevenHydrator = new \Shopware\SmMeleven\Bundle\StoreFrontBundle\MelevenMediaHydrator(
//            $mediaHydrator
//        );

        Shopware()->Container()->set('shopware_storefront.media_hydrator_dbal', $mediaHydrator);
    }

    public function decorateMediaFactory(\Enlight_Event_EventArgs $args)
    {
        return;
        
        /** @var \Shopware $sw */
        $sw = Shopware();


        $cdnConfig = Shopware()->Container()->getParameterBag()->get('shopware.cdn');

        $config = $cdnConfig['adapters']['meleven'];
        $config = new MelevenConfig(
            $config['auth']['user'],
            $config['auth']['password'],
            $config['auth']['channel']
        );

        $core = $sw->Container()->get('shopware_media.strategy_factory');

        $finder = new \Shopware\SmMeleven\Exporter\AliasFinder(
            Shopware()->Models()->getRepository('Shopware\CustomModels\MelevenImage')
        );

        $strategy = new MediaStrategy(new PlainStrategy(), new PathEncoder($finder), $config);
        
        $sw->Container()->set(
            'shopware_media.strategy_factory',
            new StrategyFactory($core, $strategy)
        );
    }
    
    public function createMelevenAdapter(\Enlight_Event_EventArgs $args)
    {
        $config = $args->getConfig();

        $config = new MelevenConfig(
            $config['auth']['user'],
            $config['auth']['password'],
            $config['auth']['channel']
        );

        $exporter = new \Shopware\SmMeleven\Exporter\ImageExporter(
            new \GuzzleHttp\Client(),
            Shopware()->Models()->getRepository('Shopware\CustomModels\MelevenImage'),
            new \Psr\Log\NullLogger()
        );

        $finder = new \Shopware\SmMeleven\Exporter\AliasFinder(
            Shopware()->Models()->getRepository('Shopware\CustomModels\MelevenImage')
        );

        $modelManager = $this->get('models');

        return new MediaAdapter($exporter, $finder, $config, $modelManager);
    }

    /**
     * This callback function is triggered at the very beginning of the dispatch process and allows
     * us to register additional events on the fly. This way you won't ever need to reinstall you
     * plugin for new events - any event and hook can simply be registerend in the event subscribers
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $subscribers = [];

        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }

    public function afterInit()
    {
        $this->registerMyComponents();
        $this->registerCustomModels();
    }

    public function registerMyComponents()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\SmMeleven',
            $this->Path()
        );
    }
}
