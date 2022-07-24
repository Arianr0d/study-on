<?php

namespace App\DTO;
use JMS\Serializer\Annotation as Serializer;

class CredUserDTO
{
    /**
     * @Serializer\Type("string")
     */
    public $username;

    /**
     * @Serializer\Type("string")
     */
    public $password;
}