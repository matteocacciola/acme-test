<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VatClassRepository")
 */
class VatClass {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $percentage;
    
    /**
     * 
     * @param float $percentage
     */
    public function __construct(float $percentage) {
        $this->percentage = $percentage;
    }

    /**
     * 
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * 
     * @return float
     */
    public function getPercentage(): float {
        return $this->percentage;
    }

}
