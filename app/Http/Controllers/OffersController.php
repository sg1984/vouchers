<?php

namespace App\Http\Controllers;


use App\Exceptions\NoContentFoundException;
use App\Offer;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OffersController extends Controller
{
    public function getAll()
    {
        try {
            $offers = Offer::getOffers();
            $dataResponse = [
                'count_offers' => count($offers),
                'offers'    => $offers
            ];

            return $this->successResponse($dataResponse);
        }
        catch (\Exception $e) {
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $newOfferData = array_only($request->toArray(), ['name', 'discount']);
            $offer = Offer::validateAndNew($newOfferData);
            $offer->save();
            DB::commit();

            $dataResponse = [
                'offer' => $offer,
            ];

            return $this->successResponse($dataResponse, 201);
        }
        catch (ValidationException $e) {
            DB::rollback();
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse);
        }
        catch (\Exception $e) {
            DB::rollback();
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse, 500);
        }
    }

    public function getOffer($offerId = null)
    {
        try {
            $offer = Offer::find($offerId);
            if ( empty($offer) ) {
                throw new NoContentFoundException('Offer not found in database!');
            }

            $dataResponse = [
                'offer' => $offer,
            ];

            return $this->successResponse($dataResponse);
        }
        catch (NoContentFoundException $e) {
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse);
        }
        catch (\Exception $e) {
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse, 500);
        }
    }

    public function newVouchers($offerId = null, Request $request)
    {
        try {
            DB::beginTransaction();
            $offer = Offer::find($offerId);
            if ( empty($offer) ) {
                throw new NoContentFoundException('Offer not found in database!');
            }

            $expiration_date_request = array_get($request->toArray(), 'expiration_date', null);
            if( empty($expiration_date_request) ){
                throw new NoContentFoundException('The expiration date is mandatory to create vouchers!');
            }

            try {
                $expiration_date = Carbon::createFromFormat('Y-m-d', $expiration_date_request);
            }
            catch (\Exception $e) {
                throw new NoContentFoundException('The expiration date must be at format YYYY-mm-dd!');
            }

            $vouchers = $offer->generateVouchers($expiration_date->endOfDay());
            DB::commit();

            $dataResponse = [
                'vouchers_created' => count($vouchers),
                '$vouchers' => $vouchers
            ];

            return $this->successResponse($dataResponse);
        }
        catch (NoContentFoundException $e) {
            DB::rollback();
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse);
        }
        catch (\Exception $e) {
            DB::rollback();
            $dataResponse = [
                'error' => $e->getMessage()
            ];

            return $this->errorResponse($dataResponse, 500);
        }
    }
}