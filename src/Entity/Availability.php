<?php

namespace App\Entity;

use App\Repository\AvailabilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDateTime = null;

    #[ORM\Column]
    private ?bool $isAvailable = true;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    private ?User $adminUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDateTime(): ?\DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getEndDateTime(): ?\DateTimeInterface
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(\DateTimeInterface $endDateTime): static
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getAdminUser(): ?User
    {
        return $this->adminUser;
    }

    public function setAdminUser(?User $adminUser): static
    {
        $this->adminUser = $adminUser;

        return $this;
    }
}
