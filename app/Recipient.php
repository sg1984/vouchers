<?php namespace App;


use Dotenv\Exception\ValidationException;
use PharIo\Manifest\InvalidEmailException;

class Recipient extends Model
{
    protected $fillable = [
        'email', 'name', 'created_at', 'updated_at',
    ];

    protected $dates = [
        'created_at', 'updated_at',
    ];

    protected $mandatoryFields = [
        'name', 'email'
    ];

    protected $hidden = [
        'id', 'created_at', 'updated_at',
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function vouchersNotUsed()
    {
        return $this->vouchers()->notUsed();
    }

    public function scopeByEmail($query, $email)
    {
        if( empty($email) || ! is_string($email)){

            return $query;
        }
        
        return $query->where('email', $email);
    }

    public static function getRecipients()
    {
        return self::all();
    }

    public static function emailIsRegistered($email)
    {
        return self::byEmail($email)->count() > 0;
    }

    public static function validateAndNew(array $data = [])
    {
        $recipient = self::validateAndNewInstance(new self(), $data);

        $email = $recipient->email;
        if( ! empty($email) ){
            if( self::emailIsRegistered($email) ){
                throw new InvalidEmailException('Email is already in database!');
            }
        }

        return $recipient;
    }
}