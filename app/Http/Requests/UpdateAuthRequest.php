<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nom' => 'sometimes|string',
            'nom_utilisateur'=> 'sometimes|string|unique:users,nom_utilisateur',
            'prenom' => 'sometimes|string',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string|max:12|unique:users,telephone',
            'sexe' => 'sometimes|in:Féminin,Masculin',
            'password' => 'sometimes|string|min:8',
            'service_id' => 'required|exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'photo.image' => 'La photo doit être une image valide.',
            'photo.mimes' => 'La photo doit être au format jpeg, png, ou jpg .',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'nom_utilisateur.unique' => 'Le nom d\'utilisateur est déjà pris.',
            'nom_utilisateur.max' => 'Le nom d\'utilisateur ne peut pas dépasser 20 caractères.',
            'email.unique' => 'L\'adresse email est déjà utilisée.',
            'email.email' => 'L\'adresse email doit être valide.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'telephone.unique' => 'Le numéro de téléphone est déjà utilisé.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 12 caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        ];
    }
}
