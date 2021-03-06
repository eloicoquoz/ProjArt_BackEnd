<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Salle;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Remarque;

class Cours extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['matiere_id', 'Debut', 'Fin'];

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

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function remarques()
    {
        return $this->hasMany(Remarque::class);
    }

}
