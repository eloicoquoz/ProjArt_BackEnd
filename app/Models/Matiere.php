<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;

    protected $fillable=['id','Annee'];

    public function cours() { 
        return $this->hasMany(Cours::class); 
    }

    public function users() { 
        return $this->belongsToMany(User::class); 
    }

    public function remarque(){
        return $this->hasMany(Remarque::class);
    }
}
