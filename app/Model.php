<?php namespace App;

use App\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public static function validateAndNewInstance(Model $model, array $data = [])
    {
        self::validateDataForNewInstance($model, $data);

        return $model->newInstance($data);
    }

    public static function validateDataForNewInstance(Model $model, array $data = [])
    {
        if( empty($data) ){
            throw new ValidationException('No data found to create a instance');
        }

        foreach ($model->mandatoryFields as $field){
            if( ! array_has($data, $field) ){
                throw new ValidationException('The field "' . $field . '" is mandatory to create an instance');
            }
        }
    }
}