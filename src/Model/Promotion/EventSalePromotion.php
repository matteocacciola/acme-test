<?php

namespace App\Model\Promotion;

use App\Entity\Promotion;
use App\Model\AbstractPromotion;
use App\Entity\OrderItem;
use App\Model\Discount;
use App\Entity\Order;

class EventSalePromotion extends AbstractPromotion {

    public function getName() {
        return 'event_sale_promotion';
    }

    public function getOrder() {
        return 5;
    }
    
    public function getScope() {
        return self::SCOPE_ORDER;
    }

    /**
     * @inheritdoc
     */
    public function promotionIsApplicableOnOrderItem(OrderItem $orderItem, Promotion $promotion) {
        $product = $orderItem->getProduct();

        if (($promotion->isOnlyOnTaxableItems() && !$product->isTaxable())) {
            return false;
        }

        if (!$product->isTaxable()) {
            return false;
        }

        if ($promotion->isAppliedOnAllProducts()) {
            return true;
        }

        if ($promotion->getConcernedProducts()->contains($product)) {
            return true;
        }

        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function promotionIsApplicableOnOrder(Order $order, Promotion $promotion) {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function calculateOrderItemDiscount(OrderItem $orderItem, Promotion $promotion) {
        $discount = $orderItem->getSubtotalAfterDiscount() * $promotion->getDiscountValue() / 100;

        if ($discount > $orderItem->getSubtotal()) {
            return $orderItem->getSubtotal();
        }

        return ($discount > 0) ? $discount : 0;
    }
    
    /**
     * @inheritdoc
     */
    public function calculateOrderDiscount(Order $order, Promotion $promotion) {
        $discount = 0;

        $this->validatePromotionType($promotion);

        foreach ($order->getItems() as $item) {
            if ($this->promotionIsApplicableOnOrderItem($item, $promotion)) {
                $discount += $this->calculateOrderItemDiscount($item, $promotion);
            }
        }

        return $discount;
    }

    /**
     * 
     * @param Order $order
     */
    public function applyPromotion(Order $order) {
        $price = $order->getSubtotalAfterDiscounts();

        $activePromotions = $this->getActivePromotions();
        /** @var $activePromotion Promotion */
        foreach ($activePromotions as $activePromotion) {
            $tmpPrice = $price;

            foreach ($order->getItems() as $orderItem) {
                if (!$this->promotionIsApplicableOnOrderItem($orderItem, $activePromotion)) {
                    $tmpPrice -= $orderItem->getSubtotal();
                }
            }

            if ($activePromotion->isDiscountInPercentage()) {
                $discountAmount = $tmpPrice * $activePromotion->getDiscountValue() / 100;
                $discount = new Discount();
                $discount->setDiscountAmount($discountAmount);
                $discount->setPromotionReason($activePromotion);
            } else {
                $discount = new Discount();
                $discount->setDiscountAmount($activePromotion->getDiscountValue());
                $discount->setPromotionReason($activePromotion);
            }

            if ($discount->getDiscountAmount() > 0.01) {
                $order->addDiscount($discount);

                $discountAmount = $discount->getDiscountAmount();

                foreach ($order->getItems() as $orderItem) {
                    if ($this->promotionIsApplicableOnOrderItem($orderItem, $activePromotion)) {
                        $discountAllocation = $orderItem->getDiscountsAllocation();

                        $discountAllocation += ($orderItem->getSubtotal() - $orderItem->getDiscountsAllocation()) * $discountAmount / $tmpPrice;

                        $orderItem->setDiscountsAllocation($discountAllocation);
                    }
                }
            }
        }
    }

}
