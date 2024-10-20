<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthRequest extends FormRequest
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
            // 2048KO est égal à 2Mo
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nom' => 'required|string',
            'nom_utilisateur'=> 'required|string|unique:users|max:20',
            'prenom' => 'required|string',
            'email' => 'nullable|string|email|max:255|unique:users',
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:12|unique:users,telephone',
            'sexe' => 'required|in:Féminin,Masculin',
            'role' => 'required|string|in:employeur,demandeur_d_emploi,admin',
            'password' => 'required|string|min:8',
            'service_id' => 'nullable|exists:services,id',
        ];
    }
    public function messages():array
    {
        return [
            'photo.required' => 'Une photo est requise.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être au format jpeg, png, ou jpg.',
            'photo.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo.',

            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',

            'nom_utilisateur.required' => 'Le nom d\'utilisateur est obligatoire.',
            'nom_utilisateur.string' => 'Le nom d\'utilisateur doit être une chaîne de caractères.',
            'nom_utilisateur.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
            'nom_utilisateur.max' => 'Le nom d\'utilisateur ne doit pas dépasser 20 caractères.',

            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',

            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',

            'adresse.required' => 'L\'adresse est obligatoire.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',

            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 12 chiffres.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',

            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être "Féminin" ou "Masculin".',

            'role.required' => 'Le rôle est obligatoire.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            ['success' => false, 'errors' => $validator->errors()],
            422
        ));
    }
}
