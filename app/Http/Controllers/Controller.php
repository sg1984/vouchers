<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function successResponse($dataResponse, $code = 200)
    {
        $dataResponse = array_merge($dataResponse, [
            'success' => true,
        ]);

        return response()->json($dataResponse, $code);
    }

    public function errorResponse($dataResponse, $code = 400)
    {
        $dataResponse = array_merge($dataResponse, [
            'success' => false,
        ]);

        return response()->json($dataResponse, $code);
    }

    public function notValidEndpoint()
    {
        $dataResponse = [
            'message' => 'This is not a valid endpoint!',
            'success' => false,
        ];

        return response()->json($dataResponse);
    }
}
