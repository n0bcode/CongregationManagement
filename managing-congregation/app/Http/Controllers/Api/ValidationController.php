<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    /**
     * Validate a specific field.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateField(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'nullable',
            'rules' => 'required|string',
            'id' => 'nullable|integer', // For unique checks ignoring current record
        ]);

        $field = $request->input('field');
        $value = $request->input('value');
        $rules = $request->input('rules');
        $id = $request->input('id');

        // Parse rules to handle unique checks with ID
        $parsedRules = $this->parseRules($rules, $id);

        $validator = Validator::make(
            [$field => $value],
            [$field => $parsedRules]
        );

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => $validator->errors()->first($field),
            ]);
        }

        return response()->json([
            'valid' => true,
        ]);
    }

    /**
     * Parse rules to inject ID for unique checks.
     *
     * @param string $rules
     * @param int|null $id
     * @return array|string
     */
    protected function parseRules($rules, $id)
    {
        if (!$id) {
            return $rules;
        }

        // Example: "required|email|unique:users,email" -> "required|email|unique:users,email,1"
        $ruleArray = explode('|', $rules);
        
        foreach ($ruleArray as &$rule) {
            if (str_starts_with($rule, 'unique:')) {
                $parts = explode(',', $rule);
                if (count($parts) < 3) {
                    $rule .= ',' . $id;
                }
            }
        }

        return implode('|', $ruleArray);
    }
}
