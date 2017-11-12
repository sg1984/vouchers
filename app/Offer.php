<?php namespace App;


use App\Exceptions\ValidationException;
use Carbon\Carbon;

class Offer extends Model
{
    protected $fillable = [
        'name', 'discount', 'created_at', 'updated_at',
    ];

    protected $dates = [
        'created_at', 'updated_at',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    protected $mandatoryFields = [
        'name', 'discount',
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public static function getOffers()
    {
        return self::all();
    }

    public static function validateAndNew(array $data = [])
    {
        $newOffer = self::validateAndNewInstance(new self(), $data);

        if( ! $newOffer->isValidDiscount() ){
            throw new ValidationException('The discount must be a value between 0 and 100.');
        }

        return self::validateAndNewInstance(new self(), $data);
    }

    public function isValidDiscount()
    {
        if( empty($this->discount) && $this->discount != 0 ){

            return false;
        }

        if( $this->discount > 100 || $this->discount < 0 ){

            return false;
        }

        return true;
    }

    public function generateVouchers(Carbon $expiration_date = null, Recipient $recipient = null)
    {
        if( empty($expiration_date) ){
            throw new ValidationException('A voucher needs an expiration date to be created!');
        }

        if( empty($recipient->id) ){
            $recipients = Recipient::getRecipients();
        }
        else{
            $recipients = collect([$recipient]);
        }

        $vouchers = collect([]);

        foreach ($recipients as $recipient){
            $voucher = Voucher::createVoucher($this, $recipient, $expiration_date);
            $vouchers->push($voucher);
        }

        return $vouchers;
    }
}