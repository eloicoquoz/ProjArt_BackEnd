<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable=['id','Annee'];

    public function cours() { 
        return $this->hasMany(Cours::class); 
    }

    public function users() { 
        return $this->belongsToMany(User::class); 
    }

   
}
