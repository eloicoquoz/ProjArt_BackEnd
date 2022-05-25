<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remarque extends Model
{
    use HasFactory;

    protected $fillable=['Titre','Description','Visibilite'];

    public function event() {
        return $this->belongsTo(Event::class);
    } 
    public function user() {
        return $this->belongsTo(User::class);
    } 
    public function cours() {
        return $this->belongsTo(Cours::class);
    } 
}
