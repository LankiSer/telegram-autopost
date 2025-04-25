<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
        
        // Добавляем правила только для администратора
        if ($this->user()->isAdmin()) {
            $rules = array_merge($rules, [
                'openai_api_key' => ['nullable', 'string', 'max:255'],
                'telegram_bot_token' => ['nullable', 'string', 'max:255'],
                'telegram_bot_name' => ['nullable', 'string', 'max:255'],
                'telegram_bot_username' => ['nullable', 'string', 'max:255'],
                'telegram_bot_description' => ['nullable', 'string'],
                'telegram_bot_link' => ['nullable', 'string', 'max:255', 'url'],
            ]);
        }
        
        return $rules;
    }
}
