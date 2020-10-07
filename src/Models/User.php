<?php

namespace fabien44300\openldap\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
Use Config;

class User extends Authenticatable
{
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'login', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAuthIdentifier()
    {
        $fieldAuthUser = Config::get('ldap.fieldAuthUser');
        return $this->$fieldAuthUser;
    }


    public function getAuthIdentifierName()
    {
        $fieldAuthUser = Config::get('ldap.fieldAuthUser');
        return $this->$fieldAuthUser;
    }
}
