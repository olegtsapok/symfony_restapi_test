<?php
namespace App\Tests;

trait CompanyTrait
{
    protected function getCompanyDummyData(int $prefix)
    {
        $companyData = [
            'name'        => 'name_' . $prefix,
            'nip'         => '1234567' . (999 - $prefix),
            'address'     => 'address_' . $prefix,
            'city'        => 'city_' . $prefix,
            'postal_code' => '12-' . (999 - $prefix),
        ];

        return $companyData;
    }
}