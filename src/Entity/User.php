<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user_data")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $telegramId;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $userSession;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $languageCode;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserItem", mappedBy="user")
     */
    private $userItem;

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

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getTelegramId(): string
    {
        return $this->telegramId;
    }

    /**
     * @param string $telegramId
     */
    public function setTelegramId(string $telegramId): void
    {
        $this->telegramId = $telegramId;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getScore(): string
    {
        return $this->score;
    }

    /**
     * @param string $score
     */
    public function setScore(string $score): void
    {
        $this->score = $score;
    }

    /**
     * @return string
     */
    public function getUserSession(): string
    {
        return $this->userSession;
    }

    /**
     * @param string $userSession
     */
    public function setUserSession(string $userSession): void
    {
        $this->userSession = $userSession;
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
