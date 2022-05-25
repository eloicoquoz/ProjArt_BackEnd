<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

protected $fillable=['Label','Debut','Fin'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function salles()
    {
        return $this->belongsToMany(Salle::class);
    }
    public function classes()
    {
        return $this->belongsToMany(Classe::class);
    }

    public function remarques() { 
        return $this->hasMany(Remarque::class); 
    } 

    public function matiere() { 
        return $this->belongsTo(Matiere::class); 
    }
}
