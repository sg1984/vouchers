<?php

use \App\Offer;
use \Laravel\Lumen\Testing\DatabaseTransactions;

class OfferTest extends TestCase
{
    use DatabaseTransactions;

    protected $offerData;

    public function setUp(){
        parent::setUp();
        $this->offerData = factory(Offer::class)->raw();
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_offer_no_data_provided(){
        Offer::validateAndNew([]);
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_offer_only_name_provided(){
        Offer::validateAndNew([
            'name' => 'Lorem Ipsum'
        ]);
    }

    /**
     * @expectedException \App\Exceptions\ValidationException
     */
    function test_fail_to_create_offer_only_discount_provided(){
        Offer::validateAndNew([
            'discount' => 5.95
        ]);
    }

    function test_create_offer(){
        $offer = Offer::validateAndNew($this->offerData);
        $offer->save();
        $this->assertInstanceOf(Offer::class, $offer);
    }
}