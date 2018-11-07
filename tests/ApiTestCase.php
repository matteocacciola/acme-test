<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use GuzzleHttp\Exception\GuzzleException;

class ApiTestCase extends KernelTestCase {

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;
    
    /**
     *
     * @var \Doctrine\Common\Persistence\ObjectManager 
     */
    protected $em;
    
    /**
     *
     * @var string
     */
    protected $baseUrl;

    /**
     *
     * @var array
     */
    protected $adminAuth = array(
        '_username' => 'admin@acme.com',
        '_password' => 'test'
    );
    
    /**
     *
     * @var array
     */
    protected $cashRegisterAuth = array(
        '_username' => 'cashier@acme.com',
        '_password' => 'test',
    );

    /**
     * 
     */
    protected function setUp() {
        self::bootKernel();
        
        $this->client = new \GuzzleHttp\Client();
        $this->em = $this->getService('doctrine.orm.entity_manager');
        $this->baseUrl = $this->getParameter('api_domain') . DIRECTORY_SEPARATOR . 'api';
    }

    /**
     * 
     * @param string $id
     * @return mixed
     */
    protected function getService($id) {
        return self::$kernel->getContainer()->get($id);
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    protected function getParameter($name) {
        return self::$kernel->getContainer()->getParameter($name);
    }

    /**
     * 
     * @param array $auth array('_username' => username_of_user, '_password' => password_of_user)
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     * @throws \Exception
     */
    protected function getFromApi(array $auth, string $module, string $action, array $toSendData = []) {
        try {
            $response = $this->getClientRequest($auth, $module, $action, $toSendData);
            return $response->getBody();
        } catch (GuzzleException $ex) {
            throw $ex;
        }
    }

    /**
     * 
     * @param array $auth array('_username' => username_of_user, '_password' => password_of_user)
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     * @throws \Exception
     */
    protected function postToApi(array $auth, string $module, string $action, array $toSendData = []) {
        try {
            $response = $this->postClientRequest($auth, $module, $action, $toSendData);
            return $response->getBody();
        } catch (GuzzleException $ex) {
            throw $ex;
        }
    }

    /**
     * 
     * @param array $auth  array('_username' => username_of_user, '_password' => password_of_user)
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     * @throws \Exception
     */
    protected function putToApi(array $auth, string $module, string $action, array $toSendData = []) {
        try {
            $response = $this->putClientRequest($auth, $module, $action, $toSendData);
            return $response->getBody();
        } catch (GuzzleException $ex) {
            throw $ex;
        }
    }

    /**
     * 
     * @param array $auth  array('_username' => username_of_user, '_password' => password_of_user)
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     */
    private function getClientRequest(array $auth, string $module, string $action, array $toSendData = []) {
        $apiRequest = $this->client->request(
                'GET',
                $this->baseUrl . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $action,
                array(
                    'query' => $toSendData,
                    'headers' => $this->getAuthorizedHeaders($auth)
                )
        );

        return $apiRequest;
    }

    /**
     * 
     * @param array $auth  array('_username' => username_of_user, '_password' => password_of_user)
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     */
    private function postClientRequest(array $auth, string $module, string $action, array $toSendData = []) {
        $apiRequest = $this->client->request(
                'POST',
                $this->baseUrl . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $action,
                array(
                    'form_params' => $toSendData,
                    'headers' => $this->getAuthorizedHeaders($auth)
                )
        );

        return $apiRequest;
    }

    /**
     * 
     * @param array $auth  array('_username' => username_of_user, '_password' => password_of_user)
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     */
    private function putClientRequest(array $auth, string $module, string $action, array $toSendData = []) {
        $apiRequest = $this->client->request(
                'PUT',
                $this->baseUrl . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $action,
                array(
                    'form_params' => $toSendData,
                    'headers' => $this->getAuthorizedHeaders($auth)
                )
        );

        return $apiRequest;
    }

    /**
     * 
     * @param array $auth array('_username' => username_of_user, '_password' => password_of_user)
     * @param array $headers
     * @return array
     */
    private function getAuthorizedHeaders(array $auth, array $headers = array()): array {
        $apiRequest = $this->client->request(
                'GET',
                $this->baseUrl . DIRECTORY_SEPARATOR . 'authentication/token',
                array('query' => $auth)
        );
        $token = json_decode($apiRequest->getBody(), true)['token'];
        
        $headers = array_merge(
                $headers,
                array('Authorization' => 'Bearer ' . $token)
        );
        return $headers;
    }

}
