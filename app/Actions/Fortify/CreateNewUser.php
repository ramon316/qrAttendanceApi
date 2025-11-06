<?php

namespace App\Actions\Fortify;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'employee_id' => ['required', 'string', 'max:12', 'unique:users', 'regex:/^[0-9]+$/'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Verificar si la matrícula existe en la tabla employees
        $employee = Employee::where('matricula', $input['employee_id'])->first();

        // Determinar el status basado en si existe la matrícula
        $status = $employee ? 'active' : 'pending_verification';

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'employee_id' => $input['employee_id'],
            'status' => $status,
            'password' => Hash::make($input['password']),
        ]);
    }
}
