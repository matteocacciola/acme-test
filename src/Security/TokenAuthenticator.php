<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use App\Entity\User;

class TokenAuthenticator extends AbstractGuardAuthenticator {

    /** @var EntityManagerInterface $em */
    private $em;
    
    /** @var JWTEncoderInterface $jwtEncoder */
    private $jwtEncoder;

    /**
     * 
     * @param EntityManagerInterface $em
     * @param JWTEncoderInterface $jwtEncoder
     */
    public function __construct(EntityManagerInterface $em, JWTEncoderInterface $jwtEncoder) {
        $this->em = $em;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Called when authentication is needed, but it's not sent
     * 
     * @param Request $request
     * @param AuthenticationException $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        if (!$request->headers->has('Authorization')) {
            return null;
        }

        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');

        $token = $extractor->extract($request);

        if (!$token) {
            return null;
        }

        return $token;
    }

    /**
     * 
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $data = $this->jwtEncoder->decode($credentials);

        if (!$data){
            return null;
        }

        $email = $data['username'];

        $user = $this->em
                ->getRepository(User::class)
                ->findOneBy(array('email' => $email))
        ;

        if (!$user){
            return null;
        }

        return $user;
    }

    /**
     * 
     * @param mixed $credentials
     * @param UserInterface $user
     * @return boolean
     */
    public function checkCredentials($credentials, UserInterface $user) {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        // return true to cause authentication success
        return true;
    }

    /**
     * 
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

                // or to translate this message
                // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request) {
        return $request->headers->has('Authorization');
    }

    /**
     * 
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        // on success, let the request continue
        return null;
    }

    /**
     * 
     * @return boolean
     */
    public function supportsRememberMe() {
        return false;
    }

}
