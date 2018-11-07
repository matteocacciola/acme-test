<?php

namespace App\Model\Promotion;

use App\Entity\Discount;
use App\Entity\Promotion;
use App\Model\AbstractPromotion;
use App\Entity\Order;
use App\Entity\OrderItem;

class BuyXForYQuantityPromotion extends AbstractPromotion {

    public function getName() {
        return 'buy_x_for_y_quantity_promotion';
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
            $initialTotalPrice = 0;

            $itemsToProcess = array();

            foreach ($order->getItems() as $orderItem) {
                if ($this->promotionIsApplicableOnOrderItem($orderItem, $promotion)) {
                    $nbrOfItems += $orderItem->getQuantity();
                    $initialTotalPrice += $orderItem->getSubtotal();

                    $itemsToProcess[] = [
                        'quantity' => $orderItem->getQuantity(),
                        'price' => $orderItem->getUnitPrice(),
                        'item' => $orderItem
                    ];
                }
            }

            usort($itemsToProcess, function($a, $b) {
                if ($a['price'] == $b['price']) {
                    return 0;
                }
                return ($a['price'] < $b['price']) ? -1 : 1;
            });

            $priceToPay = 0;

            while ($this->getNbrOfItemsToProcess($itemsToProcess) >= $promotion->getRequiredQuantity()) {
                $maxPrice = $this->processItemsAndGetMaxPrice($itemsToProcess, $promotion->getRequiredQuantity(), $allocationDetails);

                $resultingPrice = $maxPrice * $promotion->getResultingQuantity();

                $discountToAllocate = $allocationDetails['initialPrice'] - $resultingPrice;

                foreach ($allocationDetails['items'] as $itemAllocationDetail) {
                    $orderItem = $itemAllocationDetail['item'];

                    $discountAllocation = $orderItem->getDiscountsAllocation();

                    $discountAllocation += ($discountToAllocate * $itemAllocationDetail['initialPrice']) / $allocationDetails['initialPrice'];
                    $discountAllocation = number_format($discountAllocation, 2);

                    $orderItem->setDiscountsAllocation($discountAllocation);
                }

                $priceToPay += $resultingPrice;
            }

            $priceToPay += $this->getPriceOfRemainingUnprocessedItems($itemsToProcess);

            $discount = new Discount();
            $discount->setPromotionReason($promotion);
            $discount->setDiscountAmount($initialTotalPrice - $priceToPay);
            $order->addDiscount($discount);
        }
    }

    /**
     * 
     * @param type $itemsToProcess
     * @return type
     */
    private function getNbrOfItemsToProcess($itemsToProcess) {
        $counter = 0;
        foreach ($itemsToProcess as $item) {
            $counter += $item['quantity'];
        }

        return $counter;
    }

    /**
     * 
     * @param type $itemsToProcess
     * @param type $nbrOfItemsToBeProcessed
     * @param type $allocationDetails
     * @return type
     */
    private function processItemsAndGetMaxPrice(&$itemsToProcess, $nbrOfItemsToBeProcessed, &$allocationDetails) {
        $maxPrice = 0;
        $itemsToProcess = array_values($itemsToProcess);

        $allocationDetails = [
            'initialPrice' => 0,
            'items' => []
        ];

        for ($i = 0; $i < $nbrOfItemsToBeProcessed; $i++) {
            if (!isset($allocationDetails['items'][$itemsToProcess[0]['item']->getId()])) {
                $allocationDetails['items'][$itemsToProcess[0]['item']->getId()] = [
                    'initialPrice' => 0,
                    'item' => $itemsToProcess[0]['item']
                ];
            }

            $quantity = $itemsToProcess[0]['quantity'] - 1;
            $maxPrice = max($maxPrice, $itemsToProcess[0]['price']);
            $itemsToProcess[0]['quantity'] = $quantity;

            $allocationDetails['items'][$itemsToProcess[0]['item']->getId()]['initialPrice'] += $itemsToProcess[0]['price'];

            $allocationDetails['initialPrice'] += $itemsToProcess[0]['price'];

            if ($itemsToProcess[0]['quantity'] == 0) {
                unset($itemsToProcess[0]);
            }

            $itemsToProcess = array_values($itemsToProcess);
        }

        return $maxPrice;
    }

    /**
     * 
     * @param type $itemsToProcess
     * @return type
     */
    private function getPriceOfRemainingUnprocessedItems($itemsToProcess) {
        $price = 0;
        foreach ($itemsToProcess as $item) {
            $price += $item['quantity'] * $item['price'];
        }

        return $price;
    }

}
