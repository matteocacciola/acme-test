<?php

namespace App\Model;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Promotion;

interface PromotionInterface {
    
    const SCOPE_ORDER = 'order';
    const SCOPE_ORDER_ITEM = 'order_item';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return integer
     */
    public function getOrder();

    /**
     * @param Order $order
     * @return void
     */
    public function applyPromotion(Order $order);

    /**
     * @return Promotion[]|array
     */
    public function getActivePromotions();

    /**
     * @param OrderItem $orderItem
     * @param Promotion $promotion
     * @return boolean
     */
    public function promotionIsApplicableOnOrderItem(OrderItem $orderItem, Promotion $promotion);

    /**
     * @param Order $order
     * @param Promotion $promotion
     * @return boolean
     */
    public function promotionIsApplicableOnOrder(Order $order, Promotion $promotion);

    /**
     * @param OrderItem $orderItem
     * @param Promotion $promotion
     * @return integer
     */
    public function calculateOrderItemDiscount(OrderItem $orderItem, Promotion $promotion);

    /**
     * @param Order $order
     * @param Promotion $promotion
     * @return integer
     */
    public function calculateOrderDiscount(Order $order, Promotion $promotion);
    
    public function getScope();
}
