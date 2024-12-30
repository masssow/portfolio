<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity]
#[Vich\Uploadable]

class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[Vich\UploadableField(mapping: 'posts', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;


    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'post')]
    private Collection $messages;

    /**
     * @var Collection<int, CategoriePosts>
     */
    #[ORM\ManyToMany(targetEntity: CategoriePosts::class, inversedBy: 'posts')]
    private Collection $categoriePosts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageTwoName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageTwoSize = null;

    #[Vich\UploadableField(mapping: 'posts', fileNameProperty: 'imageTwoName')]
    private ?File $imageTwoFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageThreeName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageThreeSize = null;

    #[Vich\UploadableField(mapping: 'posts', fileNameProperty: 'imageThreeName')]
    private ?File $imageThreeFile = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphe2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphe3 = null;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->categoriePosts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): static
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageTwoFile(?File $imageTwoFile = null): void
    {
        $this->imageTwoFile = $imageTwoFile;

        if (null !== $imageTwoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageTwoFile(): ?File
    {
        return $this->imageTwoFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageThreeFile(?File $imageThreeFile = null): void
    {
        $this->imageThreeFile = $imageThreeFile;

        if (null !== $imageThreeFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageThreeFile(): ?File
    {
        return $this->imageThreeFile;
    }


    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setPost($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getPost() === $this) {
                $message->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategoriePosts>
     */
    public function getCategoriePosts(): Collection
    {
        return $this->categoriePosts;
    }

    public function addCategoriePost(CategoriePosts $categoriePost): static
    {
        if (!$this->categoriePosts->contains($categoriePost)) {
            $this->categoriePosts->add($categoriePost);
        }

        return $this;
    }

    public function removeCategoriePost(CategoriePosts $categoriePost): static
    {
        $this->categoriePosts->removeElement($categoriePost);

        return $this;
    }

    public function getImagetwoName(): ?string
    {
        return $this->imageTwoName;
    }

    public function setImagetwoName(?string $imagetwoName): static
    {
        $this->imageTwoName = $imagetwoName;

        return $this;
    }

    public function getImageTwoSize(): ?int
    {
        return $this->imageTwoSize;
    }

    public function setImageTwoSize(?int $imageTwoSize): static
    {
        $this->imageTwoSize = $imageTwoSize;

        return $this;
    }

    public function getImageThreeName(): ?string
    {
        return $this->imageThreeName;
    }

    public function setImageThreeName(?string $imageThreeName): static
    {
        $this->imageThreeName = $imageThreeName;

        return $this;
    }

    public function getImageThreeSize(): ?int
    {
        return $this->imageThreeSize;
    }

    public function setImageThreeSize(?int $imageThreeSize): static
    {
        $this->imageThreeSize = $imageThreeSize;

        return $this;
    }

    public function getParagraphe2(): ?string
    {
        return $this->paragraphe2;
    }

    public function setParagraphe2(?string $paragraphe2): static
    {
        $this->paragraphe2 = $paragraphe2;

        return $this;
    }

    public function getParagraphe3(): ?string
    {
        return $this->paragraphe3;
    }

    public function setParagraphe3(?string $paragraphe3): static
    {
        $this->paragraphe3 = $paragraphe3;

        return $this;
    }

}
