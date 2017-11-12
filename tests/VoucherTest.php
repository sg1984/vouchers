<?php

use \App\Offer;
use \App\Recipient;
use \Laravel\Lumen\Testing\DatabaseTransactions;

class VoucherTest extends TestCase
{
    use DatabaseTransactions;

    protected $voucherData;
    protected $recipients;
    protected $offer;

    public function setUp(){
        parent::setUp();
        $this->offer = factory(Offer::class)->create();
        $this->recipients = factory(Recipient::class, 3)->create();
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_voucher_no_expiration_date_provided(){
        $this->offer->generateVouchers();
    }

    function test_create_voucher(){
        $expirationDate = \Illuminate\Support\Carbon::today()->addDays(30)->endOfDay();
        $vouchers = $this->offer->generateVouchers($expirationDate);
        $firstVoucher = $vouchers->first();
        $this->assertInstanceOf(\App\Voucher::class, $firstVoucher);
    }
}