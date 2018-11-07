<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromotionRepository")
 */
class Promotion {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     *
     * @ORM\Column(name="promotion_type", type="string", length=255, nullable=false)
     */
    private $promotionType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="applied_on_all_products", type="boolean", nullable=true)
     */
    private $appliedOnAllProducts = false;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Product")
     * @ORM\JoinTable(name="promotion_concerned_products",
     *      joinColumns = {@ORM\JoinColumn(name="promotion_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns = {@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     *
     */
    private $concernedProducts;

    /**
     * @var bool
     * 
     * @ORM\Column(name="discount_in_percentage", type="boolean", nullable=true)
     */
    private $discountInPercentage = true;

    /**
     * @var float
     * 
     * @ORM\Column(name="discount_value", type="float", nullable=true)
     */
    private $discountValue;

    // next two are for XForY

    /**
     * @var integer
     * 
     * @ORM\Column(name="required_quantity", type="integer", nullable=true)
     */
    private $requiredQuantity;

    /**
     * @var float
     * 
     * @ORM\Column(name="resulting_price", type="float", nullable=true)
     */
    private $resultingPrice;

    /**
     * @var float
     * 
     * @ORM\Column(name="resulting_quantity", type="float", nullable=true)
     */
    private $resultingQuantity;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="create_date", type="datetime", nullable=true)
     */
    private $createDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delete_date", type="datetime", nullable=true)
     */
    private $deleteDate;

    /**
     * @var boolean
     * @ORM\Column(name="only_on_taxable_items", type="boolean", nullable=true)
     */
    private $onlyOnTaxableItems;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinTable(name="consequent_discounted_product",
     *      joinColumns = {@ORM\JoinColumn(name="promotion_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns = {@ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     *
     */
    private $consequentDiscountedProduct = null;

    /**
     * @var integer
     * @ORM\Column(name="consequent_applied_quantity", type="integer", nullable = true)
     */
    private $consequentAppliedQuantity = 0;

    /**
     * @var bool
     * 
     * @ORM\Column(name="is_mix_and_match", type="boolean", nullable=true)
     */
    private $mixAndMatch = true;

    /**
     * 
     */
    public function __construct() {
        $this->concernedProducts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $promotionType
     * @return Promotion
     */
    public function setPromotionType($promotionType) {
        $this->promotionType = $promotionType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPromotionType() {
        return $this->promotionType;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $concernedProducts
     * @return Promotion
     */
    public function setConcernedProducts($concernedProducts) {
        $this->concernedProducts = $concernedProducts;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConcernedProducts() {
        return $this->concernedProducts;
    }

    /**
     * @param boolean $discountInPercentage
     * @return Promotion
     */
    public function setDiscountInPercentage($discountInPercentage) {
        $this->discountInPercentage = $discountInPercentage;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDiscountInPercentage() {
        return $this->discountInPercentage;
    }

    /**
     * @param float $discountValue
     * @return Promotion
     */
    public function setDiscountValue($discountValue) {
        $this->discountValue = $discountValue;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountValue() {
        return $this->discountValue;
    }

    /**
     * @param \DateTime $endDate
     * @return Promotion
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param string $name
     * @return Promotion
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->getName();
    }

    /**
     * @param int $requiredQuantity
     * @return Promotion
     */
    public function setRequiredQuantity($requiredQuantity) {
        $this->requiredQuantity = $requiredQuantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequiredQuantity() {
        return $this->requiredQuantity;
    }

    /**
     * @param float $resultingPrice
     * @return Promotion
     */
    public function setResultingPrice($resultingPrice) {
        $this->resultingPrice = $resultingPrice;
        return $this;
    }

    /**
     * @return float
     */
    public function getResultingPrice() {
        return $this->resultingPrice;
    }

    /**
     * @param \DateTime $startDate
     * @return Promotion
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param boolean $status
     * @return Promotion
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * 
     * @return boolean
     */
    public function isActive() {
        return $this->getStatus();
    }

    /**
     * @param boolean $appliedOnAllProducts
     * @return Promotion
     */
    public function setAppliedOnAllProducts($appliedOnAllProducts) {
        $this->appliedOnAllProducts = $appliedOnAllProducts;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAppliedOnAllProducts() {
        return $this->appliedOnAllProducts;
    }

    /**
     * 
     * @return boolean
     */
    public function isAppliedOnAllProducts() {
        return $this->getAppliedOnAllProducts();
    }

    /**
     * @param \DateTime $createDate
     * @return Promotion
     */
    public function setCreateDate($createDate) {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->createDate;
    }

    /**
     * @param mixed $updateDate
     * @return Promotion
     */
    public function setUpdateDate($updateDate) {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateDate() {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $deleteDate
     * @return Promotion
     */
    public function setDeleteDate($deleteDate) {
        $this->deleteDate = $deleteDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeleteDate() {
        return $this->deleteDate;
    }

    /**
     * 
     * @return boolean
     */
    public function isCurrentlyActive() {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        if ($this->isActive() && (!$this->getStartDate() || $today >= $this->getStartDate()) && (!$this->getEndDate() || $today <= $this->getEndDate())) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $resultingQuantity
     * @return Promotion
     */
    public function setResultingQuantity($resultingQuantity) {
        $this->resultingQuantity = $resultingQuantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResultingQuantity() {
        return $this->resultingQuantity;
    }

    /**
     * @param boolean $onlyOnTaxableItems
     * @return Promotion
     */
    public function setOnlyOnTaxableItems($onlyOnTaxableItems) {
        $this->onlyOnTaxableItems = $onlyOnTaxableItems;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getOnlyOnTaxableItems() {
        return $this->onlyOnTaxableItems;
    }

    /**
     * @return boolean
     */
    public function isOnlyOnTaxableItems() {
        return $this->onlyOnTaxableItems;
    }

    /**
     * @return int
     */
    public function getConsequentAppliedQuantity() {
        return $this->consequentAppliedQuantity;
    }

    /**
     * @param int $consequentAppliedQuantity
     */
    public function setConsequentAppliedQuantity($consequentAppliedQuantity) {
        $this->consequentAppliedQuantity = $consequentAppliedQuantity;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getConsequentDiscountedProduct() {
        return $this->consequentDiscountedProduct;
    }

    /**
     * @param ArrayCollection $consequentDiscountedProduct
     */
    public function setConsequentDiscountedProduct($consequentDiscountedProduct) {
        $this->consequentDiscountedProduct = $consequentDiscountedProduct;

        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function isMixAndMatch() {
        return $this->mixAndMatch;
    }

    /**
     * 
     * @param boolean $mixAndMatch
     * @return $this
     */
    public function setMixAndMatch($mixAndMatch) {
        $this->mixAndMatch = $mixAndMatch;

        return $this;
    }

}
