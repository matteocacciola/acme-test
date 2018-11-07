<?php

namespace App\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait {

    /**
     * 
     * @param \Exception $ex
     * @return string
     */
    protected function getExceptionCode(\Exception $ex) {
        $code = ($ex->getCode() > 0)
                ? $ex->getCode()
                : method_exists($ex, 'getStatusCode') ? $ex->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        
        return $code;
    }

}
