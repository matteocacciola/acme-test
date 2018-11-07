<?php

namespace App\Model\Promotion;

use App\Entity\Discount;
use App\Entity\Promotion;
use App\Model\AbstractPromotion as MainPromotion;
use App\Entity\Order;
use App\Entity\OrderItem;

class BuyXForYPromotion extends MainPromotion {

    public function getName() {
        return "buy_x_for_y_promotion";
    }
    
    public function getScope() {
        return self::SCOPE_ORDER_ITEM;
    }

    /**
     * @inheritdoc
     */
    public function promotionIsApplicableOnOrderItem(OrderItem $orderItem, Promotion $promotion) {
        $product = $orderItem->getProduct();

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
        return false;
    }

    /**
     * @inheritdoc
     */
    public function calculateOrderItemDiscount(OrderItem $orderItem, Promotion $promotion) {
        $discount = 0;

        if ($orderItem->getQuantity() >= $promotion->getRequiredQuantity()) {
            $priceToPay = 0;

            $tmpQuantity = $orderItem->getQuantity();

            while ($tmpQuantity >= $promotion->getRequiredQuantity()) {
                $priceToPay += $promotion->getResultingPrice();
                $tmpQuantity -= $promotion->getRequiredQuantity();
            }

            $priceToPay += $tmpQuantity * $orderItem->getUnitPrice();

            $discount = $orderItem->getSubtotal() - $priceToPay;
        }

        return $discount;
    }
    
    /**
     * @inheritdoc
     */
    public function calculateOrderDiscount(Order $order, Promotion $promotion) {
        return 0;
    }

    /**
     * 
     * @param Order $order
     */
    public function applyPromotion(Order $order) {
        $activePromotions = $this->getActivePromotions();

        /** @var $promotion Promotion */
        foreach ($activePromotions as $promotion) {
            $nbrOfItems = 0;
            $priceCounter = 0;
            $unitPrice = null;

            foreach ($order->getItems() as $orderItem) {
                if ($this->promotionIsApplicableOnOrderItem($orderItem, $promotion)) {
                    $nbrOfItems += $orderItem->getQuantity();
                    $priceCounter += $orderItem->getSubtotal();

                    if (!$unitPrice) {
                        $unitPrice = $orderItem->getUnitPrice();
                    } else {
                        $unitPrice = min($orderItem->getUnitPrice(), $unitPrice);
                    }
                }
            }

            if ($nbrOfItems >= $promotion->getRequiredQuantity()) {
                $priceToPay = floor($nbrOfItems / $promotion->getRequiredQuantity()) * $promotion->getResultingPrice();
                $priceToPay += ($nbrOfItems - floor($nbrOfItems / $promotion->getRequiredQuantity()) * $promotion->getRequiredQuantity()) * $unitPrice;

                $discount = new Discount();
                $discount->setPromotionReason($promotion);
                $discount->setDiscountAmount($priceCounter - $priceToPay);
                $order->addDiscount($discount);

                foreach ($order->getItems() as $orderItem) {
                    if ($this->promotionIsApplicableOnOrderItem($orderItem, $promotion)) {
                        $discountAllocation = $orderItem->getDiscountsAllocation();
                        $discountAllocation += $discount->getDiscountAmount() * $orderItem->getSubtotal() / $priceCounter;
                        $orderItem->setDiscountsAllocation($discountAllocation);
                    }
                }
            }
        }
    }

}
