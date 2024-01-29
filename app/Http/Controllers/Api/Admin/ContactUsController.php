<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUsForm;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/contact-us-form",
     *     tags={"Admin - contact-us-forms"},
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
    public function index(Website $website)
    {
        $contacts = ContactUsForm::where('websiteId', $website->id)->latest()->get();

        return $this->dataResponse([
            'contacts' => $contacts
        ], null, false);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/contact-us-form/{contact}",
     *     tags={"Admin - contact-us-forms"},
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
     *         name="contact",
     *         in="path",
     *         description="id of an existing contact-us-forms.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=5),
     *              @OA\Property(property="subject", type="string", example="Example subject titile"),
     *              @OA\Property(property="name", type="string", example="Example User Name"),
     *              @OA\Property(property="email", type="string", example="example@email.com"),
     *              @OA\Property(property="phone", type="string", example="07701234567"),
     *              @OA\Property(property="message", type="string", example="Lorem ipsum dolor with blah blah blah blah blah blah."),
     *              @OA\Property(property="ipAddress", type="string", example="127.0.0.1"),
     *              @OA\Property(property="status", type="string", example="pending"),
     *              @OA\Property(property="createdAt", type="string", example="2023-10-08T10:13:10.000000Z"),
     *              @OA\Property(property="updatedAt", type="string", example="2023-10-08T10:13:10.000000Z"),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($website, $contact)
    {
        $contact = ContactUsForm::where('websiteId', $website)->findOrFail($contact);

        return $this->dataResponse([
            'contact' => $contact
        ], null, false, true);
    }
}
