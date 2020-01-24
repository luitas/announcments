<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnouncementRepository")
 * @ORM\Table(name="announcements")
 */
class Announcement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $published_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closed_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AnnouncementClick", mappedBy="announcement", cascade={"remove"})
     */
    private $announcementClicks;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closed_at;
    }

    public function setClosedAt(?\DateTimeInterface $closed_at): self
    {
        $this->closed_at = $closed_at;

        return $this;
    }

    public function __construct()
    {
        $this->show_count = 0;
        $this->is_active = true;
        $this->announcementClicks = new ArrayCollection();
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * @return Collection|AnnouncementClick[]
     */
    public function getAnnouncementClicks(): Collection
    {
        return $this->announcementClicks;
    }

    public function addAnnouncementClick(AnnouncementClick $announcementClick): self
    {
        if (!$this->announcementClicks->contains($announcementClick)) {
            $this->announcementClicks[] = $announcementClick;
            $announcementClick->setAnnouncement($this);
        }

        return $this;
    }

    public function removeAnnouncementClick(AnnouncementClick $announcementClick): self
    {
        if ($this->announcementClicks->contains($announcementClick)) {
            $this->announcementClicks->removeElement($announcementClick);
            // set the owning side to null (unless already changed)
            if ($announcementClick->getAnnouncement() === $this) {
                $announcementClick->setAnnouncement(null);
            }
        }

        return $this;
    }
}
