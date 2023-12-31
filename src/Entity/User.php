<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[UniqueEntity(fields:['email'],message:'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'training')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Training $training_id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $workout = null;

    #[ORM\Column(nullable: true)]
    private ?int $training_days = null;

    #[ORM\Column(type: Types::STRING,nullable: true)]
    private ?string $avatar = null;

    #[ORM\ManyToMany(targetEntity: Friends::class, mappedBy: 'user')]
    private Collection $friends;

    #[ORM\ManyToOne(inversedBy: 'sender')]
    private ?Invitations $sender = null;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Invitations::class)]
    private Collection $receiver;

    public function __construct()
    {
        $this->friends = new ArrayCollection();
        $this->receiver = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

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

    public function getTrainingId(): ?Training
    {
        return $this->training_id;
    }

    public function setTrainingId(?Training $training_id): static
    {
        $this->training_id = $training_id;

        return $this;
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


    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getWorkout(): ?string
    {
        return $this->workout;
    }

    public function setWorkout(?string $workout): static
    {
        $this->workout = $workout;

        return $this;
    }

    public function getTrainingDays(): ?int
    {
        return $this->training_days;
    }

    public function setTrainingDays(?int $training_days): static
    {
        $this->training_days = $training_days;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Friends>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(Friends $friend): static
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
            $friend->addUser($this);
        }

        return $this;
    }

    public function removeFriend(Friends $friend): static
    {
        if ($this->friends->removeElement($friend)) {
            $friend->removeUser($this);
        }

        return $this;
    }

    public function getSender(): ?Invitations
    {
        return $this->sender;
    }

    public function setSender(?Invitations $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return Collection<int, Invitations>
     */
    public function getReceiver(): Collection
    {
        return $this->receiver;
    }

    public function addReceiver(Invitations $receiver): static
    {
        if (!$this->receiver->contains($receiver)) {
            $this->receiver->add($receiver);
            $receiver->setReceiver($this);
        }

        return $this;
    }

    public function removeReceiver(Invitations $receiver): static
    {
        if ($this->receiver->removeElement($receiver)) {
            // set the owning side to null (unless already changed)
            if ($receiver->getReceiver() === $this) {
                $receiver->setReceiver(null);
            }
        }

        return $this;
    }


}
