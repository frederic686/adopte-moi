<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $race = null;

    #[ORM\Column(length: 20)]
    private ?string $sexe = null;

    #[ORM\Column]
    private ?int $age = null;


    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datePublication = null;

    #[ORM\ManyToOne(inversedBy: 'animaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $eleveur = null;

    /**
     * @var Collection<int, DemandeAdoption>
     */
    #[ORM\OneToMany(targetEntity: DemandeAdoption::class, mappedBy: 'animal')]
    private Collection $demandeAdoptions;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    private ?Type $type = null;

    public function __construct()
    {
        $this->demandeAdoptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
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

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(?string $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeImmutable
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeImmutable $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getEleveur(): ?User
    {
        return $this->eleveur;
    }

    public function setEleveur(?User $eleveur): static
    {
        $this->eleveur = $eleveur;

        return $this;
    }

    /**
     * @return Collection<int, DemandeAdoption>
     */
    public function getDemandeAdoptions(): Collection
    {
        return $this->demandeAdoptions;
    }

    public function addDemandeAdoption(DemandeAdoption $demandeAdoption): static
    {
        if (!$this->demandeAdoptions->contains($demandeAdoption)) {
            $this->demandeAdoptions->add($demandeAdoption);
            $demandeAdoption->setAnimal($this);
        }

        return $this;
    }

    public function removeDemandeAdoption(DemandeAdoption $demandeAdoption): static
    {
        if ($this->demandeAdoptions->removeElement($demandeAdoption)) {
            // set the owning side to null (unless already changed)
            if ($demandeAdoption->getAnimal() === $this) {
                $demandeAdoption->setAnimal(null);
            }
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }
}
