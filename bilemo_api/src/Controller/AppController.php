<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
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
     * @Rest\Get("/product/{id}")
     */
    public function getOneProduct(Request $request) : Response
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($request->get('id'));

        return $this->handleView($this->view($product, 200));
    }

    /**
     * Get All Users
     *
     * @Rest\Get("/users")
     */
    public function getAllUsers() : Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->handleView($this->view($users, 200));
    }

    /**
     * Get One user
     *
     * @param Request $request Request
     *
     * @return Response
     *
     * @Rest\Get("/user/{id}")
     */
    public function getOneUser(Request $request) : Response
    {
        $id_user = $request->get('id');

        $user = $this->getDoctrine()->getRepository(User::class)->find($id_user);

        return $this->handleView($this->view($user, 200));
    }

    /**
     * @Rest\Post("/users")
     */
    public function createUser(Request $request)
    {
        $user = new User();
        $client = $this->getDoctrine()->getRepository(Client::class)->find(1);

        $form = $this->createForm(UserType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isValid() && $form->isSubmitted())
        {
            $user->hydrate($data);
            $user->setClient($client);

            $entity_manager = $this->getDoctrine()->getManager();

            $entity_manager->persist($user);
            $entity_manager->flush();

            return $this->handleView($this->view(["Status" => "Created"], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }
}