<?php
namespace App\Controller\Api;

use App\Entity\Company;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/employee')]
final class EmployeeController extends CrudController
{
    protected string $entityName = 'Employee';

    /**
     * Action for getting employees by company
     *
     * @param int $id company entity id
     * @return JsonResponse
     */
    #[Route('/company/{companyId}', methods: ['GET'])]
    public function employees(int $companyId): JsonResponse
    {
        $company = $this->getEntityById(
            $companyId,
            $this->entityManager->getRepository(Company::class)
        );

        return $this->jsonSuccess($company->toArrayWithEmployees());
    }

    /**
     * Process additional entity data from request before validate and save entity
     *
     * @param array $requestData
     * @param Entity $entity
     * @return Entity
     */
    #[\Override]
    protected function processAdditionalData(array $requestData, $entity)
    {
        if (!empty($requestData['company_id'])) {
            $company = $this->getEntityById(
                $requestData['company_id'],
                $this->entityManager->getRepository(Company::class)
            );
            $entity->setCompany($company);
        }

        return $entity;
    }

}

