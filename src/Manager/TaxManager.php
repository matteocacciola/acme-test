<?php

namespace App\Manager;

use App\Entity\Order;

class TaxManager {

    /**
     * 
     * @param Order $order
     */
    public function applyTaxes(Order $order) {
        $taxes = $this->calculateTaxes($order);

        $order->setTaxesAmount($taxes);
    }

    /**
     * 
     * @param \App\Manager\Order $order
     * @return float
     */
    public function calculateTaxes(Order $order) {
        $orderTaxes = 0;

        // $taxableAmount = $order->getFinalPriceBeforeTaxes();
        foreach ($order->getItems() as $item) {
            $tax = 0;
            $product = $item->getProduct();
            if ($product->isTaxable()) {
                $taxableAmount = $item->getSubtotal() - $item->getDiscountsAllocation();
                $tax = $taxableAmount * $product->getVatClass()->getPercentage();
            }

            $item->setTaxAllocation($tax);
            $orderTaxes += $tax;
        }

        return $orderTaxes;
    }

}
