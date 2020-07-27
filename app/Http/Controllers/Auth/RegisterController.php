<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\StrictEmail;
use App\Rules\Username;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Valorin\Pwned\Pwned;

class RegisterController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="RegistrationRequest",
     *     type="object",
     *     required={
     *         "name",
     *         "email",
     *         "password",
     *     },
     *     additionalProperties=false,
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         minLength=5,
     *         maxLength=20,
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         minLength=3,
     *         maxLength=128,
     *     ),
     *     @OA\Property(
     *         property="password",
     *         type="string",
     *         minLength=8,
     *         maxLength=300,
     *     )
     * )
     * @OA\Post(
     *     path="/users",
     *     description="Register an account on the site",
     *     tags={"authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegistrationRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             additionalProperties=false,
     *             @OA\Property(
     *                 property="token",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="Registration successful (authentication via cookies, no token is sent)"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Validation error",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ValidationErrorResponse"
     *         )
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Already logged in via session-based authentication",
     *     ),
     *     @OA\Response(
     *         response="503",
     *         description="Registrations are not possible at the moment",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ErrorResponse"
     *         )
     *     )
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function viaPassword(Request $request)
    {
        $is_sanctum = $request->attributes->get('sanctum') === true;
        if ($is_sanctum && $request->user() !== null) {
            abort(403);
        }

        $have_users = User::any();

        // TODO remove when registration for the public is open
        if ($have_users) {
            abort(503, 'New registrations are currently not accepted, thank you for your understanding.');
        }

        $validator = Validator::make($request->only(['email', 'name', 'password']), [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:20',
                'unique:users',
                new Username(),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'min:3',
                'unique:users',
                new StrictEmail(),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:300',
                new Pwned,
            ],
        ]);

        $data = $validator->validate();
        // First user will receive developer privileges
        if (!$have_users) {
            $data['role'] = 'developer';
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($is_sanctum) {
            Auth::login($user, true);
            return response()->noContent();
        }

        return $user->authResponse();
    }
}
