<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function cours()
    {
        return $this->belongsToMany(Cours::class);
    }

    public function destinataires() { 
        return $this->hasMany(Destinataire::class); 
    } 

    public function filiere() { 
        return $this->belongsTo(Filiere::class); 
    }

}