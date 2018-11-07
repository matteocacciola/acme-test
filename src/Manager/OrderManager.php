<?php

namespace App\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Manager\PromotionManager;
use App\Manager\TaxManager;
use Symfony\Component\HttpFoundation\Response;

class OrderManager {

    const STARTING_ORDER_NUMBER = 1;

    /** @var ObjectManager $em */
    private $em;

    /** @var PromotionManager $promotionManager */
    private $promotionManager;

    /** @var TaxManager $taxManager */
    private $taxManager;

    public function __construct(ObjectManager $em, PromotionManager $promotionManager, TaxManager $taxManager) {
        $this->em = $em;
        $this->promotionManager = $promotionManager;
        $this->taxManager = $taxManager;
    }

    /**
     * 
     * @param string $shippingAddress
     * @param string $billingAddress
     * @param array $products
     * @return Order
     * @throws \Exception
     */
    public function createNew(User $user, $shippingAddress, $billingAddress, $products = array()) {
        $order = new Order();

        foreach ($products as $product) {
            $this->addItem($product, $order, false);
        }

        $order
                ->setShippingAddress($shippingAddress)
                ->setBillingAddress($billingAddress)
                ->setUser($user)
        ;

        $this->em->getConnection()->beginTransaction();

        try {
            $this->em->getConnection()->exec('LOCK TABLE order AS b0_ READ, order WRITE, order_items WRITE, bo_bebe_order_applied_gift_card WRITE, bo_bebe_order_comment WRITE, bo_bebe_order_delivery_zone WRITE, bo_bebe_order_discount WRITE, oversized_products_order WRITE, bo_bebe_shopping_cart WRITE');
            $order->setInvoiceNumber($this->createOrderNumber());
            $this->em->persist($order);
            $this->em->flush();
            $this->em->getConnection()->commit();
            $this->em->getConnection()->exec('UNLOCK TABLES');
        } catch (\Exception $ex) {
            $this->em->getConnection()->rollback();
            $this->em->getConnection()->exec('UNLOCK TABLES');
            $this->em->close();
            throw $ex;
        }

        $this->em->flush();

        return $order;
    }

