<?php

namespace App\DTO;

use symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    public string $title= '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email= '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 200)]
    public string $message= '';
}