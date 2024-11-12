<?php
namespace App\Tests\Controller;

use App\Entity\{Company, Employee};
use App\Tests\{CompanyTrait, EmployeeTrait};

final class EmployeeApiControllerTest extends ApiController
{
    use CompanyTrait, EmployeeTrait;

    protected string $path = '/api/employee/';

    protected string $entityName = 'Employee';

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearData(Company::class);
        $this->clearData(Employee::class);

        $this->repository = $this->manager->getRepository(Employee::class);
    }

    public function testIndex(): void
    {
        $entity       = $this->createEmployee(1);
        $entity2      = $this->createEmployee(2);

        $this->client->request('GET', $this->path . 'list');
        $responseData = $this->getResponseData();

        self::assertResponseStatusCodeSame(200);
        self::assertSame(true, $responseData['status']);
        self::assertSame(2,    count($responseData['data']));

        $this->client->request('GET', $this->path . 'list?limit=1');
        $responseData = $this->getResponseData();
        self::assertSame(1, count($responseData['data']));
    }

    public function testCreate(): void
    {
        $companyData = $this->getCompanyDummyData(2);
        $company     = $this->createEntity('Company', $companyData);

        $employeeData = $this->getEmployeeDummyData(2, $company);

        $this->client->request(
            method: 'POST',
            uri: $this->path . 'new',
            content: $this->json($employeeData)
        );
        $responseData = $this->getResponseData()['data'];

        $company = $this->repository->findOneById($responseData['id']);

        self::assertResponseStatusCodeSame(200);
        self::assertSame($employeeData['name'],    $company->getName());
        self::assertSame($employeeData['surname'], $company->getSurname());
        self::assertSame($employeeData['email'],   $company->getEmail());
        self::assertSame($employeeData['phone'],   $company->getPhone());
    }

    public function testRead(): void
    {
        $entity = $this->createEmployee(3);

        $this->client->request(method: 'GET', uri: $this->path . $entity->getId());
        $responseData = $this->getResponseData()['data'];

        self::assertResponseStatusCodeSame(200);
        self::assertSame($responseData['name'],    $entity->getName());
        self::assertSame($responseData['surname'], $entity->getSurname());
        self::assertSame($responseData['email'],   $entity->getEmail());
        self::assertSame($responseData['phone'],   $entity->getPhone());
    }

    public function testEdit(): void
    {
        $entity = $this->createEmployee(4);

        $employeeDataEdit = $this->getEmployeeDummyData(5, $entity->getCompany());

        $this->client->request(
            method: 'PUT',
            uri: $this->path . $entity->getId(),
            content: $this->json($employeeDataEdit)
        );
        $responseData = $this->getResponseData()['data'];
        $employee     = $this->repository->findOneById($responseData['id']);

        self::assertResponseStatusCodeSame(200);
        self::assertSame($responseData['name'],    $employee->getName());
        self::assertSame($responseData['surname'], $employee->getSurname());
        self::assertSame($responseData['email'],   $employee->getEmail());
        self::assertSame($responseData['phone'],   $employee->getPhone());
    }

    public function testDelete(): void
    {
        if (!$this->manager->getFilters()->isEnabled('soft_delete')) {
            $this->manager->getFilters()->enable('soft_delete');
        }

        $entity = $this->createEmployee(5);
        $employee     = $this->repository->findOneById($entity->getId());
        self::assertNotEmpty($employee);

        $this->client->request(method: 'DELETE', uri: $this->path . $entity->getId());

        $employee = $this->repository->findOneById($entity->getId());
        self::assertEmpty($employee);

        $this->manager->getFilters()->disable('soft_delete');
    }

    public function testEmployees(): void
    {
        $entity = $this->createEmployee(6);

        $this->manager->clear();
        $this->client->request(method: 'GET', uri: $this->path . 'company/' . $entity->getCompany()->getId());
        $responseData = $this->getResponseData()['data']['employees'][0];

        self::assertResponseStatusCodeSame(200);
        self::assertSame($responseData['name'],    $entity->getName());
        self::assertSame($responseData['surname'], $entity->getSurname());
        self::assertSame($responseData['email'],   $entity->getEmail());
        self::assertSame($responseData['phone'],   $entity->getPhone());
    }

    /**
     * Create and save employee in database
     *
     * @param int $prefix
     * @return Employee
     */
    private function createEmployee(int $prefix)
    {
        $companyData = $this->getCompanyDummyData($prefix);
        $company     = $this->createEntity('Company', $companyData);

        $employeeData = $this->getEmployeeDummyData($prefix, $company);
        $employee     = $this->createEntity($this->entityName, $employeeData, doFlush: false);

        $employee->setCompany($company);
        $this->manager->flush();

        return $employee;
    }
}