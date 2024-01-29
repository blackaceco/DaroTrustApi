<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\GroupType;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class GroupTypeController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/group-types",
     *     tags={"Admin - group_types"},
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
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $group_types = GroupType::with(['details', 'groups.details'])->get();

        return $this->dataResponse([
            'group_types' => $group_types
        ], null, false);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/group-types/{group}",
     *     tags={"Admin - group_types"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="group",
     *         in="path",
     *         description="id of an existing group.",
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
    public function show($website, $id)
    {
        $type = GroupType::with(['details', 'schemaTypes'])->findOrFail($id);

        return $this->dataResponse([
            'type' => $type
        ], null, false, true);
    }
}
