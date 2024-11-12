<?php
namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[UniqueEntity(['name', 'nip'])]
#[UniqueEntity(['nip'])]
#[Gedmo\SoftDeleteable]
class Company implements SerializedInterface
{
    use SoftDeleteable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 10, unique: true, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[0-9]{10}$/')]
    private ?string $nip = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $address = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column(length: 6, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[0-9]{2}-[0-9]{3}$/')]
    private ?string $postal_code = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    protected $deletedAt;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'company', orphanRemoval: true)]
    private Collection $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'nip'         => $this->nip,
            'address'     => $this->address,
            'city'        => $this->city,
            'postal_code' => $this->postal_code,
        ];
    }

    public function toArrayWithEmployees()
    {
        $data = $this->toArray();
        $data['employees'] = [];
        foreach ($this->getEmployees() as $employee) {
            $data['employees'][] = $employee->toArray(withRelations: false);
        }

        return $data;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setNip(?string $nip): static
    {
        $this->nip = $nip;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setCompany($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getCompany() === $this) {
                $employee->setCompany(null);
            }
        }

        return $this;
    }
}
