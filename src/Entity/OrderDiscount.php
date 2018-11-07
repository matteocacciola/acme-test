<?php

namespace App\Entity;

use App\Model\Discount;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderDiscount {

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="discounts")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion_type", type="string", length=255, nullable=true)
     */
    private $promotionType;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion_name", type="string", length=255, nullable=true)
     */
    private $promotionName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="promotion_start_date", type="datetime", nullable=true)
     */
    private $promotionStartDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="promotion_en_date", type="datetime", nullable=true)
     */
    private $promotionEndDate;

    /**
     * @var float
     * @ORM\Column(type="float", name="discount_amount", nullable=false)
     */
    private $discountAmount;

    /**
     * 
     * @param Discount $discount
     */
    public function __construct(Discount $discount = null) {
        if ($discount !== null) {
            $promotion = $discount->getPromotionReason();

            $this->setDiscountAmount(round($discount->getDiscountAmount(), 2));
            $this->setPromotionType($promotion->getPromotionType());
            $this->setPromotionName($promotion->getName());
            $this->setPromotionStartDate($promotion->getStartDate());
            $this->setPromotionEndDate($promotion->getEndDate());
        }
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param float $discountAmount
     * @return OrderDiscount
     */
    public function setDiscountAmount($discountAmount) {
        $this->discountAmount = $discountAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountAmount() {
        return $this->discountAmount;
    }

    /**
     * @param \DateTime $promotionEndDate
     * @return OrderDiscount
     */
    public function setPromotionEndDate($promotionEndDate) {
        $this->promotionEndDate = $promotionEndDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPromotionEndDate() {
        return $this->promotionEndDate;
    }

    /**
     * @param string $promotionName
     * @return OrderDiscount
     */
    public function setPromotionName($promotionName) {
        $this->promotionName = $promotionName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPromotionName() {
        return $this->promotionName;
    }

    /**
     * @param \DateTime $promotionStartDate
     * @return OrderDiscount
     */
    public function setPromotionStartDate($promotionStartDate) {
        $this->promotionStartDate = $promotionStartDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPromotionStartDate() {
        return $this->promotionStartDate;
    }

    /**
     * @param string $promotionType
     * @return OrderDiscount
     */
    public function setPromotionType($promotionType) {
        $this->promotionType = $promotionType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPromotionType() {
        return $this->promotionType;
    }

    /**
     * @param \App\Entity\Order $order
     * @return OrderDiscount
     */
    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * @return \App\Entity\Order
     */
    public function getOrder() {
        return $this->order;
    }

}
