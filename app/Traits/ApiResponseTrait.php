<?php

/**
 * VERSION :: 3
 *
 * This trait generate some functions which used by the controllers for returning responses.
 */

namespace App\Traits;

trait ApiResponseTrait
{

    /**
     * this will be call when some actions will be performed successfully.
     *
     * @param String $message
     * @param Array $data = []
     * @param Integer $statusCode = 200
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($message, $data = [], $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (count($data) > 0)
            $response = array_merge($response, $data);

        return response()->json($response, $statusCode);
    }


    /**
     * this will be call when something will be exploded !
     *
     * @param String $message
     * @param Array $data = []
     * @param Integer $statusCode = 200
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $data = [], $statusCode = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];


        if (!empty($data))
            foreach ($data as $key => $error)
                $response['messages'][$key] = $error;


        return response()->json($response, $statusCode);
    }


    /**
     * this will be call to returning some data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataResponse($data, $resource = null, $is_pagination = false, $one_object = false)
    {
        if ($is_pagination) {
            $response = [
                // items data
                'data' => is_null($resource) ? $data->items() : $resource::collection($data->items()),

                // pagination attributes
                'meta' => [
                    // 'current_page' => $data->currentPage(),
                    // 'last_page' => $data->lastPage(),

                    // 'has_next_page' => $data->currentPage() < $data->lastPage(),
                    // 'has_previous_page' => $data->currentPage() > 1,

                    // 'next_page_url' => $data->nextPageUrl(),
                    // 'prev_page_url' => $data->previousPageUrl(),

                    // 'on_first_page' => $data->onFirstPage(),
                    // 'per_page' => $data->perPage(),
                    // 'total' => $data->total(),

                    // NEW
                    'total' => $data->total(),
                    'limit' => $data->perPage(),
                    'page' => $data->currentPage(),
                    'previous' => $data->currentPage() != 1 ? $data->currentPage() - 1 : null,
                    'next' => $data->currentPage() != $data->lastPage() ? $data->currentPage() + 1 : null,
                    'last' => $data->lastPage(),
                ],
            ];
        } else {
            if (is_null($resource))
                $response = $data;
            else
                if ($one_object)
                $response = new $resource($data);
            else
                $response = $resource::collection($data);
        }


        return response()->json($response, 200);
    }





    // public function senddataResponse($pagination, $resource)
    // {
    // 	$response = [
    //         'current_page' => $pagination->currentPage(),
    //         'last_page' => $pagination->lastPage(),

    //         'data' => $resource::collection( $pagination->items() ),

    //         'has_next_page' => $pagination->currentPage() < $pagination->lastPage(),
    //         'has_previous_page' => $pagination->currentPage() > 1,

    //         'next_page_url' => $pagination->nextPageUrl(),
    //         'prev_page_url' => $pagination->previousPageUrl(),

    //         'on_first_page' => $pagination->onFirstPage(),
    //         'per_page' => $pagination->perPage(),
    //         'total' => $pagination->total(),
    //     ];

    //     return response()->json($response, 200);
    // }
}
