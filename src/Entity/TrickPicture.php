<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickPictureRepository")
 */
class TrickPicture
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
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="trickPictures", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mainPicture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getMainPicture(): ?bool
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?bool $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }
}
