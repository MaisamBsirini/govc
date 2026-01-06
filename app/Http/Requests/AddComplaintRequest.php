<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'description' => 'required|string',
            'department' => 'required|in:Interior,Health,Education,Justice,AntiCorruption,Communications,Labor,ConsumerProtection',
            'location' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
