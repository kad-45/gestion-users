<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use App\Traits\TimeStampTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Profil
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id;

    #[ORM\Column(type:'string', length: 255)]
    private ?string $url;

    #[ORM\Column(type:'string', length: 50)]
    private ?string $rs;

    #[ORM\OneToOne(targetEntity:Personne::class, mappedBy: 'profil', cascade: ['persist', 'remove'])]
    private ?Personne $personne;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRs(): ?string
    {
        return $this->rs;
    }

    public function setRs(string $rs): self
    {
        $this->rs = $rs;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(Personne $personne): self
    {
          // unset the owning side of the relation if necessary
          if ($personne === null && $this->personne !== null) {
            $this->personne->setProfil(null);
        }

        // set the owning side of the relation if necessary
        if ($personne !== null && $personne->getProfil() !== $this) {
            $personne->setProfil($this);
        }

        $this->personne = $personne;

        return $this;
    }

    public function __toString()
    {
        return $this->rs. " " .$this->url;
    }

}