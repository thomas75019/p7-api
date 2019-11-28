<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SerializerBundle\Serializer;
use Nelmio\ApiDocBundle\SwaggerPhp as SWG;

/**
 * App Controller
 *
 * @Route("/api",name="api_")
 */
class AppController extends AbstractFOSRestController
{

    /**
     * Get All Products
     *
     * @Rest\Get("/products", name="get_products")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Reward::class, groups={"full"}))
     *     )
     * )
     *
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
     * @Get("/product/{id}", name="get_one_product")
     */
    public function getOneProduct(Request $request) : Response
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($request->get('id'));

        return $this->handleView($this->view($product, Response::HTTP_OK));
    }

    /**
     * Get All Users
     *
     * @Rest\Get("/users", name="get_all_users")
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
     * @Rest\Get("/user/{id}", name="get_one_user")
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
     * @Rest\Post("/users", name="create_user")
     */
    public function createUser(Request $request) : Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isValid() && $form->isSubmitted())
        {
            $user->setClient($this->getUser());
            $user->hydrate($data);

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
     * @Rest\Delete("/user/{id}", name="delete_user")
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