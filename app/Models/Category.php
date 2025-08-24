<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, BelongsToStore, HasUuids;

    //* PROPERTIES

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'image' => 'string',
        'status' => 'string',
        'sort_order' => 'integer',
    ];


    protected $primaryKey = 'id';


    public $incrementing = false;

    protected $guarded = [];

    protected $keyType = 'string';
    

    //* MODEL EVENTS

    /**
     * Boot method para configurar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Generar slug antes de crear
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateUniqueSlug($category->name);
            }
        });

        // Generar slug antes de actualizar si el nombre cambió
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = $category->generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    //* RELATIONS

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    //* SCOPES

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    private function slugExists(string $slug, ?string $ignoreId = null): bool
    {
        $query = static::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Genera un slug único basado en el texto proporcionado
     * 
     * @param string $text
     * @param string|null $ignoreId
     * @return string
     */
    private function generateUniqueSlug(string $text, ?string $ignoreId = null): string
    {
        // Crear slug base
        $baseSlug = Str::slug($text);
        $slug = $baseSlug;
        $counter = 1;

        // Verificar unicidad y agregar número si es necesario
        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    

}
