<?php

namespace tronet\FrontappMailer\Builders;

use Illuminate\Support\Facades\Validator;
use tronet\FrontappMailer\Exceptions\InvalidMessageException;

/**
 * Message object builder for Frontapp API
 */

class MessageBuilder
{
    protected array $httpBody = [];
    protected ?string $channelId;

    public function __construct(protected array $config)
    {}

    public function from($sender): MessageBuilder
    {
        $this->setChannelId($this->config['senders'][$sender]['channel_id'] ?? null);

        $authorId = $this->config['senders'][$sender]['author_id'] ?? null;
        if (!is_null($authorId)) {
            $this->httpBody['author_id'] = $authorId;
        }
        return $this;
    }

    public function subject(string $subject): MessageBuilder
    {
        if ($subject !== '') {
            $this->httpBody['subject'] = $subject;
        }
        return $this;
    }

    public function text(string $text): MessageBuilder
    {
        if ($text !== '') {
            $this->httpBody['text'] = $text;
        }
        return $this;
    }

    public function body(string $body): MessageBuilder
    {
        if ($body !== '') {
            $this->httpBody['body'] = $body;
        }
        return $this;
    }

    public function withTag(string $tag): MessageBuilder
    {
        if ($tag !== '') {
            $this->httpBody['options']['tag_ids'][] = $tag;
            $this->httpBody['options']['tag_ids'] = array_values(array_unique($this->httpBody['options']['tag_ids']));
        }
        return $this;
    }

    public function withTags(array $tags): MessageBuilder
    {
        foreach($tags as $tag) {
            $this->withTag($tag);
        }
        return $this;
    }

    public function archiveWhenSent(): MessageBuilder
    {
        $this->httpBody['options']['archive'] = true;
        return $this;
    }

    protected function isValidEmail(string $email): bool
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email:rfc'
        ]);
        return $validator->passes();
    }

    protected function addRecipients(string $type, array|string $recipient): MessageBuilder
    {
        if (is_array($recipient)) {
            foreach($recipient as $element) {
                if (!empty($element) && $this->isValidEmail($element)) {
                    $this->httpBody[$type][] = $element;
                }
            }
        } else {
            if (!empty($recipient) && $this->isValidEmail($recipient)) {
                $this->httpBody[$type][] = $recipient;
            }
        }

        if (array_key_exists($type, $this->httpBody)) {
            $this->httpBody[$type] = array_values(array_unique($this->httpBody[$type]));
        }

        return $this;
    }

    public function to(array|string $to): MessageBuilder
    {
        return $this->addRecipients('to', $to);
    }

    public function cc(array|string $cc): MessageBuilder
    {
        return $this->addRecipients('cc', $cc);
    }

    public function bcc(array|string $bcc): MessageBuilder
    {
        return $this->addRecipients('bcc', $bcc);
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    public function setChannelId(?string $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function getHttpBody(): array
    {
        return $this->httpBody;
    }

    public function validate(): MessageBuilder
    {
        $validator = Validator::make(['channelId' => $this->getChannelId()] + $this->getHttpBody(),[
            'channelId' => 'required',
            'to' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            throw new InvalidMessageException($validator->errors());
        }

        return $this;
    }

}