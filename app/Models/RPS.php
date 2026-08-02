<?php

namespace App\Models;

use App\Enums\RPSStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $mata_kuliah_id
 * @property int $semester_id
 * @property int $user_id
 * @property array|null $dosen_pengampu_json
 * @property string|null $deskripsi
 * @property RPSStatus $status
 * @property string $version_label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read MataKuliah $mataKuliah
 * @property-read Semester $semester
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CPL> $cpl
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CPMK> $cpml
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubCPMK> $subCpmk
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MateriPertemuan> $materiPertemuan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Assessment> $assessment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RPSVersion> $versions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RPSReview> $reviews
 */
class RPS extends Model
{
    use SoftDeletes;

    protected $table = 'rps';

    protected $fillable = [
        'mata_kuliah_id',
        'semester_id',
        'user_id',
        'dosen_pengampu_json',
        'deskripsi',
        'status',
        'version_label',
    ];

    protected function casts(): array
    {
        return [
            'dosen_pengampu_json' => 'array',
            'status' => RPSStatus::class,
        ];
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cpl(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'rps_cpl')
            ->withTimestamps();
    }

    public function cpml(): HasMany
    {
        return $this->hasMany(CPMK::class);
    }

    public function subCpmk(): HasMany
    {
        return $this->hasManyThrough(SubCPMK::class, CPMK::class, 'rps_id', 'cpml_id');
    }

    public function materiPertemuan(): HasMany
    {
        return $this->hasMany(MateriPertemuan::class);
    }

    public function assessment(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(RPSVersion::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(RPSReview::class);
    }

    public function scopeByStatus(Builder $query, RPSStatus|string $status): void
    {
        $value = $status instanceof RPSStatus ? $status->value : $status;
        $query->where('status', $value);
    }

    public function scopeByDosen(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    public function scopeByProdi(Builder $query, int $prodiId): void
    {
        $query->whereHas('mataKuliah.kurikulum', function ($q) use ($prodiId) {
            $q->where('program_studi_id', $prodiId);
        });
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [RPSStatus::Draft, RPSStatus::Revision]);
    }
}
