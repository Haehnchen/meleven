<?php

namespace SmMeleven\Bundle\StoreFrontBundle\Service;

use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class ListProductService implements ListProductServiceInterface
{
    /** @var  ListProductServiceInterface */
    private $coreService;

    /** @var ThumbnailService  */
    private $thumbnailService;

    /**
     * ListProductService constructor.
     * @param ListProductServiceInterface $coreService
     * @param ThumbnailService $thumbnailService
     */
    public function __construct(ListProductServiceInterface $coreService, ThumbnailService $thumbnailService)
    {
        $this->coreService = $coreService;
        $this->thumbnailService = $thumbnailService;
    }


    /**
     * To get detailed information about the selection conditions, structure and content of the returned object,
     * please refer to the linked classes.
     *
     * @see \Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface::get()
     *
     * @param array $numbers
     * @param Struct\ProductContextInterface $context
     * @return Struct\ListProduct[] Indexed by the product order number.
     */
    public function getList(array $numbers, Struct\ProductContextInterface $context)
    {
        $products = $this->coreService->getList($numbers, $context);

        foreach ($products as $product) {
            $thumbnails = [];

            /** @var Struct\Thumbnail $thumbnail */
            foreach ($product->getCover()->getThumbnails() as $thumbnail) {
                $struct = new Struct\Thumbnail(
                    $thumbnail->getSource(),
                    $thumbnail->getRetinaSource(),
                    $thumbnail->getMaxWidth(),
                    $thumbnail->getMaxHeight()
                );

                $thumbnails[] = $struct;
            }

            $product->getCover()->setThumbnails($thumbnails);
        }

        return $products;
    }

    /**
     * Returns a full \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct object.
     * A list product contains all required data to display products in small views like listings, sliders or emotions.
     *
     * @param string $number
     * @param Struct\ProductContextInterface $context
     * @return Struct\ListProduct
     */
    public function get($number, Struct\ProductContextInterface $context)
    {
        $product = $this->getList([$number], $context);

        return array_shift($product);
    }
}