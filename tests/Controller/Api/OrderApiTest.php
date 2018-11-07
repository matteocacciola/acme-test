<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;
use App\Entity\Product;
use App\Entity\Order;

class OrderApiTest extends ApiTestCase {
    
    /**
     * This test is used to create a brand new order/receipt
     */
    public function testCreate() {
        $data = array(
            'shipping_address' => 'Street no.1, Amsterdam, NL'
        );
        
        $response = json_decode($this->postToApi($this->cashRegisterAuth, 'order', 'create', $data), true);
        
        $this->assertArrayHasKey('invoiceNumber', $response);
    }
    
    /**
     * This test is used to add a product to an order/receipt, given the product
     * barcode and code/receipt invoice number
     */
    public function testAddProduct() {
        $product = $this->em->getRepository(Product::class)->find(1);
        $order= $this->em->getRepository(Order::class)->find(1);
        
        if (!(is_null($product)) && !(is_null($order))) {
            $data = array(
                'product_id' => $product->getBarcode(),
                'receipt_id' => $order->getInvoiceNumber()
            );

            $response = json_decode($this->putToApi($this->cashRegisterAuth, 'order', 'product/add', $data), true);

            $this->assertArrayHasKey('invoiceNumber', $response);
        } else {
            $this->assertTrue((is_null($product) || is_null($order)));
        }
    }
    
    /**
     * This test is used to increase the quantity of the last item added to
     * the selected order/receipt, retrieved by a suitable invoice number
     */
    public function testIncreaseLastProduct() {
        $order = $this->em->getRepository(Order::class)->find(1);
        $lastItemQuantityBefore = $order->getItems()->last()->getQuantity();
        
        $data = array(
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = json_decode($this->putToApi($this->cashRegisterAuth, 'order', 'product/increase-last', $data), true);
        
        $countItems = count($response['items']);
        $lastItem = $response['items'][$countItems - 1];
        $lastItemQuantityAfter = $lastItem['quantity'];
        
        $this->assertEquals(1, ($lastItemQuantityAfter - $lastItemQuantityBefore));
    }
    
    /**
     * This test is used to finalize an order/receipt, given its invoice number
     */
    public function testFinalize() {
        $order = $this->em->getRepository(Order::class)->find(1);
        
        $data = array(
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = json_decode($this->putToApi($this->cashRegisterAuth, 'order', 'finalize', $data), true);
        
        $this->assertEquals($response['status'], Order::STATUS_COMPLETED);
    }
    
    /**
     * This test is used to remove a product form an order/receipt, given the
     * barcode of the product and the invoice number of the order/receipt
     */
    public function testRemoveProduct() {
        $product = $this->em->getRepository(Product::class)->find(1);
        $order= $this->em->getRepository(Order::class)->find(1);
        
        $data = array(
            'product_id' => $product->getBarcode(),
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = json_decode($this->putToApi($this->cashRegisterAuth, 'order', 'product/remove', $data), true);
        
        $this->assertArrayHasKey('invoiceNumber', $response);
    }
    
    /**
     * This test is used to get a single order/receipt, starting from aa suitable
     * invoice number
     */
    public function testGetSingle() {
        $order = $this->em
                ->getRepository(Order::class)
                ->find(1)
        ;
        $invoiceNumber = $order->getInvoiceNumber();
        
        $response = $this->getFromApi($this->cashRegisterAuth, 'order', 'get-single', array('id' => $invoiceNumber));
        $foundOrder = json_decode($response, true);
        
        $this->assertEquals($invoiceNumber, $foundOrder['invoiceNumber']);
    }
    
    /**
     * This test is used to get the turnover within the last hour.
     * 
     * The API is already set in order/receipt to retrieve the turnover within
     * last hour if no data is sent by GET request.
     * In order to change the date interval, it is possible to send GET query
     * according to this format:
     * [
     *      'startDate' => $startDate->format('c'),
     *      'endDate' => $endDate->format('c')
     * ]
     * where $startDate and $endDate are \DateTime objects. Obviously, you can
     * specify only one of these parameters. If $startDate > $endDate, dates
     * are switched
     */
    public function testGetTurnoverLastHour() {
        $response = json_decode($this->getFromApi($this->adminAuth, 'order', 'get-turnover'), true);
        
        $this->assertGreaterThanOrEqual($response['turnover'], 0);
    }
    
}

