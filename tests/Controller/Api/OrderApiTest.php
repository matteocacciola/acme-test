<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;
use App\Entity\Product;
use App\Entity\Order;

class OrderApiTest extends ApiTestCase {
    
    /**
     * 
     */
    public function testCreate() {
        $data = array(
            'shipping_address' => 'Street no.1, Amsterdam, NL'
        );
        
        $response = json_decode($this->postToApi($this->cashRegisterAuth, 'receipt', 'create', $data), true);
        
        $this->assertArrayHasKey('invoiceNumber', $response);
    }
    
    /**
     * 
     */
    public function testAddProduct() {
        $product = $this->em->getRepository(Product::class)->find(1);
        $order= $this->em->getRepository(Order::class)->find(1);
        
        $data = array(
            'product_id' => $product->getBarcode(),
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = json_decode($this->putToApi($this->cashRegisterAuth, 'receipt', 'product/add', $data), true);
        
        $this->assertArrayHasKey('invoiceNumber', $response);
    }
    
    /**
     * 
     */
    public function testIncreaseLastProduct() {
        $order = $this->em->getRepository(Order::class)->find(1);
        $lastItemQuantityBefore = $order->getItems()->last()->getQuantity();
        
        $data = array(
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = $this->putToApi($this->cashRegisterAuth, 'receipt', 'product/increase-last', $data);
        
        $lastItemQuantityAfter = $order->getItems()->last()->getQuantity();
        
        $this->assertEquals(1, ($lastItemQuantityAfter - $lastItemQuantityBefore));
    }
    
    /**
     * 
     */
    public function testFinalize() {
        $order = $this->em->getRepository(Order::class)->find(1);
        
        $data = array(
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = json_decode($this->putToApi($this->cashRegisterAuth, 'receipt', 'finalize', $data), true);
        
        $this->assertEquals($response['status'], Order::STATUS_COMPLETED);
    }
    
    /**
     * 
     */
    public function testRemoveProduct() {
        $product = $this->em->getRepository(Product::class)->find(1);
        $order= $this->em->getRepository(Order::class)->find(1);
        
        $data = array(
            'product_id' => $product->getBarcode(),
            'receipt_id' => $order->getInvoiceNumber()
        );
        
        $response = json_decode($this->putToApi($this->cashRegisterAuth, 'receipt', 'product/remove', $data), true);
        
        $this->assertArrayHasKey('invoiceNumber', $response);
    }
    
    /**
     * 
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
    
}

