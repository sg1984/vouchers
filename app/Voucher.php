<?php  namespace App;


use Carbon\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'offer_id', 'recipient_id', 'expires_by',
        'used_at', 'created_at', 'updated_at',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'expires_by', 'used_at'
    ];

    protected $hidden = [
        'id', 'offer_id', 'recipient_id',
        'created_at', 'updated_at', 'used_at',
        'offer',
    ];

    protected $appends = [
        'offer_name', 'offer_discount', 'is_used'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function scopeNotUsed($query)
    {
        return $query->whereNull('used_at');
    }

    public function scopeByCode($query, $code)
    {
        if( empty($code) ){
            throw new \Exception('It is necessary a code to find a voucher by code!');
        }

        return $query->where('code', $code);
    }

    public function getOfferNameAttribute()
    {
        return $this->offer->name;
    }

    public function getOfferDiscountAttribute()
    {
        return $this->offer->discount;
    }

    public function getIsUsedAttribute()
    {
        return $this->isUsed();
    }

    protected static function generateCode()
    {
        return str_random();
    }

    public static function createVoucher(Offer $offer, Recipient $recipient, Carbon $expires_by)
    {
        $code = self::generateCode();

        if( self::byCode($code)->count() > 0 ){
            $code .= str_random(4);
        }

        $voucher = new Voucher([
            'code' => $code,
            'expires_by' => $expires_by,
        ]);

        $voucher->offer()->associate($offer);
        $voucher->recipient()->associate($recipient);
        $voucher->save();

        return $voucher;
    }

    public function isUsed()
    {
        return ! empty($this->used_at);
    }

    public function useVoucher()
    {
        $this->update([
            'used_at' => Carbon::now()
        ]);

        return $this;
    }
}