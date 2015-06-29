<?php

namespace Kicker\Http;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response
{
    /**
     * @param array|object|mixed $data
     * @param int                $status
     * @param array              $headers
     */
    public function __construct($data = [], $status = 200, $headers = array())
    {
        parent::__construct(json_encode($data, JSON_NUMERIC_CHECK), $status, $headers);
    }
}