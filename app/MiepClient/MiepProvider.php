<?php

namespace App\MiepClient;

use League\OAuth2\Client\Provider\GenericProvider;

class MiepProvider extends GenericProvider
{
    protected function getAccessTokenMethod()
    {
        return 'GET';
    }
}
