<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Savedrecipe extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'saved_recipes_data' 
    ]; 

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    } 
    public function posts()
    {
        return $this->hasMany(Recipe::class);
    }
}
