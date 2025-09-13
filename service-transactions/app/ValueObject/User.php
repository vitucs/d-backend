<?php
namespace App\ValueObject;

class User
{
    public readonly int $id;
    public readonly string $email;
    public readonly string $type;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->type = $data['type'];
    }
}