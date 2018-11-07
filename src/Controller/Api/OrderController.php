<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Manager\OrderManager;
use App\Manager\ProductManager;
use App\Entity\Order;
use App\Entity\Product;

/**
 * @Route("/receipt")
 */
class OrderController extends AbstractController {

    /**
     * 
     * Create a new order
     * 
     * @Route("/create", name="acme.api.receipt.create")
     * @Method({"POST"})
     * @IsGranted("ROLE_CASH_REGISTER")
     */
    public function create(Request $request) {
        try {
            $shippingAddress = $request->get('shipping_address');

            if (!($shippingAddress)) {
                throw new \Exception('Shipping address is required for a receipt', Response::HTTP_BAD_REQUEST);
            }

            $billingAddress = $request->get('billing_address', $shippingAddress);

            /** @var OrderManager */
            $manager = $this->get('app.manager.order');
            $manager->createNew($shippingAddress, $billingAddress);

            $code = Response::HTTP_OK;
            $body = $order->serialize();
        } catch (\Exception $ex) {
            $code = ($ex->getCode() > 0) ? $ex->getCode() : $ex->getStatusCode();
            $body = $ex->getMessage();
        }

        return $this->json($body, $code);
    }

    /**
     * 
     * Add a product to an order
     * 
     * @Route("/product/add", name="acme.api.receipt.add_product")
     * @Method({"PUT"})
     * @IsGranted("ROLE_CASH_REGISTER")
     */
    public function addProduct(Request $request) {
        try {
            $putStr = $request->getContent();
            parse_str($putStr, $data);
            
            /** @var ProductManager */
            $manager = $this->get('app.manager.product');
            $product = $manager->findOneByBarcodeOrThrowException($data, 'product_id');
            
            /** @var OrderManager */
            $manager = $this->get('app.manager.order');
            $order = $manager->findOneByInvoiceNumberOrThrowException($data, 'receipt_id');
            
            $manager->addItem($product, $order);

            $code = Response::HTTP_OK;
            $body = $order->serialize();
        } catch (\Exception $ex) {
            $code = ($ex->getCode() > 0) ? $ex->getCode() : $ex->getStatusCode();
            $body = $ex->getMessage();
        }

        return $this->json($body, $code);
    }

    /**
     * 
     * Change the amount of the last product on a receipt
     * 
     * @Route("/product/increase-last", name="acme.api.receipt.increase_last_product")
     * @Method({"PUT"})
     * @IsGranted("ROLE_CASH_REGISTER")
     */
    public function increaseLastProduct(Request $request) {
        try {
            $putStr = $request->getContent();
            parse_str($putStr, $data);
            
            /** @var OrderManager */
            $manager = $this->get('app.manager.order');
            $order = $manager->findOneByInvoiceNumberOrThrowException($data, 'receipt_id');
            
            $manager->increaseItemQuantity($order->getItems()->last(), $order);

            $code = Response::HTTP_OK;
            $body = $order->serialize();
        } catch (\Exception $ex) {
            $code = ($ex->getCode() > 0) ? $ex->getCode() : $ex->getStatusCode();
            $body = $ex->getMessage();
        }

        return $this->json($body, $code);
    }

    /**
     * 
     * Finalize a receipt
     * 
     * @Route("/finalize", name="acme.api.receipt.finalize")
     * @Method({"PUT"})
     * @IsGranted("ROLE_CASH_REGISTER")
     */
    public function finalize(Request $request) {
        try {
            $putStr = $request->getContent();
            parse_str($putStr, $data);

            /** @var OrderManager */
            $manager = $this->get('app.manager.order');
            $order = $manager->findOneByInvoiceNumberOrThrowException($data, 'receipt_id');
            
            $manager->finalize($order);

            $code = Response::HTTP_OK;
            $body = $order->serialize();
        } catch (\Exception $ex) {
            $code = ($ex->getCode() > 0) ? $ex->getCode() : $ex->getStatusCode();
            $body = $ex->getMessage();
        }

        return $this->json($body, $code);
    }
    
}
