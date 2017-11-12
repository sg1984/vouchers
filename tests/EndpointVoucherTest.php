<?php

use \Laravel\Lumen\Testing\DatabaseTransactions;

class EndpointVoucherTest extends TestCase
{
    use DatabaseTransactions;

    protected $offer;
    protected $expirationDate;
    protected $recipient;
    protected $vouchers;
    protected $firstVoucher;

    public function setUp()
    {
        parent::setUp();
        $this->offer = factory(\App\Offer::class)->create();
        $this->expirationDate = \Illuminate\Support\Carbon::today()->addDays(30)->endOfDay();
        $this->recipient = factory(\App\Recipient::class)->create();
        $this->vouchers = $this->offer->generateVouchers($this->expirationDate, $this->recipient);
        $this->firstVoucher = $this->vouchers->first();
    }

    public function test_validate_voucher()
    {
        $voucher = $this->firstVoucher;
        $recipient = $this->recipient;
        $dataPost = [
            'code' => $voucher->code,
            'email' => $recipient->email,
        ];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => true,
                'code' => $voucher->code,
                'offer_discount' => $voucher->offer_discount,
            ]);
    }

    public function test_fail_to_validate_voucher_method_not_allowed()
    {
        $dataPost = [];

        $this->json('PUT', 'validate-voucher', $dataPost)
            ->seeJson([
                'message' => 'This is not a valid endpoint!',
            ]);
    }

    public function test_fail_to_validate_voucher_no_data_provided()
    {
        $dataPost = [];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => false,
                'error' => 'To validate a voucher, the email is mandatory!',
            ]);
    }

    public function test_fail_to_validate_voucher_no_email_provided()
    {
        $voucher = $this->firstVoucher;
        $recipient = $this->recipient;
        $dataPost = [
            'code' => $voucher->code,
        ];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => false,
                'error' => 'To validate a voucher, the email is mandatory!',
            ]);
    }

    public function test_fail_to_validate_voucher_no_code_provided()
    {
        $voucher = $this->firstVoucher;
        $recipient = $this->recipient;
        $dataPost = [
            'email' => $recipient->email,
        ];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => false,
                'error' => 'To validate a voucher, the code is mandatory!',
            ]);
    }

    public function test_fail_to_validate_voucher_invalid_email_provided()
    {
        $voucher = $this->firstVoucher;
        $recipient = $this->recipient;
        $dataPost = [
            'email' => $recipient->email . $recipient->email,
            'code' => $voucher->code,
        ];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => false,
                'error' => 'There is no recipient with this email.',
            ]);
    }

    public function test_fail_to_validate_voucher_invalid_code_provided()
    {
        $voucher = $this->firstVoucher;
        $recipient = $this->recipient;
        $dataPost = [
            'email' => $recipient->email,
            'code' => $voucher->code . $recipient->email,
        ];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => false,
                'error' => 'There is no voucher with this code.',
            ]);
    }

    public function test_fail_to_validate_voucher_code_used_provided()
    {
        $voucher = $this->firstVoucher;
        $voucher->useVoucher();
        $recipient = $this->recipient;
        $dataPost = [
            'email' => $recipient->email,
            'code' => $voucher->code,
        ];

        $this->json('POST', 'validate-voucher', $dataPost)
            ->seeJson([
                'success' => false,
                'error' => 'This voucher was used.',
            ]);
    }
}