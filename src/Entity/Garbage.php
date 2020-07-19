<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GarbageRepository")
 */
class Garbage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $telegramId;

    /**
     * @ORM\Column(type="string")
     */
    private $messageId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdded;

    /**
     * Get dateAdded
     *
     * @return DateTime
     */
    public function getDateAdded()
    {
        return $this->getDateAdded();
    }

    /**
     * Set dateAdded
     *
     * @param DateTime $dateAdded
     * @return Garbage
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
        return $this;
    }

    public function getMessageId()
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId)
    {
        $this->messageId = $messageId;
        return $this;
    }

    public function getTelegramId()
    {
        return $this->telegramId;
    }

    public function setTelegramId(string $telegramId)
    {
        $this->telegramId = $telegramId;
        return $this;
    }

}
