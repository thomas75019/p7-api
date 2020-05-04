<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractFOSRestController
{
    /**
     *
     * @Route("/connect/google", name="connect_google")
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    /**
     *
     * @Route("/connect/google/check", name="connect_google_check")
     *
     * @return Response
     */
    public function connectCheck()
    {
        if (!$this->getUser()) {
            return $this->handleView($this->view(['status' => 'User not found'], Response::HTTP_NOT_FOUND));
        } else {
            return $this->handleView($this->view(['status' => 'connexion made'], Response::HTTP_OK));
        }

    }

}

