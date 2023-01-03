<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieProduit $idCategorie = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MarqueProduit $idMarque = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EtatProduit $idEtat = null;

    #[ORM\Column]
    private ?int $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getIdCategorie(): ?CategorieProduit
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(?CategorieProduit $idCategorie): self
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }

    public function getIdMarque(): ?MarqueProduit
    {
        return $this->idMarque;
    }

    public function setIdMarque(?MarqueProduit $idMarque): self
    {
        $this->idMarque = $idMarque;

        return $this;
    }

    public function getIdEtat(): ?EtatProduit
    {
        return $this->idEtat;
    }

    public function setIdEtat(?EtatProduit $idEtat): self
    {
        $this->idEtat = $idEtat;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
}
