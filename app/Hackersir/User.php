<?php

namespace Hackersir;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * Hackersir\User
 *
 * @property integer $id
 * @property string $nid
 * @property string $email
 * @property string $nickname
 * @property string $comment
 * @property string $password
 * @property string $remember_token
 * @property string $confirm_code
 * @property string $confirm_at
 * @property string $register_ip
 * @property string $register_at
 * @property string $lastlogin_ip
 * @property string $lastlogin_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\VoteBallot[] $voteBallots
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\Role[] $roles
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereNid($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereNickname($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereConfirmCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereConfirmAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereRegisterIp($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereRegisterAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereLastloginIp($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereLastloginAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
    protected $fillable = [
        'email',
        'nickname',
        'comment',
        'password',
        'confirm_code',
        'confirm_at',
        'register_ip',
        'register_at',
        'lastlogin_ip',
        'lastlogin_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function isConfirmed()
    {
        if (!empty($this->confirm_at)) {
            return true;
        }

        return false;
    }

    public function isStaff()
    {
        if ($this->isAdmin() || $this->hasRole('staff')) {
            return true;
        }

        return false;
    }

    public function isAdmin()
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return false;
    }

    public function getNickname()
    {
        if (!empty($this->nickname)) {
            $nickname = $this->nickname;
        } else {
            $nickname = explode('@', $this->email)[0];
        }

        return $nickname;
    }

    public function voteBallots()
    {
        return $this->hasMany(VoteBallot::class);
    }
}
