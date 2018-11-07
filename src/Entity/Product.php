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
     * @var boolean
     * @ORM\Column(type="boolean", name="is_taxable", nullable=false, options={"default":true})
     */
    private $taxable = true;

    /**
     * 
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getBarcode() {
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
    public function getName() {
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
    public function getCost() {
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
    public function getVatClass() {
        return $this->vatClass;
    }

    /**
     * 
     * @param \App\Entity\VatClass $vatClass
     * @return \self
     */
    public function setVatClass(VatClass $vatClass): self {
        $this->vatClass = $vatClass;
        
        $this->setTaxable(true);

        return $this;
    }

    /**
     * 
     * @return array
     */
    public function serialize(): array {
        return array(
            'barcode' => $this->barcode,
            'name' => $this->name,
            'cost' => $this->cost,
            'vat' => $this->getVatClass()->getPercentage()
        );
    }

    /**
     * 
     * @return float
     */
    public function getCurrentPrice(): float {
        return $this->cost;
    }

    /**
     * @param boolean $taxable
     * @return Product
     */
    public function setTaxable($taxable) {
        $this->taxable = $taxable;
        
        return $this;
    }

    /**
     * @return boolean
     */
    public function getTaxable() {
        return $this->taxable;
    }

    /**
     * 
     * @return boolean
     */
    public function isTaxable() {
        return $this->getTaxable();
    }
    
    /**
     * 
     * @return float
     */
    public function getTaxedPrice() {
        return $this->cost * (1 + $this->getVatClass()->getPercentage());
    }

}
