<?php
namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\{HttpException, NotFoundHttpException};
use App\Entity\SerializedInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;

/**
 * Base class for all crud api controllers
 */
abstract class CrudController extends AbstractController
{
    /**
     * Name of entity that should be set in final controller
     * @var string
     */
    protected string $entityName;

    protected string $entityClassName;

    protected string $entityFormName;

    protected ServiceEntityRepository $repository;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
        if (empty($this->entityName)) {
            throw new Exception(get_class($this) . ' must have a $entityName property value');
        }

        $this->entityClassName = '\App\Entity\\' . $this->entityName;
        $this->entityFormName  = '\App\Form\\' . $this->entityName . 'Type';
        
        $this->repository      = $entityManager->getRepository($this->entityClassName);
    }

    /**
     * Action to get list of entities
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/list', methods: ['GET'])]
    public function index(Request $request,): JsonResponse
    {
        $limit  = $request->get('limit');
        $offset = $request->get('offset');

        $data = [];
        foreach ($this->repository->findBy(criteria: [], limit: $limit, offset: $offset) as $entity) {
            $data[] = $entity->toArray();
        }
        
        return $this->jsonSuccess($data);
    }

    /**
     * Action to create new entity
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/new', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->disableSoftDeleted();

        $entity = new $this->entityClassName();
        $form = $this->createForm($this->entityFormName, $entity);
        $requestData = json_decode($request->getContent(),true);
        $form->submit($requestData);
        $this->processAdditionalData($requestData, $entity);

        if (!count($errors = $validator->validate($entity, null, [$this->entityName, 'onCreate']))) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return $this->jsonSuccess($entity);
        }

        return $this->jsonError(data: $errors);
    }

    /**
     * Action for getting single entity data
     *
     * @param int $id entity id
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['GET'])]
    public function read(int $id): JsonResponse
    {
        $entity = $this->getEntityById($id);
        if (!$entity = $this->repository->findOneById($id)) {
            return $this->jsonError($this->entityName . ' is not found for id=' . $id, 404);
        }

        return $this->jsonSuccess($entity);
    }

    /**
     * Action for updating single entity data
     *
     * @param int $id entity id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function edit(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->disableSoftDeleted();

        $entity = $this->getEntityById($id);
        $form = $this->createForm($this->entityFormName, $entity);
        $requestData = json_decode($request->getContent(),true);
        $form->submit($requestData, false);
        $this->processAdditionalData($requestData, $entity);

        if (!count($errors = $validator->validate($entity))) {
            $this->entityManager->flush();
            return $this->jsonSuccess($entity);
        }

        return $this->jsonError(data: $errors);
    }

    /**
     * Action for deleting entity
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, Request $request): JsonResponse
    {
        $entity = $this->getEntityById($id);
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return $this->jsonSuccess([], 'Deleted');
    }

    /**
     * Find entity by id
     *
     * @param int $id
     * @return Entity
     * @throws NotFoundHttpException
     */
    protected function getEntityById(int $id,
            \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface $repository = null)
    {
        if (empty($repository)) {
            $repository = $this->repository;
        }
        if (!$entity = $repository->findOneById($id)) {
            $entityName = str_replace('Repository', '', (new \ReflectionClass($repository))->getShortName());
            throw new NotFoundHttpException($entityName . ' is not found for id=' . $id);
        }

        return $entity;
    }

    /**
     * Get JsonResponse for error response
     *
     * @param string $message
     * @param int $errorCode http error code
     * @param mixed $data
     * @return JsonResponse
     */
    protected function jsonError(string $message = '', int $errorCode = 500, mixed $data = [])
    {
        return $this->json(
            $this->getResponseData(
                status:  false,
                message: $message,
                data:    $data,
            ),
            $errorCode
        );
    }

    /**
     * Get JsonResponse for success response
     * 
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function jsonSuccess(mixed $data = [], string $message = '')
    {
        return $this->json(
            $this->getResponseData(
                status:  true,
                message: $message,
                data:    $data,
            ),
            200
        );
    }

    /**
     * Get formatted data for response
     *
     * @param mixed $data
     * @param string $message
     * @param bool $status
     * @return array
     */
    private function getResponseData(mixed $data = [], string $message = '', bool $status = true)
    {
        $response = [
            'status'  => $status,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data) {
            $response['data'] = $this->serializeData($data);
        }

        return $response;
    }

    /**
     * Serialize data
     * 
     * @param mixed $data
     * @return mixed
     */
    protected function serializeData(mixed $data)
    {
        if ($data instanceof SerializedInterface) {
            $data = $data->toArray();
        }

        return $data;
    }

    /**
     * Disable soft deleted filter
     */
    protected function disableSoftDeleted()
    {
        if ($this->entityManager->getFilters()->isEnabled('soft_delete')) {
            $this->entityManager->getFilters()->disable('soft_delete');
        }
    }

    /**
     * Process additional entity data from request before validate and save entity. To use in extended classes
     *
     * @param array $requestData
     * @param Entity $entity
     * @return Entity
     */
    protected function processAdditionalData(array $requestData, $entity)
    {
        return $entity;
    }
}