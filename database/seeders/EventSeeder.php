<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eventos para el usuario Admin (ID: 1)
        $adminEvents = [
            [
                'name' => 'Reunión Sindicato',
                'description' => 'Reunión mensual para revisar objetivos y estrategias del departamento de TI.',
                'latitude' => 28.678373,
                'longitude' => -106.079303,
                'address' => 'Sala de Juntas Principal, Edificio Administrativo',
                'allowed_radius' => 30,
                'start_time' => Carbon::now()->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(5)->setTime(11, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Junta Sindicato',
                'description' => 'Taller de capacitación sobre las últimas herramientas de desarrollo y metodologías ágiles.',
                'latitude' => 28.678373,
                'longitude' => -106.079303,
                'address' => 'Aula de Capacitación 2, Centro de Formación',
                'allowed_radius' => 25,
                'start_time' => Carbon::now()->setTime(8, 0),
                'end_time' => Carbon::now()->addDays(3)->setTime(17, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Reunión Sindicato no activo',
                'description' => 'Reunión mensual para revisar objetivos y estrategias del departamento de TI.',
                'latitude' => 28.678373,
                'longitude' => -106.079303,
                'address' => 'Sala de Juntas Principal, Edificio Administrativo',
                'allowed_radius' => 30,
                'start_time' => Carbon::now()->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(5)->setTime(11, 0),
                'active' => false,
                'user_id' => 1,
            ],
            [
                'name' => 'Junta Sindicato no activo',
                'description' => 'Taller de capacitación sobre las últimas herramientas de desarrollo y metodologías ágiles.',
                'latitude' => 28.678373,
                'longitude' => -106.079303,
                'address' => 'Aula de Capacitación 2, Centro de Formación',
                'allowed_radius' => 25,
                'start_time' => Carbon::now()->setTime(8, 0),
                'end_time' => Carbon::now()->addDays(3)->setTime(17, 0),
                'active' => false,
                'user_id' => 1,
            ],
            [
                'name' => 'CCYC chihuahua',
                'description' => 'Presentación de los resultados del cuarto trimestre a la junta directiva.',
                'latitude' => 28.644387,
                'longitude' => -106.080066,
                'address' => 'Auditorio Principal, Piso 5',
                'allowed_radius' => 40,
                'start_time' => Carbon::now()->setTime(10, 30),
                'end_time' => Carbon::now()->addDays(7)->setTime(12, 30),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Reunión Centro de capacitación',
                'description' => 'Revisión de políticas de seguridad y análisis de vulnerabilidades del sistema.',
                'latitude' => 28.644387,
                'longitude' => -106.080066,
                'address' => 'Sala de Conferencias B, Departamento de TI',
                'allowed_radius' => 20,
                'start_time' => Carbon::now()->addDays(10)->setTime(15, 0),
                'end_time' => Carbon::now()->addDays(10)->setTime(16, 30),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Evaluación de Proveedores',
                'description' => 'Reunión para evaluar y seleccionar nuevos proveedores de servicios tecnológicos.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Sala de Reuniones Ejecutiva, Piso 3',
                'allowed_radius' => 35,
                'start_time' => Carbon::now()->addDays(14)->setTime(13, 0),
                'end_time' => Carbon::now()->addDays(14)->setTime(15, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Taller de Desarrollo Personal',
                'description' => 'Sesión de desarrollo personal enfocada en habilidades de comunicación y liderazgo.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Aula de Formación 1, Centro de Desarrollo',
                'allowed_radius' => 50,
                'start_time' => Carbon::now()->addDays(2)->setTime(8, 30),
                'end_time' => Carbon::now()->addDays(2)->setTime(12, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Sesión de Feedback del Equipo',
                'description' => 'Reunión para compartir feedback y mejorar la colaboración del equipo de desarrollo.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Sala de Trabajo Colaborativo, Piso 2',
                'allowed_radius' => 30,
                'start_time' => Carbon::now()->addDays(5)->setTime(16, 0),
                'end_time' => Carbon::now()->addDays(5)->setTime(17, 30),
                'active' => true,
                'user_id' => 1,
            ],
        ];

        // Crear eventos para el Admin
        foreach ($adminEvents as $eventData) {
            Event::create($eventData);
        }
    }
}
