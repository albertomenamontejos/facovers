<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * comments
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\commentsRepository")
 */
class Comment
{
    //RELACIONES

    /**
     * Muchos comentarios pertenecen a un solo post
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post_id;

    /**
     * Muchos comentarios pertenecen a un solo usuario
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;


    /**
     * Un comentario puede tener muchas respuestas.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="comment_parent")
     */
    private $respuestas;

    /**
     * Una respuesta pertenece a un solo comentario.
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="respuestas")
     * @ORM\JoinColumn(name="comment_parent", referencedColumnName="id")
     */
    private $comment_parent;

    public function __construct()
    {
        $this->respuestas = new ArrayCollection();
    }
    //FIN RELACIONES

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
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $updateAt;


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
     * Set content.
     *
     * @param string|null $content
     *
     * @return comment
     */
    public function setContent($content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return comment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updateAt.
     *
     * @param \DateTime|null $updateAt
     *
     * @return comment
     */
    public function setUpdateAt($updateAt = null)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt.
     *
     * @return \DateTime|null
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set postId.
     *
     * @param \AppBundle\Entity\Post|null $postId
     *
     * @return Comment
     */
    public function setPostId(\AppBundle\Entity\Post $postId = null)
    {
        $this->post_id = $postId;

        return $this;
    }

    /**
     * Get postId.
     *
     * @return \AppBundle\Entity\Post|null
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * Set userId.
     *
     * @param \AppBundle\Entity\User|null $userId
     *
     * @return Comment
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Add respuesta.
     *
     * @param \AppBundle\Entity\Comment $respuesta
     *
     * @return Comment
     */
    public function addRespuesta(\AppBundle\Entity\Comment $respuesta)
    {
        $this->respuestas[] = $respuesta;

        return $this;
    }

    /**
     * Remove respuesta.
     *
     * @param \AppBundle\Entity\Comment $respuesta
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRespuesta(\AppBundle\Entity\Comment $respuesta)
    {
        return $this->respuestas->removeElement($respuesta);
    }

    /**
     * Get respuestas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRespuestas()
    {
        return $this->respuestas;
    }

    /**
     * Set commentParent.
     *
     * @param \AppBundle\Entity\Comment|null $commentParent
     *
     * @return Comment
     */
    public function setCommentParent(\AppBundle\Entity\Comment $commentParent = null)
    {
        $this->comment_parent = $commentParent;

        return $this;
    }

    /**
     * Get commentParent.
     *
     * @return \AppBundle\Entity\Comment|null
     */
    public function getCommentParent()
    {
        return $this->comment_parent;
    }
}
