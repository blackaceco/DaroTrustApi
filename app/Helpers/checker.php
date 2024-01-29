<?php

/**
 * VERSION :: 3
 * 
 * Checking the required parameters for a request in the controllers and validate
 * them with the received rules then if there are any errors available throw
 * a respone for returning the errors, if there are no any errors available then
 * return the fields based on the rule's keys.
 * 
 * @param Illuminate\Http\Request @request
 * @param Array $rules
 * @param App\Http\Controllers\Controller $controller
 * 
 * @return Array || void
 */
function checkApiValidationRules($request, $rules, $controller)
{
    // Validation rules
    $validator = \Validator::make($request->all(), $rules);

    // Return the errors if the validation rules will be failed.
    if ($validator->fails())
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            $controller->errorResponse('Validation Error.', $validator->errors()->getMessages(), 422)
        );

    // if passed, return fields
    return $request->only(array_keys($rules));
}
