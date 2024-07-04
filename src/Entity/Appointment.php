<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'Appointment')]
    private ?User $user = null;

    /**
     * @var Collection<int, appointmentLine>
     */
    #[ORM\OneToMany(targetEntity: AppointmentLine::class, mappedBy: 'appointment')]
    private Collection $appointment_line;

    #[ORM\ManyToOne(inversedBy: 'appointment')]
    private ?User $users = null;

    public function __construct()
    {
        $this->appointment_line = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, appointmentLine>
     */
    public function getAppointmentLine(): Collection
    {
        return $this->appointment_line;
    }

    public function addAppointmentLine(AppointmentLine $appointmentLine): static
    {
        if (!$this->appointment_line->contains($appointmentLine)) {
            $this->appointment_line->add($appointmentLine);
            $appointmentLine->setAppointment($this);
        }

        return $this;
    }

    public function removeAppointmentLine(AppointmentLine $appointmentLine): static
    {
        if ($this->appointment_line->removeElement($appointmentLine)) {
            // set the owning side to null (unless already changed)
            if ($appointmentLine->getAppointment() === $this) {
                $appointmentLine->setAppointment(null);
            }
        }

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }
}
