<?php 

namespace App\Service\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

trait Model 
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->id = Str::orderedUuid()->toString();
                $user_id = auth()->check() ? auth()->user()->id : NULL;
                if ($model->getConnection()->getSchemaBuilder()->hasTable($model->getTable()) && $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'created_by'))
                    $model->created_by = $user_id;
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
        static::updating(function ($model) {
            try {
                $user_id = auth()->check() ? auth()->user()->id : NULL;
                if ($model->getConnection()->getSchemaBuilder()->hasTable($model->getTable()) && $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'updated_by'))
                    $model->updated_by = $user_id;
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
        static::deleting(function ($model) {
            try {
                $user_id = auth()->check() ? auth()->user()->id : NULL;
                if ($model->getConnection()->getSchemaBuilder()->hasTable($model->getTable()) && $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'deteted_by'))
                    $model->deleted_by = $user_id;
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}