<?php

namespace tronet\FrontappMailer\Api;

use Illuminate\Http\Client\Response;
use tronet\FrontappMailer\Builders\MessageBuilder;


/**
 * Frontapp messages API services
 */

class MessagesApi
{
    public function __construct(
        protected HttpClient $client,
        protected array $config,
    ) {}

    public function build(): MessageBuilder
    {
        return new MessageBuilder($this->config);
    }

    public function validate(MessageBuilder $message): MessageBuilder
    {
        return $message->validate();
    }

    public function send(MessageBuilder $message): Response
    {
        $endpoint = 'channels/' . $message->getChannelId() . '/messages';
        return $this->client->post($endpoint, $message->getHttpBody());
    }
}