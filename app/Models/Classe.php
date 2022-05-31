<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $fillable=['id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function cours()
    {
        return $this->belongsToMany(Cours::class);
    }

    public function filiere() { 
        return $this->belongsTo(Filiere::class); 
    }

}
