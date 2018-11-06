<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\User\UserChecker;
use App\Entity\User;

/**
 * @Route("/authentication")
 */
class AuthenticationController extends AbstractController {

    /**
     * @Route("/token", name="acme.api.authentication.token")
     * @Method({"GET"})
     */
    public function token(Request $request) {
        try {
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

            $body = array('token' => $token);
            $code = Response::HTTP_OK;
        } catch (\Exception $ex) {
            $code = ($ex->getCode() > 0) ? $ex->getCode() : $ex->getStatusCode();
            $body = $ex->getMessage();
        }
        
        // Return genereted token
        return $this->json($body, $code);
    }

}
