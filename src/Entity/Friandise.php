<?php

namespace App\Entity;

use App\Repository\FriandiseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriandiseRepository::class)]
class Friandise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // App/Entity/Friandise.php (extrait)
    #[ORM\ManyToOne(inversedBy: 'friandises')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Animal $animal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // ⬅️ supprimé: onDelete: 'CASCADE'
    private ?User $envoyeur = null;


    #[ORM\Column(length: 20)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getEnvoyeur(): ?User
    {
        return $this->envoyeur;
    }

    public function setEnvoyeur(?User $envoyeur): static
    {
        $this->envoyeur = $envoyeur;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
