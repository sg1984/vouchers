<?php

use \Laravel\Lumen\Testing\DatabaseTransactions;

class EndpointOffersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_all_offers()
    {
        $this->json('GET', 'offers')
            ->seeJson([
                'success' => true,
            ]);
    }

    public function test_create_an_offer()
    {
        $newOfferData = factory(\App\Offer::class)->raw();
        $offer = \App\Offer::validateAndNew($newOfferData);

        $this->json('PUT', 'offers', $newOfferData)
            ->seeJson([
                'name' => $offer->name,
                'discount' => $offer->discount,
            ]);
    }

    public function test_fail_to_create_an_offer_method_not_allowed()
    {
        $newOfferData = [];

        $this->json('POST', 'offers', $newOfferData)
            ->seeJson([
                'message' => 'This is not a valid endpoint!',
            ]);
    }

    public function test_fail_to_create_an_offer_no_data_provided()
    {
        $newOfferData = [];

        $this->json('PUT', 'offers', $newOfferData)
            ->seeJson([
                'success' => false,
                'error' => 'No data found to create a instance',
            ]);
    }

    public function test_create_vouchers()
    {
        $offer = factory(\App\Offer::class)->create();
        $expiration_date = \Illuminate\Support\Carbon::today()->addDays(30)->endOfDay()->format('Y-m-d');

        $this->json('POST', 'offers/' . $offer->id, compact('expiration_date'))
            ->seeJson([
                'success' => true,
            ]);
    }

    public function test_fail_to_create_vouchers_method_not_allowed()
    {
        $offer = factory(\App\Offer::class)->create();
        $expiration_date = \Illuminate\Support\Carbon::today()->addDays(30)->endOfDay()->format('Y-m-d');

        $this->json('PUT', 'offers/' . $offer->id, compact('expiration_date'))
            ->seeStatusCode(405);
    }

    public function test_fail_to_create_vouchers_no_offer_provided()
    {
        $this->json('POST', 'offers', compact('expiration_date'))
            ->seeJson([
                'success' => false,
                'message' => 'This is not a valid endpoint!'
            ]);
    }

    public function test_fail_to_create_vouchers_invalid_offer_provided()
    {
        $this->json('POST', 'offers/invalid-value', compact('expiration_date'))
            ->seeJson([
                'success' => false,
                'error' => 'Offer not found in database!'
            ]);
    }

    public function test_fail_to_create_vouchers_no_expiration_date_provided()
    {
        $offer = factory(\App\Offer::class)->create();

        $this->json('POST', 'offers/' . $offer->id, compact('expiration_date'))
            ->seeJson([
                'success' => false,
                'error' => 'The expiration date is mandatory to create vouchers!'
            ]);
    }
}