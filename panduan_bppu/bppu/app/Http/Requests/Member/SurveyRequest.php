<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Indicates if the request expects JSON in return.
     */
    public function expectsJson(): bool
    {
        return true;
    }

    /**
     * Indicates if the request is AJAX.
     */
    public function wantsJson(): bool
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
            'rating_pelayanan' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_kualitas' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_variasi' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_harga' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_suasana' => ['required', 'integer', 'min:1', 'max:5'],
            'menu_favorit' => ['required', 'string', 'max:500'],
            'menu_rekomendasi' => ['required', 'string', 'max:500'],
            'kritik_saran' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating_pelayanan.required' => 'Rating pelayanan wajib diisi',
            'rating_pelayanan.min' => 'Rating pelayanan minimal 1',
            'rating_pelayanan.max' => 'Rating pelayanan maksimal 5',
            'rating_kualitas.required' => 'Rating kualitas menu wajib diisi',
            'rating_kualitas.min' => 'Rating kualitas menu minimal 1',
            'rating_kualitas.max' => 'Rating kualitas menu maksimal 5',
            'rating_variasi.required' => 'Rating variasi menu wajib diisi',
            'rating_variasi.min' => 'Rating variasi menu minimal 1',
            'rating_variasi.max' => 'Rating variasi menu maksimal 5',
            'rating_harga.required' => 'Rating harga wajib diisi',
            'rating_harga.min' => 'Rating harga minimal 1',
            'rating_harga.max' => 'Rating harga maksimal 5',
            'rating_suasana.required' => 'Rating suasana kantin wajib diisi',
            'rating_suasana.min' => 'Rating suasana kantin minimal 1',
            'rating_suasana.max' => 'Rating suasana kantin maksimal 5',
            'menu_favorit.required' => 'Menu favorit wajib diisi',
            'menu_favorit.max' => 'Menu favorit maksimal 500 karakter',
            'menu_rekomendasi.required' => 'Rekomendasi menu wajib diisi',
            'menu_rekomendasi.max' => 'Rekomendasi menu maksimal 500 karakter',
            'kritik_saran.required' => 'Kritik dan saran wajib diisi',
            'kritik_saran.max' => 'Kritik dan saran maksimal 1000 karakter',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
