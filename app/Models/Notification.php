<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable=['Objet','Message','EnvoiHeureDate'];

    public function destinataires() { 
        return $this->hasMany(Destinataire::class); 
    }  

    public function user() {
        return $this->belongsTo(User::class);
    } 
}
