<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $name
 * @property string $code
 * @property string|null $dekan
 * @property string|null $akreditasi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProgramStudi> $programStudi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 */
class Fakultas extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'dekan',
        'akreditasi',
    ];

    public function programStudi(): HasMany
    {
        return $this->hasMany(ProgramStudi::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
