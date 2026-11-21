<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    /**
     * Field yang boleh diisi
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'name',
        'slug',
    ];

    /**
     * Relasi ke Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Boot model
     *
     * Membuat slug otomatis sebelum data disimpan.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Generate slug sebelum menyimpan data
        static::saving(function ($category) {

            // Jika slug masih kosong
            if (empty($category->slug)) {

                // Buat slug berdasarkan nama category
                $category->slug = Str::slug($category->name);
            }
        });
    }
}