<?php

namespace App\Models;

use App\Models\User;
use App\Service\Contracts\InterfaceModel;
use App\Service\Traits\Model as ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements InterfaceModel
{
    use ModelTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

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
        'product_category_id',
        'name',
        'slug',
        'image',
        'description',
        'price',
        'on_sale',
        'sale_price',
        'stock',
        'status'
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
     * The routes of module products.
     *
     * @return void
     */
    public static function routes() 
    {
        $route = app()->make('router');
        return [
            $route->namespace('Product')->group(function() use ($route) {
                $route->resource('/products', 'ProductController', [
                    'except' => []
                ])->middleware(\App\Models\Permission::getPermission('products'));
                // Uncomment this if your implement multiple delete resource
                $route->delete('/products', [
                    'as' => 'products.destroyMany',
                    'uses' => 'ProductController@destroyMany'
                ]);
            }),
        ];
    }

    /**
     * products belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdUser()
    {
        // belongsTo(RelatedModel, foreignKey = created_by, keyOnRelatedModel = id)
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * products belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedUser()
    {
        // belongsTo(RelatedModel, foreignKey = updated_by, keyOnRelatedModel = id)
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * products belongs to User.
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

    /**
     * Associate for product_categories attribute.
     */
    public function productCategories()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id');
    }
}
