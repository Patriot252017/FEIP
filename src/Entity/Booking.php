<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Cottage::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private Cottage $cottage;

    #[ORM\Column(type: 'string', length: 20)]
    private string $phone;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
{
    return $this->id;
}

public function getPhone(): string
{
    return $this->phone;
}

public function setPhone(string $phone): self
{
    $this->phone = $phone;
    return $this;
}

public function getComment(): ?string
{
    return $this->comment;
}

public function setComment(?string $comment): self
{
    $this->comment = $comment;
    return $this;
}

public function getCreatedAt(): \DateTimeInterface
{
    return $this->createdAt;
}

public function getCottage(): Cottage
{
    return $this->cottage;
}

public function setCottage(Cottage $cottage): self
{
    $this->cottage = $cottage;
    return $this;
}

}