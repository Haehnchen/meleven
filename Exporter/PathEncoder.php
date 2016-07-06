<?php

namespace SmMeleven\Exporter;

use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Media\Media;
use SmMeleven\Struct\MelevenConfig;

class PathEncoder
{
    /** @var  ModelManager */
    private $modelManager;

    /** @var DataLoader */
    private $attributeLoader;

    /**
     * PathEncoder constructor.
     * @param ModelManager $modelManager
     * @param DataLoader $attributeLoader
     */
    public function __construct(ModelManager $modelManager, DataLoader $attributeLoader)
    {
        $this->modelManager = $modelManager;
        $this->attributeLoader = $attributeLoader;
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
        $melevenId = $this->getMelevenId($path);

        // //api.meleven.de/out/premiumstore/h_144,w_120,m_limit,o_pad,c_ffffff/51.87.f3.13746_front.png
        if (preg_match("#/(.*)_([\d]+)x([\d]+)(@2x)\.(.*)$#", $path, $matches)) {
            return 'out/' . $config->getChannel() . '/h_' . $matches[2] * 2 . ',w_' . $matches[3] * 2 . ',m_limit,o_pad,c_ffffff/' . $melevenId;
        } elseif (preg_match("/\/(.*)_([\d]+)x([\d]+)\.(.*)$/", $path, $matches)) {
            return 'out/'. $config->getChannel() .'/h_' . $matches[2] . ',w_' . $matches[3] . ',m_limit,o_pad,c_ffffff/' . $melevenId;
        } elseif (preg_match("#image/(.*)\.(.*)$#", $path, $matches)) {
            return 'out/' . $config->getChannel() . '/' . $melevenId;
        }

        return $path;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getMelevenId($path)
    {
        $media = $this->modelManager->getRepository(Media::class)->findOneBy(['path' => $path]);

        if (!$media) {
            return null;
        }

        $attribute = $this->attributeLoader->load('s_media_attributes', $media->getId());

        return $attribute['meleven_id'];
    }
}