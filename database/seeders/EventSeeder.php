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
                'name' => 'Reunión de Planificación Estratégica',
                'description' => 'Reunión mensual para revisar objetivos y estrategias del departamento de TI.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Sala de Juntas Principal, Edificio Administrativo',
                'allowed_radius' => 30,
                'start_time' => Carbon::now()->addDays(1)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(1)->setTime(11, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Capacitación en Nuevas Tecnologías',
                'description' => 'Taller de capacitación sobre las últimas herramientas de desarrollo y metodologías ágiles.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Aula de Capacitación 2, Centro de Formación',
                'allowed_radius' => 25,
                'start_time' => Carbon::now()->addDays(3)->setTime(14, 0),
                'end_time' => Carbon::now()->addDays(3)->setTime(17, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Presentación de Resultados Q4',
                'description' => 'Presentación de los resultados del cuarto trimestre a la junta directiva.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Auditorio Principal, Piso 5',
                'allowed_radius' => 40,
                'start_time' => Carbon::now()->addDays(7)->setTime(10, 30),
                'end_time' => Carbon::now()->addDays(7)->setTime(12, 30),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Reunión de Seguridad Informática',
                'description' => 'Revisión de políticas de seguridad y análisis de vulnerabilidades del sistema.',
                'latitude' => 0,
                'longitude' => 0,
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
            [
                'name' => 'Revisión de Proyecto Mobile App',
                'description' => 'Revisión del progreso del proyecto de aplicación móvil y planificación de siguientes fases.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Sala de Proyectos, Área de Desarrollo',
                'allowed_radius' => 25,
                'start_time' => Carbon::now()->addDays(8)->setTime(11, 0),
                'end_time' => Carbon::now()->addDays(8)->setTime(13, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Ceremonia de Retrospectiva Sprint',
                'description' => 'Retrospectiva del sprint actual para identificar mejoras y celebrar logros del equipo.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Sala Ágil, Espacio de Innovación',
                'allowed_radius' => 40,
                'start_time' => Carbon::now()->addDays(12)->setTime(14, 30),
                'end_time' => Carbon::now()->addDays(12)->setTime(16, 0),
                'active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Workshop de Testing Automatizado',
                'description' => 'Taller práctico sobre implementación de pruebas automatizadas y mejores prácticas de QA.',
                'latitude' => 0,
                'longitude' => 0,
                'address' => 'Laboratorio de Testing, Piso 4',
                'allowed_radius' => 45,
                'start_time' => Carbon::now()->addDays(16)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(16)->setTime(12, 30),
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
