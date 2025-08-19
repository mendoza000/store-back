<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
    ];


    //Relaciones jerárquicas

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    //Relaciones con producto // Scope para categorías activas
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // Scope para categorías principales (sin padre)
    public function scopeRootCategories(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // Mutator para slug automático
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Método para obtener la ruta completa (breadcrumb)
    public function getPath(): array
    {
        $path = [];
        $category = $this;
        
        while ($category) {
            array_unshift($path, $category);
            $category = $category->parent;
        }
        
        return $path;
    }

    // Método para obtener todos los descendientes
    public function getAllChildren()
    {
        return $this->children()->with('allChildren');
    }

   


    /**
     * Boot del modelo para manejar eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Evento antes de crear - se ejecuta después de todos los traits
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateUniqueSlug($category->name ?? '');
            }
        });

        // Evento antes de actualizar
        static::updating(function ($category) {
            // Solo regenerar slug si el nombre cambió y no se proporcionó un slug específico
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = $category->generateUniqueSlug($category->name ?? '');
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    /**
     * Genera un slug único basado en el nombre
     */
    public function generateUniqueSlug(string $name): string
    {
        if (empty($name)) {
            return '';
        }

        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        // Verificar unicidad del slug, excluyendo el registro actual si existe
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verifica si el slug ya existe
     */
    private function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);
        
        // Si estamos actualizando un registro existente, excluirlo de la verificación
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }
}
