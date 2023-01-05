<?php

namespace App\Entity;

use App\Repository\ConnexionUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnexionUserRepository::class)]
class ConnexionUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'connexionUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateConnexion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDeconnexion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getDateConnexion(): ?\DateTimeInterface
    {
        return $this->dateConnexion;
    }

    public function setDateConnexion(\DateTimeInterface $dateConnexion): self
    {
        $this->dateConnexion = $dateConnexion;

        return $this;
    }

    public function getDateDeconnexion(): ?\DateTimeInterface
    {
        return $this->dateDeconnexion;
    }

    public function setDateDeconnexion(?\DateTimeInterface $dateDeconnexion): self
    {
        $this->dateDeconnexion = $dateDeconnexion;

        return $this;
    }
}
