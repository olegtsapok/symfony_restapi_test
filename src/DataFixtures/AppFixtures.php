<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    private function setManager(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function load(ObjectManager $manager): void
    {
        $this->setManager($manager);

        $company = $this->createCompany([
            'name' => 'name1',
            'nip' => '123',
            'city' => 'city1',
            'address' => 'address1',
            'postal_code' => '45-123',
        ]);
        $employee = $this->createEmployee([
            'name' => 'name1',
            'surname' => 'surname1',
            'email' => 'email@mail.com',
            'phone' => '123456',
            'company' => $company,
        ]);

        $user = new User();
        $user->setName('api_user');
        $user->setPassword('test_pass');
        $user->setRole(['ROLE_API']);
        $user->setToken('api_user_token');
        $this->manager->persist($user);

        $this->manager->flush();
    }

    private function createCompany($data): Company
    {
        $fixture = new Company();
        $fixture->setName($data['name']);
        $fixture->setNip($data['nip']);
        $fixture->setCity($data['city']);
        $fixture->setAddress($data['address']);
        $fixture->setPostalCode($data['postal_code']);
        $this->manager->persist($fixture);

        return $fixture;
    }

    private function createEmployee($data): Employee
    {
        $fixture = new Employee();
        $fixture->setName($data['name']);
        $fixture->setSurname($data['surname']);
        $fixture->setEmail($data['email']);
        $fixture->setPhone($data['phone']);
        $fixture->setCompany($data['company']);
        $this->manager->persist($fixture);

        return $fixture;
    }
}
