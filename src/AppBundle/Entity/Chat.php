<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="chat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChatRepository")
 */
class Chat
{
    function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mensajes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="chats")
     */
    private $users;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="leido", type="boolean",nullable=true)
     */
    private $leido;

    /**
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="Message")
     * @ORM\JoinTable(name="chat_message",
     *      joinColumns={@ORM\JoinColumn(name="chat_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $mensajes;


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
     * Set leido.
     *
     * @param bool $leido
     *
     * @return Chat
     */
    public function setLeido($leido)
    {
        $this->leido = $leido;

        return $this;
    }

    /**
     * Get leido.
     *
     * @return bool
     */
    public function getLeido()
    {
        return $this->leido;
    }

    /**
     * Add user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Chat
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        return $this->users->removeElement($user);
    }

    /**
     * Get users.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add mensaje.
     *
     * @param \AppBundle\Entity\Message $mensaje
     *
     * @return Chat
     */
    public function addMensaje(\AppBundle\Entity\Message $mensaje)
    {
        $this->mensajes[] = $mensaje;

        return $this;
    }

    /**
     * Remove mensaje.
     *
     * @param \AppBundle\Entity\Message $mensaje
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMensaje(\AppBundle\Entity\Message $mensaje)
    {
        return $this->mensajes->removeElement($mensaje);
    }

    /**
     * Get mensajes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMensajes()
    {
        return $this->mensajes;
    }
}
