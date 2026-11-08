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
        $noMatricula = filter_var($input['no_matricula'] ?? false, FILTER_VALIDATE_BOOLEAN);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'employee_id' => $noMatricula
                ? ['nullable']
                : ['required', 'string', 'max:12', 'unique:users', 'regex:/^[0-9]+$/'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'employee_id.required' => 'La matrícula es obligatoria o marque la casilla "No cuento con matrícula".',
            'employee_id.unique' => 'Esta matrícula ya se encuentra registrada por otro usuario.',
            'employee_id.regex' => 'La matrícula solo debe contener números.',
        ])->validate();

        $employeeId = (!$noMatricula && !empty($input['employee_id'])) ? trim($input['employee_id']) : null;

        if ($employeeId !== null) {
            // Verificar si la matrícula existe en la tabla employees
            $employee = Employee::where('matricula', $employeeId)->first();
            $status = $employee ? 'active' : 'pending_verification';
        } else {
            // Usuario sin matrícula: queda en pending_verification para que el administrador la asigne
            $status = 'pending_verification';
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'employee_id' => $employeeId,
            'status' => $status,
            'password' => Hash::make($input['password']),
        ]);
    }
}
