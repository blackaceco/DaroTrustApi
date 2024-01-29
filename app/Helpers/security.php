<?php

function preventXSS($fields, $except = [])
{
    // Loop through each input value and sanitize it
    array_walk_recursive($fields, function (&$value, $key) use($except) {
        // Skip XSS cleaning for the excluded fields
        if (in_array($key, $except))
            return;

        if (!is_null($value)) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            // keeping the & symbol
            $value = str_replace(" &amp; ", " & ", $value);
            $value = str_replace(" &amp;&amp; ", " && ", $value);
        }
    });

    // removing the null or empty elements on the array before returning
    $fields = array_filter($fields, function ($value) {
        return !is_null($value) && $value != '';
    });

    return $fields;
}

/**
 * this will called on exception catches and returing a response based
 * on the given object.
 *
 * If the einvironment is local return a full detail else return empty errors.
 */
function exceptionCatchHandler($object, $e)
{
    if (config('app.env') == 'local')
        return $object->errorResponse("This will show on development mode only ::: " . $e->getMessage() , $e, 400);
    else
        return $object->errorResponse("Something went wrong !", [], 400);
}
