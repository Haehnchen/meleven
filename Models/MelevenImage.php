<?php

namespace Shopware\CustomModels;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity(repositoryClass="MelevenImageRepository")
 * @ORM\Table(name="sm_meleven_images",
 *     indexes={
 *          @Index(name="meleven_id_idx", columns={"meleven_id"})
 *     },
 *     uniqueConstraints={
 *          @UniqueConstraint(name="basename_idx", columns={"basename"})
 *     }
 *  )
 */

class MelevenImage extends ModelEntity
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $path;
    
    /**
     * @ORM\Column(type="string")
     */
    private $basename;
    
    /**
     * @ORM\Column(type="string", name="meleven_id")
     */
    private $melevenId;

    /**
     * @ORM\Column(type="json_array")
     */
    private $response;

    public function __construct($basename, $path, $melevenId, array $response)
    {
        $this->basename = $basename;
        $this->path = $path;
        $this->melevenId = $melevenId;
        $this->response = $response;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getMelevenId()
    {
        return $this->melevenId;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }
}