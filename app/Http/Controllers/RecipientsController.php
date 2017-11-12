<?php

namespace App\Http\Controllers;


use App\Exceptions\NoContentFoundException;
use App\Recipient;
use Dotenv\Exception\ValidationException;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipientsController extends Controller
{
    public function getAll()
    {
        try {
            $recipients = Recipient::getRecipients();
            $dataResponse = [
                'count_recipients' => count($recipients),
                'offers'    => $recipients
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
            $newRecipientData = array_only($request->toArray(), ['name', 'email']);
            $recipient = Recipient::validateAndNew($newRecipientData);
            $recipient->save();
            DB::commit();
            $dataResponse = [
                'recipient' => $recipient,
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

    public function validateVoucher(Request $request)
    {
        try {
            DB::beginTransaction();

            $email = $request->get('email');
            if( empty($email) ){
                throw new NoContentFoundException('To validate a voucher, the email is mandatory!');
            }

            $code = $request->get('code');
            if( empty($code) ){
                throw new NoContentFoundException('To validate a voucher, the code is mandatory!');
            }

            $recipient = Recipient::byEmail($email)->first();
            if( empty($recipient) ){
                throw new NoContentFoundException('There is no recipient with this email.');
            }

            $voucher = $recipient->vouchers()
                ->byCode($code)
                ->with('offer')
                ->first();
            if( empty($voucher) ){
                throw new NoContentFoundException('There is no voucher with this code.');
            }
            if( $voucher->isUsed() ){
                throw new NoContentFoundException('This voucher was used.');
            }

            $voucher->useVoucher();
            DB::commit();

            $dataResponse = [
                'used_voucher' => $voucher,
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
            DB::rollback();

            return $e->getMessage();
        }
    }

    public function validVouchers(Request $request)
    {
        try {
            $email = $request->get('email');
            if( empty($email) ){
                throw new NoContentFoundException('To verify the vouchers of a recipient, the email is mandatory!');
            }

            $recipient = Recipient::byEmail($email)->first();
            if( empty($recipient) ){
                throw new NoContentFoundException('There is no recipient with this email.');
            }

            $vouchers = $recipient
                ->vouchersNotUsed()
                ->with('offer')
                ->get();

            if( count($vouchers) < 1 ){
                throw new NoContentFoundException('There is no valid vouchers to this recipient.');
            }
            $dataResponse = [
                'count_vouchers' => count($vouchers),
                'vouchers' => $vouchers,
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

    public function allVouchers(Request $request)
    {
        try {
            $email = $request->get('email');
            if( empty($email) ){
                throw new NoContentFoundException('To verify the vouchers of a recipient, the email is mandatory!');
            }

            $recipient = Recipient::byEmail($email)->first();
            if( empty($recipient) ){
                throw new NoContentFoundException('There is no recipient with this email.');
            }

            $vouchers = $recipient
                ->vouchers()
                ->with('offer')
                ->get();

            if( count($vouchers) < 1 ){
                throw new NoContentFoundException('There is no valid vouchers to this recipient.');
            }
            $dataResponse = [
                'count_vouchers' => count($vouchers),
                'vouchers' => $vouchers,
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
}
