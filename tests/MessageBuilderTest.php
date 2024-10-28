<?php

namespace tronet\FrontappService\Tests;

use Orchestra\Testbench\TestCase;
use tronet\FrontappMailer\Builders\MessageBuilder;
use tronet\FrontappMailer\Exceptions\InvalidMessageException;

/**
 * @covers \tronet\FrontappMailer\Builders\MessageBuilder
 */
class MessageBuilderTest extends TestCase
{
    public array $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'transport' => 'front',
            'senders' => [
                'available_sender_with_author@tro.net' => [
                    'channel_id' => 'expected_channel_id',
                    'author_id' => 'expected_author_id'
                ],
                'available_sender_without_author@tro.net' => [
                    'channel_id' => 'expected_channel_id',
                ],
            ]
        ];
    }

    public function test_fresh_builders_gethttpbody_returns_empty_array(): void
    {
        $result = (new MessageBuilder($this->config))->getHttpBody();
        $this->assertTrue($result === [],
            'Should return empty array');

    }

    public function test_not_empty_subject_is_added_to_body(): void
    {
        $result = (new MessageBuilder($this->config))->subject('Not empty')->getHttpBody();
        $this->assertTrue(array_key_exists('subject', $result) && $result['subject'] === 'Not empty',
            'Subject in httpBody should have value of \'Not empty\'');
    }

    public function test_empty_subject_is_not_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->subject('')->getHttpBody();
        $this->assertArrayNotHasKey('subject', $result,
            'Subject should not exist in httpBody');
    }

    public function test_empty_subject_does_not_replace_existing_subject(): void
    {
        $messageBuilder = (new MessageBuilder($this->config))->subject('Existing subject');
        $result = $messageBuilder->subject('')->getHttpBody();
        $this->assertTrue(array_key_exists('subject', $result) && $result['subject'] === 'Existing subject',
            'Subject in httpBody should have value of \'Existing subject\'');
    }

    public function test_not_empty_body_is_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->body('Not empty')->getHttpBody();
        $this->assertTrue(array_key_exists('body', $result) && $result['body'] === 'Not empty',
            'Body in httpBody should have value of \'Not empty\'');
    }

    public function test_empty_body_is_not_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->body('')->getHttpBody();
        $this->assertArrayNotHasKey('body', $result,
            'Body should not exist in httpBody');
    }

    public function test_empty_body_does_not_replace_existing_subject(): void
    {
        $messageBuilder = (new MessageBuilder($this->config))->body('Existing body');
        $result = $messageBuilder->body('')->getHttpBody();
        $this->assertTrue(array_key_exists('body', $result) && $result['body'] === 'Existing body',
            'Body in httpBody should have value of \'Existing body\'');
    }

    public function test_not_empty_text_is_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->text('Not empty')->getHttpBody();
        $this->assertTrue(array_key_exists('text', $result) && $result['text'] === 'Not empty',
            'Text in httpBody should have value of \'Not empty\'');
    }

    public function test_empty_text_is_not_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->text('')->getHttpBody();
        $this->assertArrayNotHasKey('text', $result,
            'Text should not exist in httpBody');
    }

    public function test_empty_text_does_not_replace_existing_subject(): void
    {
        $messageBuilder = (new MessageBuilder($this->config))->text('Existing text');
        $result = $messageBuilder->text('')->getHttpBody();
        $this->assertTrue(array_key_exists('text', $result) && $result['text'] === 'Existing text',
            'Text in httpBody should have value of \'Existing body\'');
    }

    public function test_empty_tag_is_not_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->withTag('')->getHttpBody();
        $existingTags = $result['options']['tag_ids'] ?? [];
        $this->assertEmpty($existingTags,
            'No tags should exists when empty tag is added to fresh builder');
    }

    public function test_not_empty_tag_is_added_to_tag_ids_in_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->withTag('Not empty')->getHttpBody();
        $this->assertTrue(
            array_key_exists('options', $result)
            && array_key_exists('tag_ids', $result['options'])
            && in_array('Not empty', $result['options']['tag_ids'], true),
        'Tag \'Not empty\' should exist in httpBody options tag_ids');
    }

    public function test_empty_tags_array_is_not_added_to_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->withTags([])->getHttpBody();
        $existingTags = $result['options']['tag_ids'] ?? [];
        $this->assertEmpty($existingTags,
            'No tags should exists when empty tag array is added to fresh builder');
    }

    public function test_not_empty_tags_array_is_added_to_tag_ids_in_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->withTags(['Not empty'])->getHttpBody();
        $this->assertTrue(
            array_key_exists('options', $result)
            && array_key_exists('tag_ids', $result['options'])
            && in_array('Not empty', $result['options']['tag_ids'], true),
            'Tag \'Not empty\' should exist in httpBody options tag_ids');
    }

    public function test_duplicate_tags_are_added_uniquely_to_tag_ids_in_httpbody(): void
    {
        $result = (new MessageBuilder($this->config))->withTags(['Not empty', 'Not empty'])->getHttpBody();
        $this->assertTrue(
            array_key_exists('options', $result)
            && array_key_exists('tag_ids', $result['options'])
            && count($result['options']['tag_ids']) === 1
            && in_array('Not empty', $result['options']['tag_ids'], true),
            'Tag \'Not empty\' should only exist once in httpBody options tag_ids');
    }

    /**
     * @dataProvider provideRecipientData
     */
    public function test_recipients_with_empty_and_invalid_and_duplicate_mail_addresses(
        string $type,
        string|array $recipient,
        array $expectedResult,
        string $message
    ): void
    {
        $result = (new MessageBuilder($this->config))->{$type}($recipient)->getHttpBody();
        if (empty($expectedResult)) {
            $this->assertArrayNotHasKey($type, $result, $type . ' ' . $message);
        } else {
            $this->assertEquals($result[$type], $expectedResult, $type . ' ' . $message);
        };
    }

    public static function provideRecipientData():array
    {
        $data = [];
        $types = ['to','cc','bcc'];
        foreach ($types as $type) {
            $data[] = [$type, '', [], 'should not be added'];
            $data[] = [$type, 'valid@address.com', ['valid@address.com'], 'should be added'];
            $data[] = [$type, 'invalidaddress', [], 'should not be added'];
            $data[] = [$type, [], [], 'should be empty' ];
            $data[] = [$type, ['valid@address.com', 'valid@address.com'], ['valid@address.com'], 'should only be added once'];
            $data[] = [$type, ['valid@address.com', 'invalidaddress'], ['valid@address.com'], 'should only be added'];
            $data[] = [$type, ['invalidaddress', 'invalidaddress'], [], 'should both not be added'];
        }
        return $data;
    }

    /**
     * @dataProvider provideSenderData
     */
    public function test_sender_with_missing_valid_and_invalid_channel_and_author(
        string $sender,
        ?string $expectedChannelId,
        ?string $expectedAuthorId,
        string $message,
    ): void
    {
        $builder = (new MessageBuilder($this->config))->from($sender);
        $resultChannelId = $builder->getChannelId();
        $resultAuthorId = $builder->getHttpBody()['author_id'] ?? null;
        $this->assertTrue(
            $resultChannelId === $expectedChannelId
            && $resultAuthorId === $expectedAuthorId,
            $message
        );
    }

    public static function provideSenderData(): array
    {
        return [
            ['available_sender_with_author@tro.net', 'expected_channel_id', 'expected_author_id', 'ChannelId and AuthorId should be set'],
            ['available_sender_without_author@tro.net', 'expected_channel_id', null, 'ChannelId should be set, AuthorId not'],
            ['invalid_sender', null, null, 'ChannelId and AuthorId should both not be set']
        ];
    }

    public function test_archive_when_sent(): void
    {
        $result = (new MessageBuilder($this->config))->archiveWhenSent()->getHttpBody();
        $this->assertTrue($result['options']['archive']);
    }

    /**
     * @dataProvider provideMessagesToValidate
     */
    public function test_validate(
        ?string $channelId,
        ?array $to,
        bool $valid,
    ): void
    {
        $mail = new MessageBuilder($this->config);
        $mail->setChannelId($channelId);
        if (!is_null($to)) $mail = $mail->to($to);
        if (!$valid) {
            $this->expectException(InvalidMessageException::class);
        }
        $mail->validate();
        $this->assertInstanceOf(MessageBuilder::class, $mail, 'Should be valid');
    }

    public static function provideMessagesToValidate()
    {
        return [
            ['channelId',['to1@tro.net'], true],
            ['channelId',['to1@tro.net', 'to2@tro.net'], true],
            [null,['to1@tro.net'], false],
            ['channelId', null, false]
        ];
    }
}
