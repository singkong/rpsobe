<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $nidn
 * @property string $name
 * @property string|null $gelar_depan
 * @property string|null $gelar_belakang
 * @property string|null $jabatan_fungsional
 * @property string|null $bidang_keahlian
 * @property string|null $email
 * @property string|null $phone
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MataKuliah> $mataKuliah
 */
class Dosen extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'nidn',
        'name',
        'gelar_depan',
        'gelar_belakang',
        'jabatan_fungsional',
        'bidang_keahlian',
        'email',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'mata_kuliah_dosen', 'dosen_id', 'mata_kuliah_id')
            ->withTimestamps();
    }
}
