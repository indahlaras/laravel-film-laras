<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{
    use HasFactory;
    protected $table = 'films';
    protected $fillable = ['title','sinopsis','year','poster','genre_id'];

    public function getPosterAttribute($value): string
    {
        return 'storage/image/' . basename($value);
    }

    public function perans(): HasMany
    {
        return $this->hasMany(Peran::class);
    }
}
