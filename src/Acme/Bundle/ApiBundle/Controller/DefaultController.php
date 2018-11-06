<?php

namespace Acme\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\User\UserChecker;
use Acme\Bundle\UserBundle\Entity\User;

class DefaultController extends AbstractController {

    /**
     * @Route("/token", name="acme.api.token_authentication")
     * @Method({"POST"})
     */
    public function token(Request $request) {
        $username = $request->get('_username');
        $password = $request->get('_password');

        $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(array('email' => $username))
        ;

        if (!$user) {
            throw $this->createNotFoundException(sprintf('User %s not found.', $username));
        }

        $userChecker = new UserChecker();
        $userChecker->checkPreAuth($user);

        if (!$this->get('security.password_encoder')->isPasswordValid($user, $password)) {
            throw $this->createAccessDeniedException(sprintf('Invalid data passed for the user %s.', $username));
        }

        $userChecker->checkPostAuth($user);

        $token = $this->get('lexik_jwt_authentication.encoder')->encode(array('username' => $user->getEmail()));

        // Return genereted token
        return $this->json(array('token' => $token));
    }

}
