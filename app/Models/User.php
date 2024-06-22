<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $table = 'user';
    protected $primaryKey = 'user_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function getProfilePicUrlAttribute(){
        if($this->attributes['profile_pic_url'] != null){
            return asset($this->attributes['profile_pic_url']);
        }else{
            return null;
        }
    }

    public function userdesignation()
    {
        return $this->hasOne(UserDesignation::class,'user_id', 'user_id');
    }

    public function societymember()
    {
        return $this->hasOne(SocietyMember::class,'user_id', 'user_id');
    }

    public function staffmember()
    {
        return $this->hasOne(StaffMember::class,'user_id', 'user_id');
    }

    public function societymembers()
    {
        return $this->hasMany(SocietyMember::class,'user_id', 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
