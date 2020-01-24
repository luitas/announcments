<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnouncementClickRepository")
 * @ORM\Table(name="announcement_clicks")
 */
class AnnouncementClick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Announcement", inversedBy="announcementClicks", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $announcement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $clicked_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnouncement(): ?Announcement
    {
        return $this->announcement;
    }

    public function setAnnouncement(?Announcement $announcement): self
    {
        $this->announcement = $announcement;

        return $this;
    }

    public function getClickedAt(): ?\DateTimeInterface
    {
        return $this->clicked_at;
    }

    public function setClickedAt(\DateTimeInterface $clicked_at): self
    {
        $this->clicked_at = $clicked_at;

        return $this;
    }

    public function __construct()
    {
        $this->clicked_at = new \DateTime();
    }
}
