<?php

namespace App\Manager;

use App\Entity\Product;
use App\Model\PromotionInterface;
use App\Entity\Order;
use Doctrine\ORM\EntityManager;

class PromotionManager {

    /** @var  EntityManager */
    private $em;

    /** @var PromotionInterface[] */
    private $promotionTypes;

    public function __construct($em) {
        $this->em = $em;
        $this->promotionTypes = array();
    }

    /**
     * 
     */
    public function sortPromotions() {
        $comparisonFunction = function(PromotionInterface $a, PromotionInterface $b) {
            return $a->getOrder() > $b->getOrder();
        };
        usort($this->promotionTypes, $comparisonFunction);
    }

    /**
     * @return array
     */
    public function getActivePromotions() {
        $activePromotions = array();

        foreach ($this->getPromotionTypes() as $promotionType) {
            foreach ($promotionType->getActivePromotions() as $activePromotion) {
                $activePromotions[] = $activePromotion;
            }
        }

        return $activePromotions;
    }

    /**
     * 
     * @return Product[]
     */
    public function findAllProductsOnWhichAPromotionIsApplied() {
        $promotionRepository = $this->em->getRepository(Product::class);

        $products = array();
        foreach ($this->getActivePromotions() as $activePromotion) {
            $products = array_merge(
                    $promotionRepository->findProductsOnWhichPromotionIsApplicable($activePromotion, $products),
                    $products
            );
        }

        return $products;
    }

    /**
     * @param string $type
     * @param array $options
     * @param bool $persist
     * @param bool $flush
     * @return \App\Entity\Promotion
     * @throws \InvalidArgumentException
     */
    public function createPromotion($type, $options = array(), $persist = true, $flush = true) {
        foreach ($this->promotionTypes as $promotionType) {
            if ($promotionType->getName() == $type) {
                $promotion = $promotionType->createPromotion($options);

                if ($persist) {
                    $this->em->persist($promotion);
                }

                if ($flush) {
                    $this->em->flush();
                }

                return $promotion;
            }
        }

        throw new \InvalidArgumentException('Unable to find promotion type with name ' . $type);
    }

    /**
     * @param PromotionInterface $promotionType
     * @throws \Exception
     */
    public function addPromotionType(PromotionInterface $promotionType) {
        foreach ($this->promotionTypes as $type) {
            if ($type->getName() == $promotionType->getName()) {
                throw new \Exception('Duplicate promotion type with name ' . $type->getName());
            }
        }
        $this->promotionTypes[] = $promotionType;
    }

    /**
     * @return array|\BoBebeOnlineStore\PromotionBundle\Model\PromotionInterface[]
     */
    public function getPromotionTypes() {
        return $this->promotionTypes;
    }

    /**
     * @param $name
     * @return PromotionInterface
     * @throws \InvalidArgumentException
     */
    public function getPromotionType($name) {
        foreach ($this->promotionTypes as $type) {
            if ($type->getName() == $name) {
                return $type;
            }
        }

        throw new \InvalidArgumentException('Unable to find promotion type with name ' . $name);
    }

    /**
     * 
     * @param Order $order
     */
    public function applyPromotions(Order $order) {
        $this->sortPromotions();
        foreach ($this->promotionTypes as $promotionType) {
            $promotionType->applyPromotion($order);
        }

        foreach ($order->getItems() as $item) {
            if ($itemDiscount = $item->getDiscount()) {
                $orderDiscount = null;
                
                foreach ($order->getDiscounts() as $discount) {
                    if ($discount->getPromotionReason() == $itemDiscount->getPromotionReason()) {
                        $orderDiscount = $discount;
                        break;
                    }
                }

                if ($orderDiscount) {
                    $discountAmount = $orderDiscount->getDiscountAmount() + $itemDiscount->getDiscountAmount();
                    $orderDiscount->setDiscountAmount($discountAmount);
                } else {
                    $orderDiscount = clone $itemDiscount;
                    $order->addDiscount($orderDiscount);
                }
            }
        }
    }

}
