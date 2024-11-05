<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDateTime = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Availability $availability = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?User $user = null; // Changement ici

    #[ORM\Column(length: 100)]
    private ?string $name = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAvailability(): ?Availability
    {
        return $this->availability;
    }

    public function setAvailability(?Availability $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user; // Changement ici
    }

    public function setUser(?User $user): static
    {
        $this->user = $user; // Changement ici

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
