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
                'id' => 1,
                'name' => 'Asamblea Estatal 2025',
                'description' => 'Evento en cada uno de los municipios del estado de Chihuahua para la difusión de información de la Asamblea 2025.',
                'latitude' => 28.678373,
                'longitude' => -106.079303,
                'address' => 'Sindicato Sección VIII Chihuahua',
                'allowed_radius' => 100,
                'start_time' => '2025-09-01 09:00:00',
                'end_time' => '2025-09-01 12:00:00',
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
