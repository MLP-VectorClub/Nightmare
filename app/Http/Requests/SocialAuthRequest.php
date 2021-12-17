<?php

namespace App\Http\Requests;

use App\Enums\SocialProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class SocialAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function validator()
    {
        return Validator::make(['provider' => $this->route('provider')], [
            'provider' => ['required', new Enum(SocialProvider::class)],
        ]);
    }

    /**
     * @inheritDoc
     * @return array = [
     *     'provider' => SocialProvider::cases(),
     * ]
     */
    public function validated()
    {
        return parent::validated();
    }
}
