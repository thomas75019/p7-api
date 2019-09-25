<?php

namespace App\Controller;

use App\Entity\Product;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Product Controller
 *
 * @Route("/api",name="api_")
 */
class AppController extends AbstractFOSRestController
{

    /**
     * Get All Products
     *
     * @Rest\Get("/products")
     */
    public function getProducts() : Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->handleView($this->view($products, 200));
    }

    /**
     * Get One product
     *
     * @param Request $request Request
     *
     * @return Response
     *
     * @Rest\Get("product/{id}")
     */
    public function getOneProduct(Request $request) : Response
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($request->get('id'));

        return $this->handleView($this->view($product, 200));

    }
}