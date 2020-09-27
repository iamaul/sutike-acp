<?php

namespace App\Models;

use App\Models\User;
use App\Service\Contracts\InterfaceModel;
use App\Service\Traits\Model as ModelTrait;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model implements InterfaceModel
{
    use ModelTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'blog_tags';

    /**
     * Indicates if the model should be timestamp.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * The attributes that should be hidden to native types.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    /**
     * The attributes that should be appends to native types.
     *
     * @var array
     */
    protected $appends = [
        //
    ];

    /**
     * The routes of module blog_tags.
     *
     * @return void
     */
    public static function routes() 
    {
        $route = app()->make('router');
        return [
            $route->namespace('BlogTag')->group(function() use ($route) {
                $route->resource('/blog-tags', 'BlogTagController', [
                    'except' => []
                ])->middleware(\App\Models\Permission::getPermission('blog-tags'));
                // Uncomment this if your implement multiple delete resource
                $route->delete('/blog-tags', [
                    'as' => 'blog-tags.destroyMany',
                    'uses' => 'BlogTagController@destroyMany'
                ]);
            }),
        ];
    }

    /**
     * blog_tags belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdUser()
    {
        // belongsTo(RelatedModel, foreignKey = created_by, keyOnRelatedModel = id)
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * blog_tags belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedUser()
    {
        // belongsTo(RelatedModel, foreignKey = updated_by, keyOnRelatedModel = id)
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * blog_tags belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedUser()
    {
        // belongsTo(RelatedModel, foreignKey = deleted_by, keyOnRelatedModel = id)
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    /**
     * Accessor for created_at attribute.
     *
     * @return returnType
     */
    public function getCreatedAtAttribute($value)
    {
        return carbon()->parse($this->attributes['created_at'])->format('d-m-Y');
    }

    /**
     * Accessor for updated_at attribute.
     *
     * @return returnType
     */
    public function getUpdatedAtAttribute($value)
    {
        return carbon()->parse($this->attributes['updated_at'])->format('d-m-Y');
    }

    /**
     * Accessor for deleted_at attribute.
     *
     * @return returnType
     */
    public function getDeletedAtAttribute($value)
    {
        return carbon()->parse($this->attributes['deleted_at'])->format('d-m-Y');
    }
}
