<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    use EntrustUserTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'password', 'confirm_code', 'confirm_at', 'register_ip', 'register_at', 'lastlogin_ip', 'lastlogin_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function isConfirmed()
    {
        if (!empty($this->confirm_at)) {
            return true;
        }
        return false;
    }

    public function isStaff()
    {
        if ($this->isAdmin() || $this->group->name == "staff") {
            return true;
        }
        return false;
    }

    public function isAdmin()
    {
        if ($this->group->name == "admin") {
            return true;
        }
        return false;
    }

    public function getNickname()
    {
        if (!empty($this->nid)) {
            $nickname = $this->nid;
        } else {
            $nickname = explode("@", $this->email)[0];
        }
        return $nickname;
    }

    public function voteBallots()
    {
        return $this->hasMany('App\VoteBallot');
    }
}
