<?php

namespace tronet\FrontappMailer;

use tronet\FrontappMailer\Api\HttpClient;
use tronet\FrontappMailer\Api\MessagesApi;

/**
 * Frontapp API service
 */

class FrontappService
{
    protected HttpClient $client;
    public MessagesApi $messages;

    public function __construct(array $config)
    {
        $this->client = new HttpClient($config['api_base_url'], $config['api_token']);
        $this->messages = new MessagesApi($this->client, $config);
    }

}