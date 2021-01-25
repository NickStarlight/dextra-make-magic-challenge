<?php

namespace App\Rules;

use App\SDK\PotterAPI\PotterAPI;
use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * The house validation rule.
 *
 * This will attempt to find the provided house in the array
 * returned by Potter API.
 * If any server error occurs, that's probably our fault or Potter API's
 * so we return a `503 - Service unavailable error` so the browser/client
 * can retry as soon as possible. 
 * 
 * @see https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md
 * @author Nickolas Gomes Moraes
 * @version 1.0
 * @license WTFPL
 */
class HouseExists implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute The attribute name
     * @param  mixed  $value The house UUID
     * 
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        /** 
         * The exceptions will only be throw if all retries
         * and timeouts defined by de SDK fail, if that happens, it's not
         * the user fault so we return 503(service unavailable).
         */
        try {
            $SDK = new PotterAPI(fromCache: true);
            $response = $SDK->getHouses();

            /** HTTP 200 */
            if ($response->status === 200) {
                /** Array search returns false if nothing is found. */
                return array_search($value, array_column($response->response['houses'], 'id')) !== false;
            }

            /** 
             * If the response is NOT ok, it probably means
             * that the server is having problems, again,
             * not the user fault.
             */
            if ($response->status >= 500) {
                abort(503);
            }
        } catch (Exception $e) {
            report($e);
            abort(503);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The house does not exist.');
    }
}
