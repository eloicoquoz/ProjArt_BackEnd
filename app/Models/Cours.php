<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

protected $fillable=['Label','Debut','Fin'];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
