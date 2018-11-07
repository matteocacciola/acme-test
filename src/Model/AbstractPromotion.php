<?php

namespace App\Model;

use App\Entity\Promotion;
use Doctrine\ORM\EntityManager;
use App\Model\PromotionInterface;
use App\Model\Discount;
use App\Entity\Order;

abstract class AbstractPromotion implements PromotionInterface {

    /** @var  EntityManager */
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function getOrder() {
        return 1;
    }

    /**
     * @return array|\App\Entity\Promotion[]
     */
    public function getActivePromotions() {
        $repository = $this->em->getRepository(Promotion::class);

        return $repository->findActivePromotionsByType($this->getName());
    }
    
    /**
     * 
     * @param Order $order
     */
    public function applyPromotion(Order $order) {
        $activePromotions = $this->getActivePromotions();

        foreach ($activePromotions as $promotion) {
            if ($this->getScope() == self::SCOPE_ORDER_ITEM) {
                foreach ($order->getItems() as $orderItem) {
                    if ($this->promotionIsApplicableOnOrderItem($orderItem, $promotion)) {

                        $discountAmount = $this->calculateOrderItemDiscount($orderItem, $promotion);

                        if ($discountAmount > $orderItem->getDiscountAmount()) {
                            $discount = new Discount();
                            $discount->setDiscountAmount($discountAmount);
                            $discount->setPromotionReason($promotion);

                            $orderItem->setDiscount($discount);
                        }
                    }
                }
            }

            if ($this->getScope() == self::SCOPE_ORDER) {
                if ($this->promotionIsApplicableOnOrder($order, $promotion)) {

                    $discountAmount = $this->calculateOrderDiscount($order, $promotion);

                    if ($discountAmount > 0) {
                        $discount = new Discount();
                        $discount->setDiscountAmount($discountAmount);
                        $discount->setPromotionReason($promotion);

                        $order->addDiscount($discount);
                    }
                }
            }
        }
    }

    /**
     * 
     * @param Promotion $promotion
     * @throws \InvalidArgumentException
     */
    protected function validatePromotionType(Promotion $promotion) {
        if ($this->getName() != $promotion->getPromotionType()) {
            throw new \InvalidArgumentException('Class ' . get_class($this) . ' can\'t handle promotions of type ' . $promotion->getPromotionType());
        }
    }

}
