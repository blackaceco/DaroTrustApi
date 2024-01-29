<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\TagResource;
use App\Models\Group;
use App\Models\Tag;
use App\Traits\ApiResponseTrait;

class GeneralController extends Controller
{
    use ApiResponseTrait;


    /**
     * @OA\Get(
     *     path="/api/{website_slug}/tags",
     *     tags={"Front - general"},
     *     summary="guest",
     * 
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="id of existing website.",
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(type="object")
     *          )
     *      )
     * )
     */
    public function tags()
    {
        $tags = Tag::all();

        return $this->dataResponse($tags, TagResource::class);
    }


    /**
     * @OA\Get(
     *     path="/api/{website_slug}/groups",
     *     tags={"Front - general"},
     *     summary="guest",
     * 
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="id of existing website.",
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(type="object")
     *          )
     *      )
     * )
     */
    public function groups()
    {
        $groups = Group::all();

        return $this->dataResponse($groups, GroupResource::class);
    }
}
