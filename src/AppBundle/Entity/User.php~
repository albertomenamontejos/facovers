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

/**
 * user
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @Vich\Uploadable
 */
class User implements UserInterface
{

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
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="Photo")
     * @ORM\JoinColumn(name="photo", referencedColumnName="id")
     */
    private $photo;


    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Chat", inversedBy="users")
     * @ORM\JoinTable(name="users_chat")
     */
    private $chats;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 40,
     *      minMessage = "Su nombre de usuario debe contener mínimo {{ limit }} caracteres",
     *      maxMessage = "Su nombre de usuario debe contener como máximo {{ limit }} caracteres"
     * )
     * @ORM\Column(name="username", type="string", length=40, unique=true)
     */
    private $username;


    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 5,
     *      max = 60,
     *      minMessage = "Su email debe contener mínimo {{ limit }} caracteres",
     *      maxMessage = "Su email debe contener como máximo {{ limit }} caracteres"
     * )
     * @Assert\Email(
     *     message = "El correo '{{ value }}' no es válido.",
     *     checkMX = true
     * )
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     * @Assert\Length(
     *      min = 6,
     *      max = 60,
     *      minMessage = "La contraseña de tener un mínimo de {{ limit }} caracteres",
     *      maxMessage = "La contraseña debe tener un máximo de {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 40,
     *      minMessage = "Su nombre completo debe contener mínimo {{ limit }} caracteres",
     *      maxMessage = "Su nombre completo debe contener como máximo {{ limit }} caracteres"
     * )
     * @ORM\Column(name="name", type="string", length=40)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="text", nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

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
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     */
    private $comments;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Event", mappedBy="user")
     */
    private $events;

    /**
     * Many posts have many posts_likes
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="likes")
     */
    private $posts_likes;


    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user")
     */
    private $notifications;


    public function __construct()
    {
        $this->followed = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->posts_likes = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->chats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

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

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photoName
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    //MÉTODOS RELACIONES

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
     * Is followed.
     *
     * @return boolean
     */
    public function isFollower($id_user)
    {
        foreach($this->followed as $follower){
            if($id_user == $follower->getId()){
                return true;
            }
        }
        return false;
    }


    /**
     * Set roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Add postsLike.
     *
     * @param \AppBundle\Entity\Post $postsLike
     *
     * @return User
     */
    public function addPostsLike(\AppBundle\Entity\Post $postsLike)
    {
        $this->posts_likes[] = $postsLike;

        return $this;
    }

    /**
     * Remove postsLike.
     *
     * @param \AppBundle\Entity\Post $postsLike
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePostsLike(\AppBundle\Entity\Post $postsLike)
    {
        return $this->posts_likes->removeElement($postsLike);
    }

    /**
     * Get postsLikes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPostsLikes()
    {
        return $this->posts_likes;
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
    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Add notification.
     *
     * @param \AppBundle\Entity\Notification $notification
     *
     * @return User
     */
    public function addNotification(\AppBundle\Entity\Notification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification.
     *
     * @param \AppBundle\Entity\Notification $notification
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNotification(\AppBundle\Entity\Notification $notification)
    {
        return $this->notifications->removeElement($notification);
    }

    /**
     * Add chat.
     *
     * @param \AppBundle\Entity\Chat $chat
     *
     * @return User
     */
    public function addChat(\AppBundle\Entity\Chat $chat)
    {
        $this->chats[] = $chat;

        return $this;
    }

    /**
     * Remove chat.
     *
     * @param \AppBundle\Entity\Chat $chat
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChat(\AppBundle\Entity\Chat $chat)
    {
        return $this->chats->removeElement($chat);
    }

    /**
     * Get chats.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChats()
    {
        return $this->chats;
    }

//    /**
//     * getChatsByIdUser
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getChatsByIdUser($id_user)
//    {
//        foreach($this->chats as $chat){
//            if($chat->getUser()->getId() == $id_user){
//                return $chat;
//            }
//        }
//        return null;
//    }
}
