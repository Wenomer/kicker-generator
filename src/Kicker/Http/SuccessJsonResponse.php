<?php

namespace Kicker\Http;

class SuccessJsonResponse extends JsonResponse
{
    public function __construct($data = array(), $status = 200, $headers = array())
    {
        parent::__construct(['data' => $data, 'success' => true], $status, $headers);
    }

}
