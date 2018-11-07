<?php

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;
use App\Entity\VatClass;
use App\Entity\Product;

class ProductApiTest extends ApiTestCase {
    
    /**
     * 
     */
    public function testAddProduct() {
        $vat = $this->em
                ->getRepository(VatClass::class)
                ->findOneBy(array('percentage' => VatClass::VAT_TWENTYONE_PERCENT))
        ;
        
        $data = array(
            'barcode' => '0123456789ABCDEF',
            'name' => 'Router',
            'cost' => 10,
            'vatClass' => $vat->getId()
        );
        
        $response = json_decode($this->postToApi($this->adminAuth, 'product', 'add', $data), true);
        
        $this->assertArrayHasKey('barcode', $response);
    }
    
    /**
     * 
     */
    public function testGetAllProducts() {
        $response = $this->getFromApi($this->adminAuth, 'product', 'get-all');
        $products = json_decode($response, true);
        
        $this->assertGreaterThanOrEqual(0, count($products));
    }
    
    /**
     * 
     */
    public function testGetSingleProduct() {
        $product = $this->em
                ->getRepository(Product::class)
                ->find(1)
        ;
        $barcode = $product->getBarCode();
        
        $response = $this->getFromApi($this->cashRegisterAuth, 'product', 'get-single', array('id' => $barcode));
        $foundProduct = json_decode($response, true);
        
        $this->assertEquals($barcode, $foundProduct['barcode']);
    }
    
}

