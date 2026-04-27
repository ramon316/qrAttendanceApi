<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MothersDayRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone',
        'matricula',
        'name',
        'email',
        'phone',
    ];

    /**
     * Relación con el empleado.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'matricula', 'matricula');
    }
}
