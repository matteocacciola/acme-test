<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Product;
use App\Form\Type\ProductType;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController {

    /**
     * 
     * Add a product
     * 
     * @Route("/add", name="acme.api.product.add")
     * @Method({"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request) {
        try {
            $product = new Product();
            
            $data = $request->request->all();
            $form = $this->createForm(ProductType::class, $product);
            $form->submit($data);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            
            $code = Response::HTTP_OK;
            $body = $product->serialize();
        } catch (\Exception $ex) {
            $code = ($ex->getCode() > 0) ? $ex->getCode() : $ex->getStatusCode();
            $body = $ex->getMessage();
        }
        
        return $this->json($body, $code);
    }
    
    /**
     * 
     * Get all the products
     * 
     * @Route("/get-all", name="acme.api.product.get-all")
     * @Method({"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function getAll() {
        $products = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository(Product::class)
                ->findAll()
        ;
        $body = array();
        foreach ($products as $product) {
            $body[] = $product->serialize();
        }
        
        return $this->json($body);
    }
    
    /**
     * 
     * Get a single product
     * 
     * @Route("/get-single", name="acme.api.product.get-single")
     * @Method({"GET"})
     * @IsGranted("ROLE_CASH_REGISTER")
     */
    public function getSingle(Request $request) {
        $barcode = $request->get('id');
        
        $product = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository(Product::class)
                ->findOneBy(array('barcode' => $barcode))
        ;
        
        return $this->json($product->serialize());
    }

}
