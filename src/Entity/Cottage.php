<?php

namespace App\Entity;

class Cottage
{
    private ?int $id = null;
    private ?string $amenities = null;
    private ?int $beds = null;
    private ?int $distanceFromSea = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getAmenities(): ?string
    {
        return $this->amenities;
    }

    public function setAmenities(string $amenities): self
    {
        $this->amenities = $amenities;
        return $this;
    }

    public function getBeds(): ?int
    {
        return $this->beds;
    }

    public function setBeds(int $beds): self
    {
        $this->beds = $beds;
        return $this;
    }

    public function getDistanceFromSea(): ?int
    {
        return $this->distanceFromSea;
    }

    public function setDistanceFromSea(int $distanceFromSea): self
    {
        $this->distanceFromSea = $distanceFromSea;
        return $this;
    }
}