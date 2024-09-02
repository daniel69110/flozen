<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private RouterInterface $router, private Security $security) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // if ($this->security->isGranted('ROLE_ADMIN')) {
        //     $targetUrl = $this->router->generate('app_ad');
        // } else {
        $targetUrl = $this->router->generate('infos'); // Remplacez par la route de votre choix pour les non-admins


        return new RedirectResponse($targetUrl);
    }
}
