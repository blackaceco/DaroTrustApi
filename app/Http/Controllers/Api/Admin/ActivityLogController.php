<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/activity-logs",
     *     tags={"Admin - activity-logs"},
     *     summary="auth",
     * 
     *      @OA\Parameter(
     *         name="adminId",
     *         in="query",
     *         description="id of an existing admin.",
     *         required=false,
     *     ),
     * 
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="number of pagination.",
     *         required=false,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items (
     *                      type="object",
     *                      @OA\Property (property="id", type="integer", example="1"),
     *                      @OA\Property (property="admin", type="object",
     *                          @OA\Property (property="id", type="integer", example="1"),
     *                          @OA\Property (property="name", type="string", example="Example Admin Name"),
     *                      ),
     *                      @OA\Property (property="website", type="object",
     *                          @OA\Property (property="id", type="integer", example="1"),
     *                          @OA\Property (property="name", type="string", example="Example Website Title"),
     *                      ),
     *                      @OA\Property (property="ipAddress", type="string", example="127.0.0.1"),
     *                      @OA\Property (property="entityId", type="integer", example="1"),
     *                      @OA\Property (property="entity", type="string", example="website_languages"),
     *                      @OA\Property (property="action", type="string", example="create"),
     *                      @OA\Property (property="oldValue", type="object"),
     *                      @OA\Property (property="newValue", type="object"),
     *                      @OA\Property (property="createdAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property (property="current_page", type="integer", example=1),
     *                  @OA\Property (property="last_page", type="integer", example=1),
     *                  @OA\Property (property="has_next_page", type="boolean", example=false),
     *                  @OA\Property (property="has_previous_page", type="boolean", example=false),
     *                  @OA\Property (property="next_page_url", type="string", example=null),
     *                  @OA\Property (property="prev_page_url", type="string", example=null),
     *                  @OA\Property (property="on_first_page", type="boolean", example=true),
     *                  @OA\Property (property="per_page", type="integer", example=10),
     *                  @OA\Property (property="total", type="integer", example=20),
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    {
        $logs = ActivityLog::orderBy('id', 'DESC')->with(['admin', 'website']);
        
        if (request()->filled('adminId'))
            $logs->where('adminId', request()->input('adminId'));

        // if (request()->filled('limit'))
        //     $logs->take(request()->input('limit'));

        $logs = $logs->paginate(request()->input('limit', 10));

        return $this->dataResponse($logs, ActivityLogResource::class, true);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/activity-logs/{activity_log}",
     *     tags={"Admin - activity-logs"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="activity_log",
     *         in="path",
     *         description="id of an existing log.",
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
    public function show(ActivityLog $activity_log)
    {
        return $this->dataResponse([
            'log' => new ActivityLogResource($activity_log)
        ], null, false, true);
    }
}
