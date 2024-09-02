<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

// #[Vich\Uploadable()]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ProfilUser $profilUser = null;

    /**
     * @var Collection<int, appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'users')]
    private Collection $appointment;

    /**
     * @var Collection<int, order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'orders')]
    private Collection $orders;

    public function __construct()
    {
        $this->appointment = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProfiluser(): ?ProfilUser
    {
        return $this->profilUser;
    }

    public function setProfiluser(?ProfilUser $profiluser): static
    {
        $this->profilUser = $profiluser;

        return $this;
    }

    /**
     * @return Collection<int, appointment>
     */
    public function getAppointment(): Collection
    {
        return $this->appointment;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointment->contains($appointment)) {
            $this->appointment->add($appointment);
            $appointment->setUsers($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointment->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getUsers() === $this) {
                $appointment->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setOrders($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getOrders() === $this) {
                $order->setOrders(null);
            }
        }

        return $this;
    }
}
