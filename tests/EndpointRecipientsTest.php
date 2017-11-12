<?php

use \Laravel\Lumen\Testing\DatabaseTransactions;

class EndpointRecipientsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_all_recipients()
    {
        $this->json('GET', 'recipients')
            ->seeJson([
                'success' => true,
            ]);
    }

    public function test_create_a_recipient()
    {
        $newRecipientData = factory(\App\Recipient::class)->raw();
        $recipient = \App\Recipient::validateAndNew($newRecipientData);

        $this->json('PUT', 'recipients', $newRecipientData)
            ->seeJson([
                'email' => $recipient->email
            ]);
    }

    public function test_fail_to_create_an_recipient_method_not_allowed()
    {
        $newRecipientData = [];

        $this->json('POST', 'offers', $newRecipientData)
            ->seeJson([
                'message' => 'This is not a valid endpoint!',
            ]);
    }

    public function test_failt_to_create_a_recipient_no_data_provided()
    {
        $newRecipientData = [];

        $this->json('PUT', 'recipients', $newRecipientData)
            ->seeJson([
                'success' => false,
                'error' => 'No data found to create a instance',
            ]);
    }
}