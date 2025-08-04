<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
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

   


    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
