<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use AppBundle\Entity\User;

use Serializable;
/**

/**
 * user
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhotoRepository")
 * @Vich\Uploadable
 */
class Photo implements \Serializable
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photoName", type="string", length=500, nullable=true)
     */
    private $photoName;

    /**
     *
     * NOTA: Este no es un campo asignado de metadatos de entidad, solo una propiedad simple.
     *
     * @Vich\UploadableField(mapping="assets", fileNameProperty="photoName", size="photoSize")
     *
     * @var File|null
     */
    private $photoFile;


    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer|null
     */
    private $photoSize;

    private $enlace;

    /**
     * @return int|null
     */
    public function getEnlace()
    {
        return $this->enlace;
    }

    /**
     * @param int|null $enlace
     */
    public function setEnlace($enlace)
    {
        $this->enlace = $enlace;
    }

    /**
     * @return File
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @param File $photoFile
     */
    public function setPhotoFile($photoFile)
    {
        $this->photoFile = $photoFile;
    }

    /**
     * Set photo.
     *
     * @param string|null $photo
     *
     * @return user
     */
    public function setPhotoNAme($photoName = null)
    {
        $this->photoName = $photoName;
        return $this;
    }

    /**
     * Get photo.
     *
     * @return string|null
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }

    /**
     * @return int
     */
    public function getPhotoSize()
    {
        return $this->photoSize;
    }

    /**
     * @param int $photoSize
     */
    public function setPhotoSize($photoSize)
    {
        $this->photoSize = $photoSize;
    }

    /*
    Rest of our awesome entity
    */

    public function serialize()
    {
        $this->photoFile = base64_encode($this->photoFile);
    }

    public function unserialize($serialized)
    {
        $this->photoFile = base64_decode($this->photoFile);

    }

//    /** @see \Serializable::serialize() */
//    public function serialize()
//    {
//        return serialize(array(
//            $this->id,
//            $this->photoFile,
//        ));
//    }
//
//    /** @see \Serializable::unserialize() */
//    public function unserialize($serialized)
//    {
//        list (integer
//            $this->id,
//            $this->photoFile,
//            ) = unserialize($serialized, array('allowed_classes' => false));
//    }
}
