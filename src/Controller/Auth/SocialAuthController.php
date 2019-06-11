<?php


namespace App\Controller\Auth;


use App\Controller\AppController;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SocialAuthController
 *
 * This controller handles all authentication from social logins.
 *
 * @package App\Controller\Auth
 * @Route("/social", methods={"post"}, name="social_")
 */
class SocialAuthController extends AppController
{

    /**
     * @Route("/login", name="login")
     */
    public function login(){
        $name = $this->request->request->get('name');
        $email = $this->request->request->get('email');
        $id = $this->request->request->get('id');

        $user = $this->objectManager->getRepository(User::class)
            ->findBy(['email' => $email]);
    }
}