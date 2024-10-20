<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class OffreRequest extends FormRequest
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
            // 'description' => 'required|string|max:200',
            // 'lieu' => 'required|string|max:20',
            // 'salaire' => 'required|numeric|max:99999999',
            // 'nombre_postes' => 'required|numeric|max:9999',
            // 'horaire' => 'required|string|max:6',
            // 'date_debut' => 'required|date_format:Y-m-d',
            // 'date_fin' => 'nullable|date_format:Y-m-d',
            // 'date_limite' => 'nullable|date_format:Y-m-d',
            // 'profil' => 'required|string|max:500',
            // 'service_ids' => 'required|array',
            // 'service_ids.*' => 'exists:services,id',
        ];
    }

     /**
     * Messages d'erreurs personnalisés pour chaque règle de validation.
     */
    public function messages(): array
    {
        return [
            'description.required' => 'La description est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 200 caractères.',
            'lieu.required' => 'Le lieu est obligatoire.',
            'lieu.string' => 'Le lieu doit être une chaîne de caractères.',
            'lieu.max' => 'Le lieu ne doit pas dépasser 20 caractères.',
            'salaire.required' => 'Le salaire est obligatoire.',
            'salaire.numeric' => 'Le salaire doit être un nombre.',
            'salaire.max' => 'Le salaire ne doit pas dépasser 99999999.',
            'nombre_postes.required' => 'Le nombre de postes est obligatoire.',
            'nombre_postes.numeric' => 'Le nombre de postes doit être un nombre.',
            'nombre_postes.max' => 'Le nombre de postes ne doit pas dépasser 9999.',
            'horaire.required' => 'L\'horaire est obligatoire.',
            'horaire.string' => 'L\'horaire doit être une chaîne de caractères.',
            'horaire.max' => 'L\'horaire ne doit pas dépasser 6 caractères.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date_format' => 'La date de début doit être au format YYYY-MM-DD.',
            'date_fin.date_format' => 'La date de fin doit être au format YYYY-MM-DD.',
            'date_limite.date_format' => 'La date limite doit être au format YYYY-MM-DD.',
            'profil.required' => 'Le profil est obligatoire.',
            'profil.string' => 'Le profil doit être une chaîne de caractères.',
            'profil.max' => 'Le profil ne doit pas dépasser 500 caractères.',
            'service_id.required' => 'Le service est obligatoire.',
        ];
    }

    /**
     * Ajout de validations personnalisées avec des règles conditionnelles.
     */
    public function withValidator(Validator $validator): void
{
    $validator->after(function ($validator) {
        $dateDebut = $this->input('date_debut');
        $dateFin = $this->input('date_fin');
        $dateLimite = $this->input('date_limite');

        // Vérifie que la date de fin est renseignée et qu'elle vient après la date de début
        if ($dateFin && $dateFin < $dateDebut) {
            $validator->errors()->add(
                'date_fin',
                'La date de fin doit être après la date de début.'
            );
        }

        // Vérifie que la date limite est renseignée et qu'elle vient après la date de début
        // La première partie $dateLimite s'assure que le champ est rempli
        // La deuxième partie $dateLimite < $dateDebut vérifie l'ordre des dates
        if ($dateLimite && $dateLimite < $dateDebut) {
            $validator->errors()->add(
                'date_limite',
                'La date limite doit être après la date de début.'
            );
        }
    });
}

}
