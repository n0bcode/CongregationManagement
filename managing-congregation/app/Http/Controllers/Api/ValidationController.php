<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    /**
     * Validate a single field
     */
    public function validateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $rules = $request->input('rules', []);

        if (empty($field) || empty($rules)) {
            return response()->json([
                'valid' => false,
                'message' => 'Field and rules are required'
            ], 400);
        }

        $validator = Validator::make(
            [$field => $value],
            [$field => $rules]
        );

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => $validator->errors()->first($field)
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Valid'
        ]);
    }
}
