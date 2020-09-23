<?php

namespace App\Security;

use phpCAS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class CASAuthenticator extends AbstractGuardAuthenticator
{

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * \brief Nécessaire pour récupérer le dossier du projet, et le chemin du certificat du CAS
     *
     * @var KernelInterface
     */
    private $kernel;

    /**
     * UtilisateurCASAuthenticator constructor.
     *
     * @param UrlGeneratorInterface  $urlGenerator  autowiré
     * @param KernelInterface        $kernel        autowiré
     */
    public function __construct(UrlGeneratorInterface $urlGenerator,
                                KernelInterface $kernel)
    {
        $this->urlGenerator  = $urlGenerator;
        $this->kernel        = $kernel;
    }


    public function supports(Request $request)
    {
        return 'connexion_cas' === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        phpCAS::setVerbose(false);
        if ( !phpCAS::isInitialized()) {
            phpCAS::client(
                CAS_VERSION_2_0,
                "cas.utt.fr",
                443,
                "/cas"
            );
        }

        phpCAS::setCasServerCACert($this->kernel->getProjectDir() . "/config/certificates/utt-cas-chain.pem", true);
        phpCAS::setFixedServiceURL($this->getLoginUrl());

        phpCAS::forceAuthentication();

        // Return User
        if (phpCAS::getUser()) {
            return phpCAS::getUser();
        }

        return NULL;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $credentials === $user->getUsername();
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        );

        return new JsonResponse($data, 403);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse("https://cas.utt.fr/cas/login?service=" . $this->getLoginUrl());
        //return null;
    }

    /**
     * Generate the Login URL in router.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('connexion_cas', [], $this->urlGenerator::ABSOLUTE_URL);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
