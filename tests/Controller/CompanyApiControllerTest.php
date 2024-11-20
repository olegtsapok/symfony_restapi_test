<?php
namespace App\Tests\Controller;

use App\Entity\Company;
use App\Tests\Traits\CompanyTrait;

final class CompanyApiControllerTest extends ApiController
{
    use CompanyTrait;
    
    protected string $path = '/api/company/';

    protected string $entityName = 'Company';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->clearData(Company::class);

        $this->repository = $this->manager->getRepository(Company::class);
    }

    public function testIndex(): void
    {
        $companyData = $this->getCompanyDummyData(1);
        $entity      = $this->createEntity($this->entityName, $companyData);
        $companyData = $this->getCompanyDummyData(2);
        $entity      = $this->createEntity($this->entityName, $companyData);

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
        $this->client->request(
            method: 'POST',
            uri: $this->path . 'new',
            content: $this->json($companyData)
        );
        $responseData = $this->getResponseData()['data'];

        $company = $this->repository->findOneById($responseData['id']);

        self::assertResponseStatusCodeSame(200);
        self::assertSame($companyData['name'],        $company->getName());
        self::assertSame($companyData['nip'],         $company->getNip());
        self::assertSame($companyData['address'],     $company->getAddress());
        self::assertSame($companyData['city'],        $company->getCity());
        self::assertSame($companyData['postal_code'], $company->getPostalCode());
    }

    public function testRead(): void
    {
        $companyData = $this->getCompanyDummyData(3);
        $entity      = $this->createEntity($this->entityName, $companyData);

        $this->client->request(method: 'GET', uri: $this->path . $entity->getId());
        $responseData = $this->getResponseData()['data'];

        self::assertResponseStatusCodeSame(200);
        self::assertSame($companyData['name'],        $responseData['name']);
        self::assertSame($companyData['nip'],         $responseData['nip']);
        self::assertSame($companyData['address'],     $responseData['address']);
        self::assertSame($companyData['city'],        $responseData['city']);
        self::assertSame($companyData['postal_code'], $responseData['postal_code']);
    }

    public function testEdit(): void
    {
        $companyData = $this->getCompanyDummyData(4);
        $entity      = $this->createEntity($this->entityName, $companyData);

        $companyDataEdit = $this->getCompanyDummyData(5);

        $this->client->request(
            method: 'PUT',
            uri: $this->path . $entity->getId(),
            content: $this->json($companyDataEdit)
        );
        $responseData = $this->getResponseData()['data'];
        $company      = $this->repository->findOneById($responseData['id']);

        self::assertResponseStatusCodeSame(200);
        self::assertSame($companyDataEdit['name'],        $company->getName());
        self::assertSame($companyDataEdit['nip'],         $company->getNip());
        self::assertSame($companyDataEdit['address'],     $company->getAddress());
        self::assertSame($companyDataEdit['city'],        $company->getCity());
        self::assertSame($companyDataEdit['postal_code'], $company->getPostalCode());
    }

    public function testDelete(): void
    {
        $companyData = $this->getCompanyDummyData(6);
        $entity      = $this->createEntity($this->entityName, $companyData);
        $company     = $this->repository->findOneById($entity->getId());
        self::assertNotEmpty($company);

        $this->client->request(method: 'DELETE', uri: $this->path . $entity->getId());

        $company = $this->repository->findOneById($entity->getId());
        self::assertEmpty($company);
    }
}
