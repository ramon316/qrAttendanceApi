<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_cannot_be_rendered_if_support_is_disabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_register_without_matricula(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'name' => 'Sin Matricula User',
            'email' => 'sinmatricula@example.com',
            'no_matricula' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = \App\Models\User::where('email', 'sinmatricula@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->employee_id);
        $this->assertEquals('pending_verification', $user->status);
    }

    public function test_registration_fails_if_no_matricula_and_checkbox_not_checked(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'name' => 'Failed User',
            'email' => 'failed@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['employee_id']);
        $this->assertGuest();
    }

    public function test_new_users_can_register_with_matricula(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        \App\Models\Employee::forceCreate([
            'matricula' => '12345678',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Lopez',
            'nombres' => 'Juan',
            'nombre_completo' => 'Juan Perez Lopez',
            'puesto' => 'Docente',
            'puesto_desc' => 'Docente Asignatura',
            'departamento' => 'Sistemas',
            'departamento_desc' => 'Ingenieria en Sistemas',
            'clave_adscripcion' => '123',
            'tipo_contratacion' => 'Base',
            'tipo_empleado' => 'Docente',
            'curp' => 'PELJ900101HDFRRN01',
            'nss' => '12345678901',
            'rfc' => 'PELJ900101ABC',
        ]);

        $response = $this->post('/register', [
            'name' => 'Con Matricula User',
            'email' => 'conmatricula@example.com',
            'employee_id' => '12345678',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = \App\Models\User::where('email', 'conmatricula@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('12345678', $user->employee_id);
        $this->assertEquals('active', $user->status);
    }
}
