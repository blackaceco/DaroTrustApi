<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Rules\s3existFile;
use App\Traits\ApiResponseTrait;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/attachments/{count?}",
     *     tags={"Admin - attachments"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="path",
     *         in="query",
     *         description="path of an existing object.",
     *     ),
     *     @OA\Parameter(
     *         name="count",
     *         in="path",
     *         description="count the objects on the specific path.",
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
    public function attachments(Request $request, $count = null)
    {
        // check filesystem and getting the objects
        if (config('filesystems.default') == 'local')
            $objects = $this->localStorageAttachments($request, $count ? true : false);
        elseif (config('filesystems.default') == 's3')
            $objects = $this->s3StorageAttachments($request, $count ? true : false);


        // return the response
        return $this->dataResponse([
            'attachments' => $objects
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/signed-url",
     *     tags={"Admin - attachments"},
     *     summary="auth",
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
    public function getSignedUrl(Request $request)
    {
        // validate
        checkApiValidationRules($request, ['fileName' => "required|string|max:255"], $this);

        return $this->dataResponse([
            'url' => generateSignedUrlFromS3($request->input('fileName')),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/attachments/upload-attachments",
     *     tags={"Admin - attachments"},
     *     summary="auth",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"file"},
     *                 @OA\Property(property="file", type="file", example="file type"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function uploadAttachments(Request $request)
    {
        // validation
        checkApiValidationRules($request, ['file' => "required|file|max:15360"], $this);

        // save the file
        if (config('filesystems.default' == 'local'))
            $file_path = $request->file('file')->store($request->input('path', ''), 'public');
        else
            $file_path = $request->file('file')->store($request->input('path', ''));

        // return success response including the file's name
        return $this->successResponse("Successfully saved.", [
            'temp' => $file_path,
            'path' => getFileLink($file_path),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/attachments",
     *     tags={"Admin - attachments"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="path",
     *         in="query",
     *         description="path of an existing object.",
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
    public function destroy(Request $request)
    {
        $path = "";

        // generate the path
        if (config('filesystems.default') == 'local') {
            $path = "public";
            if ($request->has('path'))
                $path .= "/$request->path";

            // delete the path
            if (File::isDirectory(storage_path("app/$path")))
                $is_deleted = Storage::deleteDirectory($path);
            else
                $is_deleted = Storage::delete($path);
        } elseif (config('filesystems.default') == 's3') {
            $path = $request->input('path', '');

            // deleting
            // check is directory
            if (in_array($path, Storage::directories(dirname($path))))
                $is_deleted = Storage::deleteDirectory($path);
            else
                $is_deleted = Storage::delete($path);
        }



        if ($is_deleted)
            // return success response
            return $this->successResponse("Successfully deleted.");
        else
            // return failed response
            return $this->errorResponse("Unable to delete the file !");
    }





    /****************************************************
     *              Local Private Functions             *
     *     By: Kamyar R. Muhammad on 18 October 2023    *
     ***************************************************/

    private function localStorageAttachments($request, $count)
    {
        // generate the path
        $path = "public";
        if ($request->has('path'))
            $path .= "/$request->path";

        // check is the path exist
        if (!Storage::exists($path))
            abort(404);

        // load the directories
        $directories = Storage::directories($path);
        // loda the files
        $files = Storage::files($path);
        // merge the loaded directories and loaded files
        $objects = array_merge($directories, $files);

        foreach ($objects as $key => $object) {
            // remove .gitignore files
            if (strpos($object, '.gitignore')) {
                unset($objects[$key]);
                continue;
            }

            $file_path = str_replace('public/', '', $object);

            // generate the file detail object
            $objects[$key] = [
                'name' => array_reverse(explode('/', $object))[0],
                'path' => $file_path,
                'fullpath' => asset("storage/$file_path"),
                'type' => File::isDirectory(storage_path("app/$object")) ? 'directory' : pathinfo($object, PATHINFO_EXTENSION),
            ];
        }

        // Re-index the array
        $objects = array_values($objects);

        // if requested count only
        if ($count)
            return [
                'directories' => count($directories),
                'files' => count($files),
                'total' => count($objects),
            ];

        // return data
        return $objects;
    }

    private function s3StorageAttachments($request, $count)
    {
        $path = $request->input('path', "/");

        $objects = Storage::listContents($path);
        $objectsList = [];

        // generate the file detail object
        foreach ($objects as $key => $object) {
            $objectsList[] = [
                'name' => array_reverse(explode('/', $object['path']))[0],
                'path' => $object['path'],
                'fullpath' => Storage::url($object['path']),
                'type' => $object['type'] == 'dir' ? 'directory' : pathinfo($object['path'], PATHINFO_EXTENSION),
            ];
        }

        // if requested count only
        if ($count) {
            $directories = 0;
            $files = 0;
            $total = 0;

            // counting
            foreach ($objectsList as $key => $object) {
                if ($object['type'] == 'directory')
                    $directories++;
                else
                    $files++;

                $total++;
            }

            // return
            return [
                'directories' => $directories,
                'files' => $files,
                'total' => $total,
            ];
        }

        // return objects
        return $objectsList;
    }
}
