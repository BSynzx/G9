<?php

abstract class Person
{
    protected string $firstName;
    protected string $middleName;
    protected string $lastName;
    protected string $address;
    protected string $contact;
    protected string $email;

    public function __construct(
        string $firstName,
        string $middleName,
        string $lastName,
        string $address,
        string $contact,
        string $email
    ) {
        $this->firstName  = $firstName;
        $this->middleName = $middleName;
        $this->lastName   = $lastName;
        $this->address    = $address;
        $this->contact    = $contact;
        $this->email      = $email;
    }

    abstract public function getRole(): string;
    abstract public function getTableName(): string;

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getFirstName(): string  { return $this->firstName; }
    public function getMiddleName(): string { return $this->middleName; }
    public function getLastName(): string   { return $this->lastName; }
    public function getAddress(): string    { return $this->address; }
    public function getContact(): string    { return $this->contact; }
    public function getEmail(): string      { return $this->email; }

    public function setFirstName(string $value): void  { $this->firstName = $value; }
    public function setMiddleName(string $value): void { $this->middleName = $value; }
    public function setLastName(string $value): void   { $this->lastName = $value; }
    public function setAddress(string $value): void    { $this->address = $value; }
    public function setContact(string $value): void    { $this->contact = $value; }
    public function setEmail(string $value): void      { $this->email = $value; }
}
