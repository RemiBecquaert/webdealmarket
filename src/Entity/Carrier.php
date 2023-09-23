<?php

namespace App\Entity;

use App\Repository\CarrierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarrierRepository::class)]
class Carrier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixFactureSup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function __toString(){
        return '[b]'.$this->getNom().'[br]'.'[/b]'.'[em]'.$this->getDescription().'[/em]'.'[br]'.number_format($this->getPrix(), 2, ',', ',').'€';   
    }

    public function getPrixFactureSup(): ?float
    {
        return $this->prixFactureSup;
    }

    public function setPrixFactureSup(?float $prixFactureSup): self
    {
        $this->prixFactureSup = $prixFactureSup;

        return $this;
    }

    }
