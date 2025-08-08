<?php

namespace App\Models\Traits;

use App\Services\CurrentStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class StoreScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $storeId = CurrentStore::id();
        if ($storeId && Schema::hasColumn($model->getTable(), 'store_id')) {
            $builder->where($model->getTable() . '.store_id', $storeId);
        }
    }
}

trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        static::addGlobalScope(new StoreScope());

        // En creación, si el modelo tiene columna store_id y hay tienda actual, asignarla
        static::creating(function (Model $model) {
            if (Schema::hasColumn($model->getTable(), 'store_id') && empty($model->getAttribute('store_id'))) {
                $storeId = CurrentStore::id();
                if ($storeId) {
                    $model->setAttribute('store_id', $storeId);
                }
            }
        });
    }

    public function scopeForCurrentStore(Builder $query): Builder
    {
        $storeId = CurrentStore::id();
        return $storeId ? $query->where($this->getTable() . '.store_id', $storeId) : $query;
    }
}
