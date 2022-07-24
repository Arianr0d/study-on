<?php

namespace App\DTO;
use JMS\Serializer\Annotation as Serializer;

class CredUserDTO
{
    /**
     * @Serializer\Type("string")
     */
    private $username;

    /**
     * @Serializer\Type("string")
     */
    private $password;
}