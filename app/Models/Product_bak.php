<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\IBaseDb;

class ProductBak extends ModelGlxBase implements IBaseDb
{
    use HasFactory;
    use \LadLib\Laravel\Database\TraitModelExtra;
    use SoftDeletes;

    protected $guarded = [];

    public function getDbName()
    {
        return env('DB_DATABASE');
    }

    public function getTableName()
    {
        return $this->getTable();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        $ret = $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id')->withTimestamps();

        //        dd($ret);
        return $ret;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function productImages()
    {
        $ret = $this->hasMany(ProductImage::class, 'product_id');

        return $ret;
    }

    public function getProductSearch($request)
    {
        $products = new ProductBak();
        if (! empty($request->product_id)) {
            $products = $products->where('products.id', $request->product_id);
        }
        if (! empty($request->name)) {
            $products = $products->where('products.name', 'like', '%'.$request->name.'%');
        }
        if (! empty($request->category_id)) {
            $products = $products->where('products.category_id', $request->category_id);
        }
        if (! empty($request->tags)) {
            $products = $products->join('product_tags', 'products.id', 'product_tags.product_id')
                ->join('tags', 'product_tags.tag_id', 'tags.id')
                ->where('tags.name', 'like', '%'.$request->tags.'%');
        }
        $products = $products
            ->select('products.*')
            ->latest('products.created_at')
            ->paginate(5);

        return $products;
    }
}
