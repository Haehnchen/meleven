<?php

namespace Shopware\CustomModels;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnexpectedResultException;

class MelevenImageRepository extends EntityRepository
{

    /**
     * @param string $path
     * @return null|MelevenImage
     */
    public function findOneByBasename($path)
    {
        return $this->findOneBy(['basename' => $path]); 
    }

    /**
     * @param string $basename
     * @return string
     */
    public function findMelevenIdByBasename($basename)
    {
        $qb = $this->createQueryBuilder('meleven_image')
            ->select('meleven_image.melevenId')
            ->where('meleven_image.basename = :basename')
            ->setParameter('basename', $basename)
            ->setMaxResults(1)
        ;

        $query = $qb->getQuery();

        $query->useQueryCache(true);

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param string $melevenId "00.00.00.foo.jpg"
     * @return boolean
     */
    public function hasMelevenId($melevenId)
    {
        $qb = $this->createQueryBuilder('meleven_image')
            ->select('count(meleven_image.id)')
            ->where('meleven_image.melevenId = :melevenId')
            ->setParameter('melevenId', $melevenId)
        ;

        $query = $qb->getQuery();

        $query->useQueryCache(true);
        return (boolean) $query->getSingleScalarResult();
    }

    /**
     * @param MelevenImage $image
     */
    public function createAndClear(MelevenImage $image)
    {
        $this->_em->persist($image);
        $this->_em->flush($image);
        $this->_em->clear($image);
    }   
}
