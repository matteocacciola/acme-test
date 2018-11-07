<?php

namespace App\Entity;

use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Discount;

/**
 * @ORM\Table("acme_order_item")
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 */
class OrderItem {

    const STATUS_AT_SUPPLIER = "at_supplier";
    const STATUS_IN_WAREHOUSE = "in_warehouse";
    const STATUS_READY_TO_BE_SHIPPED = "ready_to_be_shipped";
    const STATUS_SHIPPED_TO_CUSTOMER = "shipped_to_customer";
    const STATUS_PREORDER_PREORDERED = "PREORDERED";
    const STATUS_PREORDER_SENTTOVENDOR = "SENTTOVENDOR";
    const STATUS_PREORDER_RECEIVEDFROMVENDOR = "RECEIVEDFROMVENDOR";
    const STATUS_PREORDER_SENTTOCUSTOMER = "SENTTOCUSTOMER";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Order
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @var Product
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, name="name")
     */
    private $name;

    /**
     * @var integer
     * 
     * @ORM\Column(type="integer", nullable=false, name="quantity", options={"default": 1})
     */
    private $quantity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, name="status", nullable=true)
     */
    private $status = self::STATUS_IN_WAREHOUSE;

    /**
     * @var Discount
     */
    private $discount = null;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="added_at_date", type="datetime", nullable=false)
     */
    private $addedAtDate;
    
    /**
     * @var float
     */
    private $discountsAllocation = 0;
    
    /**
     * @var float
     */
    private $taxAllocation = 0;

    /**
     * 
     * @param Product $product
     */
    public function __construct(Product $product) {
        $this->setProduct($product);
        $this->quantity = 1;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $name
     * @return OrderItem
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param Product $product
     * @return OrderItem
     */
    public function setProduct(Product $product) {
        $this->product = $product;
        $this->name = $product->getName();
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @param int $quantity
     * @return OrderItem
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }
    
    /**
     * 
     * @param int $howMany
     * @return $this
     */
    public function increaseQuantity($howMany = 1) {
        $this->quantity += $howMany;
        
        return $this;
    }

    /**
     * @param string $status
     * @return OrderItem
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $order
     * @return OrderItem
     */
    public function setOrder($order = null) {
        $this->order = $order;
        
        $this->addedAtDate = new \DateTime();
        
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * 
     * @return float
     */
    public function getSubtotal() {
        return $this->quantity * $this->getProduct()->getCurrentPrice();
    }

    /**
     * 
     * @return int
     */
    public function getSubtotalAfterDiscount() {
        if ($this->getDiscountAmount() > 0) {
            if ($this->getDiscountAmount() < $this->getSubtotal())
                return $this->getSubtotal() - $this->discount->getDiscountAmount();

            return 0;
        }

        return $this->getSubtotal();
    }

    /**
     * 
     * @return mixed
     */
    public function getDiscountAmount() {
        if ($this->discount) {
            return $this->discount->getDiscountAmount();
        }

        return 0;
    }

    /**
     * 
     * @return Discount
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * 
     * @param Discount $discount
     */
    public function setDiscount(Discount $discount) {
        $this->discount = $discount;
    }

    /**
     * 
     * @return type
     */
    public function getUnitPrice() {
        return $this->getProduct()->getCurrentPrice();
    }
    
    /**
     * @return float
     */
    public function getDiscountsAllocation() {
        return number_format($this->discountsAllocation, 2);
    }

    /**
     * @param float $discountsAllocation
     * @return ShoppingCartItem
     */
    public function setDiscountsAllocation($discountsAllocation) {
        $this->discountsAllocation = $discountsAllocation;
        return $this;
    }

    /**
     * @return int
     */
    public function getTaxAllocation() {
        return $this->taxAllocation;
    }

    /**
     * @param int $taxAllocation
     * @return ShoppingCartItem
     */
    public function setTaxAllocation($taxAllocation) {
        $this->taxAllocation = $taxAllocation;
        return $this;
    }

    /**
     * @param \DateTime $addedAtDate
     * @return OrderItem
     */
    public function setAddedAtDate(\DateTime $addedAtDate) {
        $this->addedAtDate = $addedAtDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAtDate() {
        return $this->addedAtDate;
    }
    
    /**
     * 
     * @return array
     */
    public function serialize(): array {
        return array(
            'id' => $this->id,
            'addedAtDate' => is_null($this->addedAtDate) ? null : $this->addedAtDate->format('c'),
            'discount' => is_null($this->discount) ? null : $this->discount->serialize(),
            'discountAllocation' => $this->discountsAllocation,
            'name' => $this->name,
            'product' => $this->product->serialize(),
            'quantity' => $this->quantity,
            'status' => $this->status,
            'taxAllocation' => $this->taxAllocation
        );
    }

}
