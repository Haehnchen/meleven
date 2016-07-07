<?php

namespace SmMeleven\Bundle\StoreFrontBundle\Service;

use SmMeleven\Bundle\StorefrontBundle\Struct\Thumbnail;

class ThumbnailService
{
    /**
     * @param Thumbnail $thumbnail
     * @return string
     */
    public function getUrl(Thumbnail $thumbnail)
    {
        return $thumbnail->getMedia()->getFile();
    }
}