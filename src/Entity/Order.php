<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\OrderItem;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order {

    const STATUS_NOTPAID = 'NOTPAID';
    const STATUS_WAITING_FOR_PAYMENT = 'WAITING_FOR_PAYMENT';
    const STATUS_PAID = 'PAID';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_REFUNDED = 'REFUNDED';
    const STATUS_COMPLETED = 'COMPLETED';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoice_number", type="integer", nullable=false)
     */
    private $invoiceNumber;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection | OrderItem[]
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="order", cascade={"persist", "remove"})
     * @ORM\OrderBy({"added_at_date" = "ASC"})
     */
    private $items;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection | OrderDiscount[]
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\OrderDiscount", mappedBy="order", cascade={"persist", "remove"})
     */
    private $discounts;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", name="order_status", nullable=false, length=100)
     */
    private $status;

    /**
     * @ORM\Column(type="text", name="shipping_address", nullable=false)
     */
    private $shippingAddress;

    /**
     * @ORM\Column(type="text", name="billing_address", nullable=false)
     */
    private $billingAddress;

    /**
     * @var float
     * 
     * @ORM\Column(type="float", name="taxes_amount", nullable=false, options={"default": 0})
     */
    private $taxesAmount;

    /**
     * @var float
     * 
     * @ORM\Column(type="float", name="discounts_amount", nullable=false, options={"default": 0})
     */
    private $discountsAmount;

    /**
     * @var float
     * 
     * @ORM\Column(type="float", name="subtotal", nullable=false, options={"default": 0})
     */
    private $subtotal;

    /**
     * @var float
     * 
     * @ORM\Column(type="float", name="subtotal_after_discounts", nullable=false, options={"default": 0})
     */
    private $subtotalAfterDiscounts;

    /**
     * @var float
     * 
     * @ORM\Column(type="float", name="final_price_before_taxes", nullable=false, options={"default": 0})
     */
    private $finalPriceBeforeTaxes;

    /**
     * @var float
     * 
     * @ORM\Column(type="float", name="final_price", nullable=false, options={"default": 0})
     */
    private $finalPrice;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="status_update_date", type="datetime", nullable=true)
     */
    private $statusUpdateDate;

    /**
     * 
     */
    public function __construct() {
        $this->items = new ArrayCollection();
        $this->status = self::STATUS_NOTPAID;
        $this->createDate = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param \App\Entity\OrderDiscount $discount
     * @return $this
     */
    public function addDiscount(OrderDiscount $discount) {
        $this->discounts->add($discount);
        $discount->setOrder($this);

        return $this;
    }

    /**
     * @param string $billingAddress
     * @return Order
     */
    public function setBillingAddress($billingAddress) {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAddress() {
        return $this->billingAddress;
    }

    /**
     * @param \DateTime $createDate
     * @return Order
     */
    public function setCreateDate(\DateTime $createDate) {
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
     * @param float $discountsAmount
     * @return Order
     */
    public function setDiscountsAmount($discountsAmount) {
        $this->discountsAmount = $discountsAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountsAmount() {
        return $this->discountsAmount;
    }

    /**
     * @param \App\Entity\OrderItem[]|\Doctrine\Common\Collections\ArrayCollection $items
     * @return Order
     */
    public function setItems($items) {
        $this->items = $items;
        return $this;
    }

    /**
     * 
     * @param \App\Entity\OrderItem $item
     * @return $this
     */
    public function addItem(OrderItem $item) {
        $this->items->add($item);

        $item->setOrder($this);

        return $this;
    }

    /**
     * 
     * @param \App\Entity\OrderItem $item
     * @return $this
     */
    public function removeItem(OrderItem $item) {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * 
     * @param \App\Entity\OrderItem $item
     */
    public function containsItem(OrderItem $item) {
        $this->items->contains($item) ? true : false;
    }

    /**
     * @return \App\Entity\OrderItem[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @return \BoBebeOnlineStore\OrderBundle\Entity\OrderItem[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getProductsIds() {
        $ids = array();
        foreach ($this->getItems() as $item) {
            $ids[] = $item->getProduct()->getId();
        }

        return $ids;
    }

    /**
     * @param $invoiceNumber
     * @return $this
     */
    public function setInvoiceNumber($invoiceNumber) {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getInvoiceNumber() {
        return $this->invoiceNumber;
    }

    /**
     * @param string $shippingAddress
     * @return Order
     */
    public function setShippingAddress($shippingAddress) {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAddress() {
        return $this->shippingAddress;
    }

    /**
     * @param string $status
     * @return Order
     */
    public function setStatus($status) {
        $this->status = $status;
        
        $this->setStatusUpdateDate(new \DateTime());
        
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $statusUpdateDate
     * @return Order
     */
    public function setStatusUpdateDate(\DateTime $statusUpdateDate) {
        $this->statusUpdateDate = $statusUpdateDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusUpdateDate() {
        return $this->statusUpdateDate;
    }

    /**
     * @param float $taxesAmount
     * @return Order
     */
    public function setTaxesAmount($taxesAmount) {
        $this->taxesAmount = $taxesAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxesAmount() {
        return $this->taxesAmount;
    }
    
    /**
     * 
     * @param float $amount
     * @return $this
     */
    public function addToTaxesAmount($amount) {
        $this->taxesAmount += $amount;
        
        return $this;
    }

    /**
     * @param float $finalPrice
     * @return Order
     */
    public function setFinalPrice() {
        $this->finalPrice = round($this->calcFinalPrice(), 2);
        return $this;
    }

    /**
     * @return float
     */
    public function getFinalPrice() {
        $this->setFinalPrice();

        return $this->finalPrice;
    }

    /**
     * @param float $finalPriceBeforeTaxes
     * @return Order
     */
    public function setFinalPriceBeforeTaxes() {
        $this->finalPriceBeforeTaxes = round($this->calcFinalPriceBeforeTaxes(), 2);
        return $this;
    }

    /**
     * @return float
     */
    public function getFinalPriceBeforeTaxes() {
        $this->setFinalPriceBeforeTaxes();

        return $this->finalPriceBeforeTaxes;
    }

    /**
     * @param mixed $subtotal
     * @return Order
     */
    public function setSubtotal() {
        $this->subtotal = round($this->calcSubtotal(), 2);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubtotal() {
        $this->setSubtotal();

        return $this->subtotal;
    }

    /**
     * @param float $subtotalAfterDiscounts
     * @return Order
     */
    public function setSubtotalAfterDiscounts() {
        $this->subtotalAfterDiscounts = round($this->calcSubtotalAfterDiscounts(), 2);
        return $this;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscounts() {
        $this->setSubtotalAfterDiscounts();

        return $this->subtotalAfterDiscounts;
    }
    
    /**
     * 
     * @param OrderItem[] $items
     * @return Product[]
     */
    public function getItemsProducts($items) {
        $products = new ArrayCollection();
        
        foreach ($items as $item) {
            if ($item instanceof OrderItem) {
                $products->add($item->getProduct());
            }
        }
        
        return $products;
    }
    
    /**
     * 
     * @param \App\Entity\Product $product
     * @return boolean
     */
    public function hasItemProduct(Product $product) {
        return $this->getItemsProducts($items)->contains($product);
    }

    /**
     * 
     * @return int
     */
    protected function calcFinalPrice() {
        /* $shippingPrice = $this->getShippingPrice();
        if ($shippingPrice == -1) {
            $shippingPrice = 0;
        }

        $oversizedShippingPrice = $this->getOversizedShippingPrice();
        if ($oversizedShippingPrice == -1) {
            $oversizedShippingPrice = 0;
        } */
        $shippingPrice = $oversizedShippingPrice = 0;

        $price = $this->getSubtotal() - $this->calcDiscountsTotalAmount() + $shippingPrice + $oversizedShippingPrice + $this->taxes;

        return ($price > 0) ? $price : 0;
    }

    /**
     * 
     * @return int
     */
    protected function calcSubtotal() {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            $subtotal += $item->getSubtotal();
        }

        return $subtotal;
    }

    /**
     * 
     * @return float
     */
    protected function calcFinalPriceBeforeTaxes() {
        /* $shippingPrice = $this->getShippingPrice();
        if ($shippingPrice == -1) {
            $shippingPrice = 0;
        }

        $oversizedShippingPrice = $this->getOversizedShippingPrice();
        if ($oversizedShippingPrice == -1) {
            $oversizedShippingPrice = 0;
        } */
        $shippingPrice = $oversizedShippingPrice = 0;

        return $this->getSubtotal() - $this->calcDiscountsTotalAmount() + $shippingPrice + $oversizedShippingPrice;
    }

    /**
     * 
     * @return type
     */
    protected function calcDiscountsTotalAmount() {
        $amount = 0;

        foreach ($this->discounts as $discount) {
            $amount += $discount->getDiscountAmount();
        }

        return $amount;
    }

    /**
     * 
     * @return type
     */
    protected function calcSubtotalAfterDiscounts() {
        return $this->getSubtotal() - $this->calcDiscountsTotalAmount();
    }

}