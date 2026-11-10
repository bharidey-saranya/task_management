<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'project_id',
        'is_active',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The project that this task belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope tasks ordered by priority ascending.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Scope query to a specific project if provided, or unassigned tasks if null.
     */
    public function scopeForProject(Builder $query, ?int $projectId): Builder
    {
        return $query->when(
            $projectId !== null,
            fn (Builder $q) => $q->where('project_id', $projectId),
            fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->whereNull('project_id')->orWhere('project_id', 0))
        );
    }

    /**
     * Scope query to only active (non-deleted) tasks.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate the next available priority for a given project or globally.
     */
    public static function nextPriority(?int $projectId = null): int
    {
        return ((int) static::query()
            ->active()
            ->forProject($projectId)
            ->max('priority')) + 1;
    }
}
