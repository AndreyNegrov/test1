<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $transcription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $english;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $word;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $card;

    /**
     * @ORM\Column(type="string", length=1000000, nullable=true)
     */
    private $additionInfo;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserItem", mappedBy="item")
     */
    private $userItem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GlobaUser", inversedBy="items")
     */
    private $globalUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GlobaUser")
     */
    private $userEdited;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAdded;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEdited;

    /**
     * Get dateEdited
     *
     * @return \DateTime
     */
    public function getDateEdited()
    {
        return $this->dateEdited;
    }

    /**
     * Set dateEdited
     *
     * @param \DateTime $dateEdited
     * @return Item
     */
    public function setDateEdited($dateEdited)
    {
        $this->dateEdited = $dateEdited;
        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Item
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
        return $this;
    }

    public function getGlobalUser(): ?GlobaUser
    {
        return $this->globalUser;
    }

    public function setGlobalUser(?GlobaUser $globalUser): self
    {
        $this->globalUser = $globalUser;

        return $this;
    }

    public function getUserEdited(): ?GlobaUser
    {
        return $this->userEdited;
    }

    public function setUserEdited(?GlobaUser $userEdited): self
    {
        $this->userEdited = $userEdited;

        return $this;
    }

    public function __construct()
    {
        $this->userItem = new ArrayCollection();
    }

    /**
     * @return Collection|UserItem[]
     */
    public function getUserItems(): Collection
    {
        return $this->userItem;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranscription(): ?string
    {
        return $this->transcription;
    }

    public function setTranscription(?string $transcription): self
    {
        $this->transcription = $transcription;

        return $this;
    }

    public function getEnglish(): ?string
    {
        return $this->english;
    }

    public function setEnglish(?string $english): self
    {
        $this->english = $english;

        return $this;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(?string $word): self
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param mixed $card
     */
    public function setCard($card): void
    {
        $this->card = $card;
    }

    /**
     * @return mixed
     */
    public function getAdditionInfo()
    {
        return $this->additionInfo;
    }

    /**
     * @param mixed $additionInfo
     */
    public function setAdditionInfo($additionInfo): void
    {
        $this->additionInfo = $additionInfo;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

}
