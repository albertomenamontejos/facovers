<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * user
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    //RELACIONES

    /**
     * Muchos usuarios pueden seguir a un usuario.
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="follow",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="followed_id", referencedColumnName="id")}
     *      )
     */
    private $followed; //Personas que sigo.

    /**
     * A un usuario le pertenecen muchos comentarios
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user_id")
     */
    private $comments;

    /**
     * Un usuario puede crear muchos eventos
     * @ORM\OneToMany(targetEntity="Event", mappedBy="user_id")
     */
    private $events;

    public function __construct()
    {
        $this->followed = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->events = new ArrayCollection();
    }
    // FIN RELACIONES

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=40, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="text", nullable=true)
     */
    private $bio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bday", type="datetime")
     */
    private $bday;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="string", length=500, nullable=true)
     */
    private $photo;


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
     * Set username.
     *
     * @param string $username
     *
     * @return user
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return user
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return user
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return user
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set bio.
     *
     * @param string|null $bio
     *
     * @return user
     */
    public function setBio($bio = null)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio.
     *
     * @return string|null
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set bday.
     *
     * @param \DateTime $bday
     *
     * @return user
     */
    public function setBday($bday)
    {
        $this->bday = $bday;

        return $this;
    }

    /**
     * Get bday.
     *
     * @return \DateTime
     */
    public function getBday()
    {
        return $this->bday;
    }

    /**
     * Set photo.
     *
     * @param string|null $photo
     *
     * @return user
     */
    public function setPhoto($photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo.
     *
     * @return string|null
     */
    public function getPhoto()
    {
        return $this->photo;
    }


    /**
     * Add followed.
     *
     * @param \AppBundle\Entity\User $followed
     *
     * @return User
     */
    public function addFollowed(\AppBundle\Entity\User $followed)
    {
        $this->followed[] = $followed;

        return $this;
    }

    /**
     * Remove followed.
     *
     * @param \AppBundle\Entity\User $followed
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFollowed(\AppBundle\Entity\User $followed)
    {
        return $this->followed->removeElement($followed);
    }

    /**
     * Get followed.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowed()
    {
        return $this->followed;
    }

    /**
     * Add comment.
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
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
     * Add event.
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return User
     */
    public function addEvent(\AppBundle\Entity\Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEvent(\AppBundle\Entity\Event $event)
    {
        return $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
