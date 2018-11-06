<?php

namespace App\Tests\Traits;

class ApiActionsTrait {

    /**
     * 
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     * @throws \Exception
     */
    protected function getFromApi(string $module, string $action, array $toSendData = []) {
        $response = $this->getClientRequest($module, $action, $toSendData);

        try {
            return $response->send()->json();
        } catch (GuzzleException $ex) {
            throw new \Exception('Could not parse: ' . $response->getResponse()->getBody());
            // throw $ex;
        }
    }

    /**
     * 
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     * @throws \Exception
     */
    protected function postToApi(string $module, string $action, array $toSendData = []) {
        $response = $this->postClientRequest($module, $action, $toSendData);
        try {
            return $response->send()->json();
        } catch (GuzzleException $ex) {
            throw new \Exception('Could not parse: ' . $response->getResponse()->getBody());
        }
    }
    
    /**
     * 
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     * @throws \Exception
     */
    protected function putToApi(string $module, string $action, array $toSendData = []) {
        $response = $this->putClientRequest($module, $action, $toSendData);
        try {
            return $response->send()->json();
        } catch (GuzzleException $ex) {
            throw new \Exception('Could not parse: ' . $response->getResponse()->getBody());
        }
    }
    
    /**
     * 
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     */
    private function getClientRequest(string $module, string $action, array $toSendData = []) {
        $client = new \GuzzleHttp\Client();

        $requestUrl = $this->getParameter('api_domain') . '/api/' . $module . '/' . $action . '?' . http_build_query($toSendData);
        $apiRequest = $client->get($requestUrl);

        return $apiRequest;
    }

    /**
     * 
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     */
    private function postClientRequest(string $module, string $action, array $toSendData = []) {
        $client = new \GuzzleHttp\Client();
        $apiRequest = $client->post(
                $this->getParameter('api_domain') . '/api/' . $module . '/' . $action,
                null,
                $toSendData
        );

        return $apiRequest;
    }
    
    /**
     * 
     * @param string $module
     * @param string $action
     * @param array $toSendData
     * @return type
     */
    private function putClientRequest(string $module, string $action, array $toSendData = []) {
        $client = new \GuzzleHttp\Client();
        $apiRequest = $client->put(
                $this->getParameter('api_domain') . '/api/' . $module . '/' . $action,
                array('body' => json_encode($toSendData))
        );

        return $apiRequest;
    }

}
