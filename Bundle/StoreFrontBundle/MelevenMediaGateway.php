<?php

namespace SmMeleven\Bundle\StoreFrontBundle;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Gateway\DBAL\FieldHelper;
use Shopware\Bundle\StoreFrontBundle\Gateway\MediaGatewayInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class MelevenMediaGateway implements MediaGatewayInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var FieldHelper
     */
    private $fieldHelper;
    /**
     * @var MelevenMediaHydrator
     */
    private $hydrator;

    /**
     * MelevenMediaGateway constructor.
     * @param Connection $connection
     * @param FieldHelper $fieldHelper
     * @param MelevenMediaHydrator $hydrator
     */
    public function __construct(Connection $connection, FieldHelper $fieldHelper, MelevenMediaHydrator $hydrator)
    {
        $this->connection = $connection;
        $this->fieldHelper = $fieldHelper;
        $this->hydrator = $hydrator;
    }

    /**
     * To get detailed information about the selection conditions, structure and content of the returned object,
     * please refer to the linked classes.
     *
     * @see \Shopware\Bundle\StoreFrontBundle\Gateway\ProductMediaGatewayInterface::get()
     *
     * @param array $ids
     * @param Struct\ShopContextInterface $context
     * @return Struct\Media[] Indexed by the media id
     */
    public function getList($ids, Struct\ShopContextInterface $context)
    {
        $query = $this->getQuery($context);

        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        /**@var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $query->execute();

        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($data as $row) {
            $mediaId = $row['__media_id'];
            $result[$mediaId] = $this->hydrator->hydrate($row);
        }

        return $result;
    }

    /**
     * To get detailed information about the selection conditions, structure and content of the returned object,
     * please refer to the linked classes.
     *
     * @see \Shopware\Bundle\StoreFrontBundle\Gateway\ProductMediaGatewayInterface::get()
     *
     * @param $id
     * @param Struct\ShopContextInterface $context
     * @return Struct\Media
     */
    public function get($id, Struct\ShopContextInterface $context)
    {
        $media = $this->getList([$id], $context);

        return array_shift($media);
    }

    /**
     * @param Struct\ShopContextInterface $context
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getQuery(Struct\ShopContextInterface $context)
    {
        $query = $this->connection->createQueryBuilder();

        $query->select($this->fieldHelper->getMediaFields());

        $query->from('s_media', 'media')
            ->innerJoin('media', 's_media_album_settings', 'mediaSettings', 'mediaSettings.albumID = media.albumID')
            ->leftJoin('media', 's_media_attributes', 'mediaAttribute', 'mediaAttribute.mediaID = media.id')
            ->where('media.id IN (:ids)');

        $this->fieldHelper->addMediaTranslation($query, $context);

        return $query;
    }

    /**
     * The \Shopware\Bundle\StoreFrontBundle\Struct\Media requires the following data:
     * - Product image data
     * - Media data
     * - Core attribute of the product image
     * - Core attribute of the media
     *
     * Required translation in the provided context language:
     * - Product image
     *
     * Required conditions for the selection:
     * - Selects only product media which has no configurator configuration and the main flag equals 1
     * - Sorted ascending by the image position
     *
     * @param Struct\BaseProduct $product
     * @param Struct\ShopContextInterface $context
     * @return Struct\Media
     */
    public function getCover(Struct\BaseProduct $product, Struct\ShopContextInterface $context)
    {
    }

    /**
     * To get detailed information about the selection conditions, structure and content of the returned object,
     * please refer to the linked classes.
     *
     * @see \Shopware\Bundle\StoreFrontBundle\Gateway\ProductMediaGatewayInterface::getCover()
     *
     * The passed $products array contains in some case two variations of the same product.
     * For example:
     *  - Product.1  (white)
     *  - Product.2  (black)
     *
     * The function has to return an array which contains a cover for each passed product variation.
     * Product white & black shares the product cover, so the function returns the following result:
     *
     * <php>
     * array(
     *     'Product.1' => Shopware\Bundle\StoreFrontBundle\Struct\Media(id=1)
     *     'Product.2' => Shopware\Bundle\StoreFrontBundle\Struct\Media(id=1)
     * )
     * </php>
     *
     * @param Struct\BaseProduct[] $products
     * @param Struct\ShopContextInterface $context
     * @return Struct\Media[] Indexed by the product number
     */
    public function getCovers($products, Struct\ShopContextInterface $context)
    {
    }
}