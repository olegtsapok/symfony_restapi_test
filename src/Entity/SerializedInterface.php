<?php
namespace App\Entity;

interface SerializedInterface
{
    public function toArray(): array;
}