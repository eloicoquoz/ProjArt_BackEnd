<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable=['id'];

    public function classes() { 
        return $this->hasMany(Classe::class); 
    } 
}
