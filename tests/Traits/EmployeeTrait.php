<?php
namespace App\Tests\Traits;

trait EmployeeTrait
{
    protected function getEmployeeDummyData(int $prefix, $company)
    {
        $entityData = [
            'name'       => 'name_' . $prefix,
            'surname'    => 'surname_' . $prefix,
            'email'      => 'email_' . $prefix . '@mail.com',
            'phone'      => '123456' . $prefix,
            'company_id' => $company->getId(),
        ];

        return $entityData;
    }
}