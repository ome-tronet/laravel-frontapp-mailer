<?php

namespace tronet\FrontappService\Mail\Transports;

use Symfony\Component\Mailer\Header\TagHeader;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use tronet\FrontappMailer\FrontappService;

/**
 * Frontapp mail driver for Laravel
 */

class FrontappTransport extends AbstractTransport
{
    public function __construct(
        protected FrontappService $front
    )
    {
        parent::__construct();
        $this->setMaxPerSecond(1);
    }

    protected function doSend(SentMessage $message): void
    {
        $emailMessage = MessageConverter::toEmail($message->getOriginalMessage());

        $tags = [];
        foreach ($emailMessage->getHeaders()->all() as $header) {
            if ($header instanceof TagHeader) {
                $tags[] = $header->getValue();
            }
        }

        $this->front->messages->send(
            $this->front->messages->validate(
                $this->front->messages->build()
                    ->from(collect($emailMessage->getFrom())->map->toString()->values()->first())
                    ->to(collect($emailMessage->getTo())->map->toString()->values()->all())
                    ->cc(collect($emailMessage->getCc())->map->toString()->values()->all())
                    ->bcc(collect($emailMessage->getBcc())->map->toString()->values()->all())
                    ->subject($emailMessage->getSubject())
                    ->body($emailMessage->getHtmlBody())
                    ->text($emailMessage->getTextBody())
                    ->withTags($tags)
                    ->archiveWhenSent()
            )
        );
    }

    public function __toString()
    {
        return 'front';
    }
}