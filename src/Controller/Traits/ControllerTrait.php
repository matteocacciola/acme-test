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
        if ($ex->getCode() > 0) {
            $code = $ex->getCode();
        } else {
            $code = method_exists($ex, 'getStatusCode')
                    ? $ex->getStatusCode()
                    : Response::HTTP_INTERNAL_SERVER_ERROR
            ;
        }
        
        return $code;
    }

}
