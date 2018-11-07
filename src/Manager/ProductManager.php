<?php

namespace App\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductManager {

    /** @var ObjectManager */
    private $em;

    public function __construct(ObjectManager $em) {
        $this->em = $em;
    }
    
    /**
     * 
     * @param string|array $barcode
     * @return Product
     * @throws \Exception
     */
    public function findOneByBarcodeOrThrowException($barcode, $key = null) {
        if ($key) {
            if (is_array($barcode) && array_key_exists($key, $barcode)) {
                $barcode = $barcode[$key];
            } else {
                $barcode = null;
            }
        }
        
        if (!($barcode)) {
            throw new \Exception('Barcode is mandatory', Response::HTTP_BAD_REQUEST);
        }
        
        $product = $this->em->getRepository(Order::class)->findOneBy(array(
            'barcode' => $barcode
        ));
        if (!($product)) {
            throw new \Exception('Product not found', Response::HTTP_NOT_FOUND);
        }
        
        return $product;
    }
    
    /**
     * 
     * @param Product $product
     * @param boolean $persistent
     */
    public function save(Product $product, $persistent = true) {
        if ($persistent === true) {
            $this->em->persist($product);
            $this->em->flush();
        }
    }

}
