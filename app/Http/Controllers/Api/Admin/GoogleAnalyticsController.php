<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Illuminate\Http\Request;

class GoogleAnalyticsController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/analytics",
     *     tags={"Admin - analytics"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function analytics(Request $request, Website $website)
    {
        // validations
        $this->validation($request);

        // Using a default constructor instructs the client to use the credentials
        $client = new BetaAnalyticsDataClient();

        // Collecting the data
        $last24Hours = $this->getAnalyticsResponse($website, $client, null, "date", "totalUsers", true);
        $last7Days = $this->getAnalyticsResponse($website, $client, null, "date", "totalUsers");
        $platforms = $this->getAnalyticsResponse($website, $client, $request, "platform", "totalUsers");
        $dates = $this->getAnalyticsResponse($website, $client, $request, "date", "totalUsers");
        $deviceCategories = $this->getAnalyticsResponse($website, $client, $request, "deviceCategory", "totalUsers");
        $countries = $this->getAnalyticsResponse($website, $client, $request, "country", "totalUsers");

        return $this->dataResponse([
            'last24Hours' => $last24Hours,
            'last7Days' => $last7Days,
            'platform' => $platforms,
            'date' => $dates,
            'deviceCategory' => $deviceCategories,
            'country' => $countries,
        ]);
    }


    /**
     * A local private function used to validate the required fields for the analytics
     */
    private function validation(Request $request)
    {
        $request->validate([
            'startDate' => "required|date",
            'endDate' => "required|date",
        ]);
    }

    /**
     * A local private function used to getting the analytics from the google based on the requested parameters
     */
    private function getAnalyticsResponse($website, BetaAnalyticsDataClient $client, Request $request = null, $dimension , $metric, $past24hours = false)
    {
        $startDate = !is_null($request) ? $request->input('startDate') : now()->subDays(7)->toDateString();
        $endDate = !is_null($request) ? $request->input('endDate') : 'today';

        if ($past24hours)
        {
            $startDate = now()->subHours(24)->toDateString();
            $endDate = now()->toDateString();
        }

        // Make an API call.
        try {
            $response = $client->runReport([
                'property' => 'properties/' . $website->propertyId ,  // config('custom.google_analytics.property_id')
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]),
                ],
                'dimensions' => [new Dimension(['name' => $dimension])],
                'metrics' => [new Metric(['name' => $metric])]
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        // Check if the response has rows
        if (!empty($response->getRows())) {
            $data = [];
            foreach ($response->getRows() as $row) {
                $data[] = [
                    $dimension => $row->getDimensionValues()[0]->getValue(),
                    $metric => $row->getMetricValues()[0]->getValue(),
                ];
            }

            // sorting
            usort($data, function ($a, $b) use($dimension) {
                return (int) $a[$dimension] - (int) $b[$dimension];
            });

            // return the data
            return $data;
        }

        // return void
    }
}