    /**
     * 
     * @param Product $product
     * @param Order $order
     * @param bool $persistent
     * @throws \Exception
     */
    public function addItem(Product $product, Order &$order, $persistent = true) {
        $status = $order->getStatus();

        if (($status == Order::STATUS_NOTPAID) || ($status == Order::STATUS_WAITING_FOR_PAYMENT)) {
            if ($order->hasItemProduct($product)) {
                $orderItem = $this->em->getRepository(OrderItem::class)
                        ->findOneBy(array('order' => $order, 'product' => $product))
                ;
                $orderItem->increaseQuantity();
            } else {
                $orderItem = new OrderItem($product);
                $order->addItem($orderItem);
            }

            $this->refresh($order);
            $this->save($order, $persistent);
        } else {
            throw new \Exception('The current order cannot be changed', Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * 
     * @param OrderItem $item
     * @param Order $order
     * @param int $howMany
     */
    public function increaseItemQuantity(OrderItem $item, Order &$order, $howMany = 1, $persistent = true) {
        $status = $order->getStatus();

        if (($status == Order::STATUS_NOTPAID) || ($status == Order::STATUS_WAITING_FOR_PAYMENT)) {
            if ($order->containsItem($item)) {
                $item->increaseQuantity($howMany);
            } else {
                $order->addItem($item);
            }

            $this->refresh($order);
            $this->save($order, $persistent);
        } else {
            throw new \Exception('The current order cannot be changed', Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * 
     * @param Order $order
     * @return type
     */
    public function finalize(Order &$order) {
        if ($order->getStatus() == Order::STATUS_COMPLETED || $order->getStatus() == Order::STATUS_PAID) {
            return;
        }

        $order->setStatus(Order::STATUS_COMPLETED);
        $this->em->flush();
    }
    
    /**
     * 
     * @param Product $product
     * @param Order $order
     * @param boolean $persistent
     * @throws \Exception
     */
    public function removeItem(Product $product, Order &$order, $persistent = true) {
        $status = $order->getStatus();

        if (($status == Order::STATUS_NOTPAID) || ($status == Order::STATUS_WAITING_FOR_PAYMENT)) {
            if ($order->hasItemProduct($product)) {
                $orderItem = $this->em->getRepository(OrderItem::class)
                        ->findOneBy(array('order' => $order, 'product' => $product))
                ;
                
                $order->removeItem($orderItem);
                
                $this->refresh($order);
                $this->save($order, $persistent);
            }
        } else {
            throw new \Exception('The current order cannot be changed', Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * 
     * @param array $orders
     * @return float
     * @throws \Exception
     */
    public function calculateProfitMargin($orders) {
        try {
            $margin = 0;
            foreach ($orders as $order) {
                foreach ($order->getItems() as $orderItem) {
                    $margin += $this->calculateProfitMarginFromItem($orderItem);
                }
            }

            return $margin;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 
     * @param Order $order
     * @return array
     */
    public function getTotalVatPerClass(Order $order) {
        $vatAmounts = [];
        foreach ($order->getItems() as $item) {
            $vatPercentage = $item->getProduct()->getVatClass()->getPercentage();
            $vatClass = number_format($vatPercentage, 2);
            $vatAmounts[$vatClass] += $item->getSubtotalAfterDiscount() * $vatPercentage;
        }

        return $vatAmounts;
    }

    /**
     * 
     * @param Order $order
     * @param boolean $persistent
     */
    public function save(Order $order, $persistent = true) {
        if ($persistent === true) {
            $this->em->persist($order);
            $this->em->flush();
        }
    }
    
    /**
     * 
     * @param Order $order
     * @throws \Exception
     */
    public function refresh(Order &$order) {
        $status = $order->getStatus();

        if (($status == Order::STATUS_NOTPAID) || ($status == Order::STATUS_WAITING_FOR_PAYMENT)) {
            $order
                    ->setSubtotal()
                    ->setSubtotalAfterDiscounts()
                    ->setFinalPriceBeforeTaxes()
                    ->setFinalPrice()
            ;
            
            $this->promotionManager->applyPromotions($order);
            $this->taxManager->applyTaxes($order);
        } else {
            throw new \Exception('The current order cannot be changed', Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * 
     * @param string|array $invoiceNumber
     * @param string|null $key
     * @return Order
     * @throws \Exception
     */
    public function findOneByInvoiceNumberOrThrowException($invoiceNumber, $key = null) {
        if ($key) {
            if (is_array($invoiceNumber) && array_key_exists($key, $invoiceNumber)) {
                $invoiceNumber = $invoiceNumber[$key];
            } else {
                $invoiceNumber = null;
            }
        }

        if (!($invoiceNumber)) {
            throw new \Exception('Receipt id is mandatory', Response::HTTP_BAD_REQUEST);
        }

        $order = $this->em->getRepository(Order::class)->findOneBy(array(
            'invoiceNumber' => $invoiceNumber
        ));
        if (!($order)) {
            throw new \Exception('Receipt not found', Response::HTTP_NOT_FOUND);
        }

        return $order;
    }

    /**
     * 
     * @return string
     */
    private function createOrderNumber() {
        $query = $this->em->createQuery('SELECT MAX(o.invoiceNumber) FROM ' . Order::class . ' o');
        $result = $query->getSingleScalarResult();

        if ($result && $result >= self::STARTING_ORDER_NUMBER) {
            return $result + 1;
        }

        return self::STARTING_ORDER_NUMBER;
    }

    /**
     * 
     * @param OrderItem $orderItem
     * @return float
     */
    private function calculateProfitMarginFromItem(OrderItem $orderItem) {
        return $orderItem->getSubtotalAfterDiscount();
    }

}
