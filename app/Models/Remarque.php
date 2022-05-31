<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remarque extends Model
{
    use HasFactory;

    protected $fillable=['Titre','Description','Visibilite', 'Date'];

    public function user() {
        return $this->belongsTo(User::class);
    } 
    public function matieres() {
        return $this->belongsTo(Matiere::class);
    } 
}
