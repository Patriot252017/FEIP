<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\CottageRepository;

#[ORM\Entity(repositoryClass: CottageRepository::class)]
class Cottage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $amenities = null;

    #[ORM\Column(type: 'integer')]
    private int $beds;

    #[ORM\Column(type: 'integer')]
    private int $distanceFromSea;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'cottage')]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmenities(): ?string
    {
        return $this->amenities;
    }

    public function setAmenities(?string $amenities): self
    {
        $this->amenities = $amenities;
        return $this;
    }

    public function getBeds(): int
    {
        return $this->beds;
    }

    public function setBeds(int $beds): self
    {
        $this->beds = $beds;
        return $this;
    }

    public function getDistanceFromSea(): int
    {
        return $this->distanceFromSea;
    }

    public function setDistanceFromSea(int $distanceFromSea): self
    {
        $this->distanceFromSea = $distanceFromSea;
        return $this;
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }
}