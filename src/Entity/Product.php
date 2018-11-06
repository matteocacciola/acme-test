<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $barcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $cost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\VatClass")
     * @ORM\JoinColumn(name="vat_class", nullable=false)
     */
    private $vatClass;

    /**
     * 
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getBarcode(): string {
        return $this->barcode;
    }

    /**
     * 
     * @param string $barcode
     * @return \self
     */
    public function setBarcode(string $barcode): self {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * 
     * @param string $name
     * @return \self
     */
    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * 
     * @return float
     */
    public function getCost(): float {
        return $this->cost;
    }

    /**
     * 
     * @param float $cost
     * @return \self
     */
    public function setCost(float $cost): self {
        $this->cost = $cost;

        return $this;
    }

    /**
     * 
     * @return \App\Entity\VatClass
     */
    public function getVatClass(): VatClass {
        return $this->vatClass;
    }

    /**
     * 
     * @param \App\Entity\VatClass $vatClass
     * @return \self
     */
    public function setVatClass(VatClass $vatClass): self {
        $this->vatClass = $vatClass;

        return $this;
    }

}