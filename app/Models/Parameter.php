<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'type'];
    protected $table = 'parameters';

    public function images()
    {
        return $this->hasOne(ParameterImage::class, 'parameter_id');
    }
}
