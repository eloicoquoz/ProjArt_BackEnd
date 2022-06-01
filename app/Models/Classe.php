<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable=['id', 'departement_id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function cours()
    {
        return $this->belongsToMany(Cours::class);
    }

    public function departement() { 
        return $this->belongsTo(Departement::class, 'departement_id');
    }

}
