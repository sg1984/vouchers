<?php

use \App\Recipient;
use \Laravel\Lumen\Testing\DatabaseTransactions;

class RecipientTest extends TestCase
{
    use DatabaseTransactions;

    protected $recipientData;

    public function setUp(){
        parent::setUp();
        $this->recipientData = factory(Recipient::class)->raw();
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_recipient_no_data_provided(){
        Recipient::validateAndNew([]);
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_recipient_only_name_provided(){
        Recipient::validateAndNew([
            'name' => 'John Doe'
        ]);
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_recipient_only_email_provided(){
        Recipient::validateAndNew([
            'email' => ''
        ]);
    }

    function test_create_recipient(){
        $recipient = Recipient::validateAndNew($this->recipientData);
        $recipient->save();
        $this->assertInstanceOf(Recipient::class, $recipient);
    }
}