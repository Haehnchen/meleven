<?php

namespace SmMeleven\Bundle\StoreFrontBundle;

use Shopware\Bundle\StoreFrontBundle\Gateway\DBAL\Hydrator\MediaHydrator;
use Shopware\Bundle\StoreFrontBundle\Struct\Thumbnail;

class MelevenMediaHydrator extends MediaHydrator
{
    /** @var  MediaHydrator */
    private $mediaHydrator;

    /**
     * MelevenMediaHydrator constructor.
     * @param MediaHydrator $mediaHydrator
     */
    public function __construct(MediaHydrator $mediaHydrator)
    {
        $this->mediaHydrator = $mediaHydrator;
    }

    /**
     * @param array $data
     * @return \Shopware\Bundle\StoreFrontBundle\Struct\Media
     */
    public function hydrate(array $data)
    {
        $media = $this->mediaHydrator->hydrate($data);

        $media->setFile('');
        $media->setThumbnails([
            new Thumbnail(),
            new Thumbnail(),
            new Thumbnail(),
            new Thumbnail()
        ]);

        return $media;
    }

    /**
     * @param array $data
     * @return \Shopware\Bundle\StoreFrontBundle\Struct\Media
     */
    public function hydrateProductImage(array $data)
    {
        return $this->mediaHydrator->hydrateProductImage($data);
    }
}