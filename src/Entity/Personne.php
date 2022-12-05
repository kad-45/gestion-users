<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use App\Traits\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Personne
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message:"Veuillez renseigner ce champ")]
    #[Assert\Length(
        min: 4, 
        max: 12, 
        minMessage:"Veuillez renseigner au moins 4 caractères",
        maxMessage:"Veuillez renseigner au plus 12 caractères"
        )]
    private ?string $firstname;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message:"Veuillez renseigner ce champ")]
    #[Assert\Length(
        min: 4, 
        max: 20, 
        minMessage:"Veuillez renseigner au moins 4 caractères",
        maxMessage:"Veuillez renseigner au plus 20 caractères"
        )]

    protected ?string $name;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $age;

     
    #[ORM\OneToOne(targetEntity: Profil::class, inversedBy: "personne", cascade:["persist", "remove"])]
     
    private $profil;

    #[ORM\ManyToMany(targetEntity: Hobies::class)]
    private Collection $hobies;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'personnes')]
    private ?Job $job;

    #[ORM\Column(type:'string', length: 255, nullable: true)]
    private ?string $image;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->hobies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * @return Collection<int, Hobies>
     */
    public function getHobies(): Collection
    {
        return $this->hobies;
    }

    public function addHobies(Hobies $hobies): self
    {
        if (!$this->hobies->contains($hobies)) {
            $this->hobies->add($hobies);
        }

        return $this;
    }

    public function removeHobies(Hobies $hobies): self
    {
        $this->hobies->removeElement($hobies);

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    } 

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }


   
    
}