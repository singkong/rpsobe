<?php

namespace App\Models;

use App\Enums\Jenjang;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $fakultas_id
 * @property string $name
 * @property string $code
 * @property string $jenjang
 * @property string|null $akreditasi
 * @property string|null $kaprodi_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Fakultas $fakultas
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Kurikulum> $kurikulum
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProfilLulusan> $profilLulusan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CPL> $cpls
 */
class ProgramStudi extends Model
{
    use SoftDeletes;

    protected $table = 'program_studi';

    protected $fillable = [
        'fakultas_id',
        'name',
        'code',
        'jenjang',
        'akreditasi',
        'kaprodi_name',
    ];

    protected function casts(): array
    {
        return [
            'jenjang' => Jenjang::class,
        ];
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function tenant(): ?Tenant
    {
        return $this->fakultas?->tenant;
    }

    public function kurikulum(): HasMany
    {
        return $this->hasMany(Kurikulum::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function profilLulusan(): HasMany
    {
        return $this->hasMany(ProfilLulusan::class);
    }

    public function cpls(): HasMany
    {
        return $this->hasMany(CPL::class);
    }
}
