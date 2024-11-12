<?php
namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[UniqueEntity(['surname', 'name'])]
#[UniqueEntity(['email'])]
#[Gedmo\SoftDeleteable]
class Employee implements SerializedInterface
{
    use SoftDeleteable;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $surname = null;

    #[ORM\Column(length: 150, unique: true, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    protected $deletedAt;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'employees')]
    #[ORM\JoinColumn(name: 'company_id', nullable: false)]
    private ?Company $company = null;

    /**
     * Virtual field to get company_id from api request
     * @var int|null
     */
    #[Assert\NotBlank(groups: ['onCreate'])]
    private ?int $company_id = null;

    /**
     * Get entity data as an array
     *
     * @param bool $withRelations include relations
     * @return array
     */
    public function toArray(bool $withRelations = true): array
    {
        $data = [
            'id'      => $this->id,
            'name'    => $this->name,
            'surname' => $this->surname,
            'email'   => $this->email,
            'phone'   => $this->phone,
        ];
        if ($withRelations) {
            $data['company'] = $this->getCompany()->toArray();
        }

        return $data;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;
        
        if ($companyId = $company?->getId()) {
            $this->company_id = $companyId;
        }

        return $this;
    }

    public function getCompanyId(): ?string
    {
        return $this->company_id;
    }

    public function setCompanyId(int $companyId): static
    {
        $this->company_id = $companyId;

        return $this;
    }
}
