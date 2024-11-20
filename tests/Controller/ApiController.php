<?php
namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormFactoryInterface;
use App\Entity\User;

abstract class ApiController extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $manager;
    protected EntityRepository $repository;
    protected FormFactoryInterface $formFactory;

    /**
     * Auto run before tests
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->formFactory = Forms::createFormFactoryBuilder()->getFormFactory();

        $this->removeListener('onFlush', '\Gedmo\SoftDeleteable\SoftDeleteableListener');
        $this->loginApiUser();
    }

    public function removeListener(string $eventName, string $listenerName)
    {
        foreach ($this->manager->getEventManager()->getListeners($eventName) as $listener) {
            if ($listener instanceof $listenerName) {
                // remove the SoftDeletableSubscriber event listener
                $this->manager->getEventManager()->removeEventListener($eventName, $listener);
            }
        }
    }

    protected function loginApiUser()
    {
        $this->clearData(User::class);
        
        $user = new User();
        $user->setName('api_user');
        $user->setPassword('test_pass');
        $user->setRole(['ROLE_API']);
        $user->setToken('api_user_token');
        $this->manager->persist($user);
        $this->manager->flush();
        $this->client->loginUser($user);
    }

    protected function clearData(string $entityName)
    {
        $this->repository = $this->manager->getRepository($entityName);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
            $this->manager->flush();

            $this->clearSoftDeleted($object);
        }
    }

    private function clearSoftDeleted($object)
    {
        $this->manager->remove($object);
        $this->manager->flush();
    }

    protected function getResponseData(): ?array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    /**
     * Get raw json
     * @param array $data
     * @return string
     */
    protected function json(array $data): string
    {
        return json_encode($data);
    }

    protected function createEntity(string $entityName, array $data, bool $doFlush = true)
    {
        $entity = new ('\App\Entity\\' . $entityName)();
        $form   = $this->formFactory->create('\App\Form\\' . $entityName . 'Type', $entity);
        $form->submit($data);
        $this->manager->persist($entity);
        if ($doFlush) {
            $this->manager->flush();
        }
        return $entity;
    }
}