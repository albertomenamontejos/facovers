<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\Response;
/**
 * post
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @Vich\Uploadable
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * NOTA: Este no es un campo asignado de metadatos de entidad, solo una propiedad simple.
     *
     * @Vich\UploadableField(mapping="assets", fileNameProperty="videoName", size="videoSize")
     *
     * @var File
     */
    private $videoFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $videoName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $videoSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Si cargar un archivo manualmente (es decir, no usar el formulario de Symfony) asegure una instancia
     * de 'UploadedFile' se inyecta en este configurador para activar la actualización. Si esto
     * el parámetro de configuración del paquete 'inject_on_load' se establece en 'true' este configurador
     * debe poder aceptar una instancia de 'Archivo' ya que el paquete inyectará una aquí
     * Durante la hidratación de la Doctrina.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     */
    public function setVideoFile($video = null)
    {
        $this->videoFile = $video;

        if (null !== $video) {
            // Se requiere que al menos un campo cambie si está usando doctrina
            // de lo contrario, no se llamará a los detectores de eventos y se perderá el archivo
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getVideoFile()
    {
        return $this->videoFile;
    }

    public function setVideoName($videoName)
    {
        $this->videoName = $videoName;
    }

    public function getVideoName()
    {
        return $this->videoName;
    }

    public function setVideoSize($videoSize)
    {
        $this->videoSize = $videoSize;
    }

    public function getVideoSize()
    {
        return $this->videoSize;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
    //RELACIONES

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * En un post pueden haber varios comentarios
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post_id")
     */
    private $comments;

    /**
     * Muchos usuarios pueden seguir a muchos usuarios.
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="like",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }



    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="views", type="integer", nullable=true)
     */
    private $views;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set views.
     *
     * @param int|null $views
     *
     * @return post
     */
    public function setViews($views = null)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views.
     *
     * @return int|null
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add like.
     *
     * @param \AppBundle\Entity\User $like
     *
     * @return post
     */
    public function addLike(\AppBundle\Entity\User $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like.
     *
     * @param \AppBundle\Entity\User $like
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLike(\AppBundle\Entity\User $like)
    {
        return $this->likes->removeElement($like);
    }

    /**
     * Get likes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add comment.
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        return $this->comments->removeElement($comment);
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (! in_array($this->file->getMimeType(), array(
            'video/mp4',
            'video/quicktime',
            'video/avi',
        ))) {
            $context
                ->buildViolation('Wrong file type (mp4,mov,avi)')
                ->atPath('videoName')
                ->addViolation()
            ;
        }
    }

}
