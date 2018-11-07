<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("acme_vat_class")
 * @ORM\Entity(repositoryClass="App\Repository\VatClassRepository")
 */
class VatClass {
    
    const VAT_SIX_PERCENT = 0.06;
    const VAT_TWENTYONE_PERCENT = 0.21;

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
     * @return int|null
     */
    public function getId() {
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
