<?php

namespace App\Security;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    private $entityManager;

    private $registry;

    private $router;

    public function __construct(EntityManagerInterface $manager, ClientRegistry $registry, RouterInterface $router)
    {
        $this->entityManager = $manager;
        $this->registry = $registry;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $name = $googleUser->getName();

        $client = $this->entityManager->getRepository('App:Client')
            ->findOneBy(['name' => $name]);

        if (!$client) {
            $user = new Client();
            $user->setName($name);
            $user->setSiret('11111111111');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $client;
    }


    /**
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2Client
     */
    private function getGoogleClient()
    {
        return $this->registry
            ->getClient('google');
    }


    public function start(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $authException = null)
    {
        return new RedirectResponse('/');
    }


    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception)
    {
        return new JsonResponse(['status' => 'Error']);
    }


    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $providerKey)
    {
        return new RedirectResponse('/');
    }
}

