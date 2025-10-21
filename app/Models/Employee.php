<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'matricula',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'nombre_completo',
        'puesto',
        'puesto_desc',
        'departamento',
        'departamento_desc',
        'clave_adscripcion',
        'tipo_contratacion',
        'tipo_cempleado',
        'curp',
        'nss',
        'rfc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Agregar casts si necesitas convertir tipos de datos específicos
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // La tabla no tiene created_at/updated_at

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'matricula';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'matricula');
    }

    /**
     * Get the pending attendances for this employee.
     */
    public function pendingAttendances()
    {
        return $this->hasMany(PendingAttendance::class, 'employee_matricula', 'matricula');
    }
}
