<?php

namespace Kicker\Http;

class TableJsonResponse extends JsonResponse
{
    public function __construct($data = array(), $status = 200, $headers = array())
    {
        parent::__construct(['rows' => $data], $status, $headers);
    }

}