<?php

namespace App\Model;

use App\Entity\Promotion;

class Discount {

    /** @var integer */
    private $discountAmount;

    /** @var Promotion */
    private $promotionReason;

    /**
     * @param int $discountAmount
     * @return Discount
     */
    public function setDiscountAmount($discountAmount) {
        $this->discountAmount = $discountAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getDiscountAmount() {
        return $this->discountAmount;
    }

    /**
     * @param Promotion $promotionReason
     * @return Discount
     */
    public function setPromotionReason(Promotion $promotionReason) {
        $this->promotionReason = $promotionReason;
        return $this;
    }

    /**
     * @return Promotion
     */
    public function getPromotionReason() {
        return $this->promotionReason;
    }
    
    /**
     * 
     * @return array
     */
    public function serialize(): array{
        return array(
            'discountAmount' => $this->discountAmount,
            'promotion' => is_null($this->promotionReason) ? null : $this->promotionReason->serialize()
        );
    }

}
