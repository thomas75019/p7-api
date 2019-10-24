<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * App Controller
 *
 * @Route("/api",name="api")
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

        return $this->handleView($this->view($products, Response::HTTP_OK));
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

        return $this->handleView($this->view($product, Response::HTTP_OK));
    }

    /**
     * Get All Users
     *
     * @Rest\Get("/users")
     */
    public function getAllUsers() : Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->handleView($this->view($users, Response::HTTP_OK));
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

        return $this->handleView($this->view($user, Response::HTTP_OK));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\Post("/users")
     */
    public function createUser(Request $request) : Response
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

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\Delete("/user/{id}")
     */
    public function removeUser(Request $request) : Response
    {
        $user_id = $request->get('id');

        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);

        $entity_manager = $this->getDoctrine()->getManager();

        $entity_manager->remove($user);
        $entity_manager->flush();

        return $this->handleView($this->view(["Status" => "Removed"], Response::HTTP_NO_CONTENT));
    }
}