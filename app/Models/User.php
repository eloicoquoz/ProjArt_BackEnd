<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'Email';
    public $incrementing = false;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'FullName', 'Email', 'Password',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function classes()
    {
        return $this->belongsToMany(Classe::class);
    }
    public function cours()
    {
        return $this->belongsToMany(Cours::class);
    }

    public function notifications() { 
        return $this->hasMany(Notification::class); 
    } 
    public function destinataires() { 
        return $this->hasMany(Destinataire::class); 
    } 
    public function remarques() { 
        return $this->hasMany(Remarque::class); 
    } 
    public function events() { 
        return $this->hasMany(Event::class); 
    } 
    public function matieres() { 
        return $this->belongsToMany(Matiere::class); 
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
    * Permet d'encoder le mot de passe
    * @param type $password Le mot de passe
    */
    public function setPasswordAttribute($password) {
        $this->attributes['Password'] = Hash::make($password);
    }
}
