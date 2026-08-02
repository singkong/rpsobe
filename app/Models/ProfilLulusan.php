<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $program_studi_id
 * @property string $name
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read ProgramStudi $programStudi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CPL> $cpls
 */
class ProfilLulusan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'program_studi_id',
        'name',
        'deskripsi',
    ];

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'profil_lulusan_cpl')
            ->withTimestamps();
    }
}
