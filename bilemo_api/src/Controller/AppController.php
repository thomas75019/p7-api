<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use App\ErrorHandler\FormErrorHandler;
use App\Form\UserType;
use App\Normalizer\NormalizerInterface;
use App\Repository\UserRepository;
use App\Service\CacheHandler;
use function Clue\StreamFilter\append;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SerializerBundle\Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     *     description="Returns all products"
     *     )
     * )
     */
    public function getProducts(CacheHandler $handler) : Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        if ($products)
        {
            $response = $this->handleView($this->view($products, Response::HTTP_OK));

            $handler->startCache($response)->setEtag($response->getContent())->setSharedMaxAge(10);

            return $response;
        }

        return $this->handleView($this->view([Response::HTTP_NOT_FOUND => 'No products found'], Response::HTTP_NOT_FOUND));
    }

    /**
     * Get One product
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Get("/product/{id}", name="get_one_product")
     *
     *
     * @SWG\Response(
     *    response=200,
     *    description="Returns one product"
     *   )
     * )
     *
     */
    public function getOneProduct(Request $request) : Response
    {
        $product_id = $request->get('id');
        $product = $this->getDoctrine()->getRepository(Product::class)->find($product_id);

        if (!is_int($product_id))
        {
            return $this->handleView($this->view([Response::HTTP_BAD_REQUEST => 'URL is not valid'], Response::HTTP_BAD_REQUEST));
        }

        if ($product)
        {
            return $this->handleView($this->view($product, Response::HTTP_OK));
        }

        return $this->handleView($this->view([Response::HTTP_NOT_FOUND => 'Product not found'], Response::HTTP_NOT_FOUND));
    }

    /**
     * Get All Users
     *
     * @Rest\Get("/users", name="get_all_users")
     *
     * @SWG\Response(
     *    response=200,
     *    description="Returns all users"
     *   )
     * )
     */
    public function getAllUsers(CacheHandler $handler, UserRepository $repository, Request $request) : Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $page = $request->get('page');
        $users = iterator_to_array($repository->findAllUsers($page, 10));

        if ($users)
        {
            $response = $this->handleView($this->view($users, Response::HTTP_OK));

            $handler->startCache($response)->setEtag($response->getContent())->setSharedMaxAge(10);

            return $response;
        }

        return $this->handleView($this->view([Response::HTTP_NOT_FOUND => 'No users found'], Response::HTTP_NOT_FOUND));


    }

    /**
     * Get One user
     *
     * @param Request $request Request
     *
     * @return Response
     *
     * @Rest\Get("/user/{id}", name="get_one_user")
     *
     * @SWG\Response(
     *    response=200,
     *    description="Returns one user"
     *   )
     * )
     */
    public function getOneUser(Request $request) : Response
    {
        $id_user = $request->get('id');

        if (!is_int($id_user))
        {
            return $this->handleView($this->view([Response::HTTP_BAD_REQUEST => 'URL is not valid'], Response::HTTP_BAD_REQUEST));
        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($id_user);

        if ($user)
        {
            return $this->handleView($this->view($user, Response::HTTP_OK));
        }

        return $this->handleView($this->view([Response::HTTP_NOT_FOUND => 'User not found']));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\Post("/users", name="create_user")
     *
     * @SWG\Response(
     *    response=201,
     *    description="Create an user"
     *   )
     * )
     */
    public function createUser(Request $request, ValidatorInterface $validator, \Symfony\Component\Serializer\Normalizer\NormalizerInterface $normalizer) : Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isValid() && $form->isSubmitted())
        {
            //This is just in a purpose of test
            $user->setClient($this->getUser());
            $user->hydrate($data);

            $entity_manager = $this->getDoctrine()->getManager();

            $entity_manager->persist($user);
            $entity_manager->flush();

            return $this->handleView($this->view(["Status" => "Created"], Response::HTTP_CREATED));
        }

        $errors = $validator->validate($form);

        return $this->handleView($this->view($errors->get(0), Response::HTTP_BAD_REQUEST));

    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Rest\Delete("/user/{id}", name="delete_user")
     *
     * @SWG\Response(
     *    response=204,
     *    description="Removes an user"
     *   )
     * )
     */
    public function removeUser(Request $request) : Response
    {
        $user_id = $request->get('id');

        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);

        if (!$user)
        {
            return $this->handleView($this->view([Response::HTTP_BAD_REQUEST => 'Bad Request'], Response::HTTP_BAD_REQUEST));
        }


        $entity_manager = $this->getDoctrine()->getManager();

        $entity_manager->remove($user);
        $entity_manager->flush();

        return $this->handleView($this->view(["Status" => "Removed"], Response::HTTP_NO_CONTENT));
    }
}