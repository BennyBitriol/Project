<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
  
class Users extends Authenticatable implements JWTSubject
{
    use HasFactory,Notifiable;
  
    protected $fillable = [
        'name', 
        'email' , 
        'email_verified_at' , 
        'password' , 
        'remember_token' , 
        'created_at' , 
        'updated_at',
        'line_id',
        'line_avatar',
        'line_name',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
