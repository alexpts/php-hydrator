<?php
declare(strict_types = 1);

class UserModel
{
    private $id;
    protected string $name;
    protected string $login;
    protected DateTime|int|null $creAt;
    protected string $email;
    protected bool $active;

    public function __construct()
    {
        $this->creAt = new DateTime;
    }

    public function __toString(): string
    {
       return (string)$this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getCreAt(): DateTime
    {
        return $this->creAt;
    }

    public function setCreAt(DateTime $creAt): void
    {
        $this->creAt = $creAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = (bool)$active;
    }

    public function getCreAtTimestamp(): int
    {
        return $this->creAt->getTimestamp();
    }

    public function getTitleName(string $title): string
    {
        return $title . ' ' . $this->name;
    }

    public function setTitleName(string $name, string $suffix): void
    {
        $this->name = $name . ' ' . $suffix;
    }
}
