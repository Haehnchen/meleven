<?php

namespace SmMeleven;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin;

class SmMeleven extends Plugin implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Collect_MediaAdapter_meleven' => 'onCollectAdapter'
        ];
    }

    /**
     * @return object|\Shopware\SmMeleven\Media\MediaAdapter
     */
    public function onCollectAdapter()
    {
        return $this->container->get('sm_meleven.media_adapter');
    }
}
