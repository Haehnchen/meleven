<?php

namespace SmMeleven;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;

class SmMeleven extends Plugin implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Collect_MediaAdapter_meleven' => 'onCollectAdapter'
        ];
    }

    public function install(InstallContext $context)
    {
        /** @var \Shopware\Bundle\AttributeBundle\Service\CrudService $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');
        $crudService->update('s_media_attributes', 'meleven_id', 'string');

        $this->container->get('models')->generateAttributeModels(['s_media_attributes']);
    }

    /**
     * @return object|\Shopware\SmMeleven\Media\MediaAdapter
     */
    public function onCollectAdapter()
    {
        return $this->container->get('sm_meleven.media_adapter');
    }
}
