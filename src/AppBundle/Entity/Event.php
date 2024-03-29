<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
{
    //RELACIONES
    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Muchos usuarios pueden asistir a muchos eventos
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="user_assistant",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $assistants;

    public function __construct()
    {
        $this->assistants = new ArrayCollection();
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * Set name.
     *
     * @param string $name
     *
     * @return event
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
     * Set location.
     *
     * @param string $location
     *
     * @return event
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set price.
     *
     * @param string|null $price
     *
     * @return event
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return event
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set photo.
     *
     * @param string|null $photo
     *
     * @return event
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
     * Add assistant.
     *
     * @param \AppBundle\Entity\User $assistant
     *
     * @return Event
     */
    public function addAssistant(\AppBundle\Entity\User $assistant)
    {
        $this->assistants[] = $assistant;

        return $this;
    }

    /**
     * is assistant.
     *
     * @param \AppBundle\Entity\User $assistant
     *
     * @return boolean
     */
    public function isAssistant(\AppBundle\Entity\User $assistant)
    {
        foreach($this->assistants as $user){
            if($user->getId() == $assistant->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * Remove assistant.
     *
     * @param \AppBundle\Entity\User $assistant
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAssistant(\AppBundle\Entity\User $assistant)
    {
        return $this->assistants->removeElement($assistant);
    }

    /**
     * Get assistants.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssistants()
    {
        return $this->assistants;
    }

    /**
     * Remove all assistants.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function removeAssistants()
    {
        $this->assistants = [];
    }



    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return Event
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
