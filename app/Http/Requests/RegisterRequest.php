<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email|max:255',
            'name' => 'required|string|max:255', 
            'password' => 'required|string|min:8|max:255',
        ];
    }
    public function sanitize()
    {
        $this->merge([
            'email' => filter_var($this->email, FILTER_SANITIZE_EMAIL), 
            'name' => filter_var($this->name, FILTER_SANITIZE_STRING),  
            'password' => filter_var($this->password, FILTER_SANITIZE_STRING),  
        ]);
    }
}
