<?php

namespace Phabricator\Api;

use Phabricator\Client;

abstract class Api
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

}
