<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Safe\DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class), UniqueEntity('email', 'username')]
#[ApiResource(
    collectionOperations: ['get', 'post' => ['denormalization_context' => ['groups' => ['post_user']]]],
    itemOperations: ['get', 'delete', 'put'],
    denormalizationContext: [
        'skip_null_values' => false,
        'groups' => ['read_user'],
    ],
    normalizationContext: [
        'skip_null_values' => false,
        'groups' => ['read_user'],
    ],
)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    #[Groups(['read_user'])]
    private ?int $id;

    #[ORM\Column(type: Types::STRING, length: 200, unique: true)]
    #[Groups(['read_user', 'post_user'])]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 200)]
    private string $username;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_user'])]
    private ?string $name;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['read_user'])]
    private array $roles = [];

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['read_user'])]
    private bool $isEnable;

    #[Vich\UploadableField(mapping: 'user_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[Groups(['post_user'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Store::class, cascade: ['persist', 'remove'])]
    #[Groups(['read_user'])]
    private Collection $stores;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserNotificationTimer::class, cascade: ['persist', 'remove'])]
    #[Groups(['read_user'])]
    private Collection $userNotificationTimers;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
        $this->id = rand();
        $this->isEnable = false;
        $this->stores = new ArrayCollection();
        $this->userNotificationTimers = new ArrayCollection();
    }

    #[Groups(['read_user'])]
    public function getImagePath(): string
    {
        return sprintf('/images/%s', $this->getImage()->getName());
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return array<mixed>
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<mixed> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        $this->username = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getIsEnable(): bool
    {
        return $this->isEnable;
    }

    public function setIsEnable(bool $isEnable): self
    {
        $this->isEnable = $isEnable;

        return $this;
    }

    public function isIsEnable(): ?bool
    {
        return $this->isEnable;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
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

    public function getActiveStore(): Store|bool
    {
        return $this->getStores()->filter(
            function (Store $store) {
                return true === $store->isIsActive();
            }
        )->first();
    }

    /**
     * @return Collection<int, Store>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores->add($store);
            $store->setUser($this);
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        $this->stores->removeElement($store);

        return $this;
    }

    /**
     * @return Collection<int, UserNotificationTimer>
     */
    public function getUserNotificationTimers(): Collection
    {
        return $this->userNotificationTimers;
    }

    public function addUserNotificationTimer(UserNotificationTimer $userNotificationTimer): self
    {
        if (!$this->userNotificationTimers->contains($userNotificationTimer)) {
            $this->userNotificationTimers->add($userNotificationTimer);
            $userNotificationTimer->setUser($this);
        }

        return $this;
    }

    public function removeUserNotificationTimer(UserNotificationTimer $userNotificationTimer): self
    {
        if ($this->userNotificationTimers->removeElement($userNotificationTimer)) {
            // set the owning side to null (unless already changed)
            if ($userNotificationTimer->getUser() === $this) {
                $userNotificationTimer->setUser(null);
            }
        }

        return $this;
    }
}
