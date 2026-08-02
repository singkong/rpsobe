<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'akronim',
        'alamat',
        'website',
        'phone',
        'email',
        'logo',
        'akreditasi',
        'is_active',
        'subscription_package',
        'subscription_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function fakultas(): HasMany
    {
        return $this->hasMany(\App\Models\Fakultas::class);
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function dosens(): HasMany
    {
        return $this->hasMany(Dosen::class);
    }

    public function referensis(): HasMany
    {
        return $this->hasMany(Referensi::class);
    }
}
