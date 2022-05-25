<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable=['Titre','Debut','Fin','Description','Lieu'];

    public function user() {
        return $this->belongsTo(User::class);
    } 

    public function remarques() { 
        return $this->hasMany(Remarque::class); 
    } 
}
