<?php
namespace fabien44300\openldap;


use Illuminate\Contracts\Auth\UserProvider as IlluminateUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Config;


class openldapUserProvider  implements IlluminateUserProvider
{

    public function retrieveById($identifier)
    {
        $fieldAuthUser = Config::get('ldap.fieldAuthUser');
	$modelUser =  Config::get('ldap.ldapModel');
        try
        {
            $user = $modelUser::where($fieldAuthUser, $identifier)->first();
            if (!$user)
            {
                $user = new $modelUser();
                $user->$fieldAuthUser = $identifier;
            }
        }
        catch (\Exception $e)
       {
           $user = new $modelUser();
           $user->$fieldAuthUser = $identifier;
       }

        return $user;

    }
    /**
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();
        
        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }
    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        if (Config::get('ldap.updateUserFromLDAP'))
        {
            $user->setRememberToken($token);

            $timestamps = $user->timestamps;

            $user->timestamps = false;

            $user->save();

            $user->timestamps = $timestamps;
        }

    }
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        $openldapConnection= new openldapConnection();
        $user = $openldapConnection->authenticate($credentials[Config::get('ldap.fieldAuthUser')], $credentials['password']);
        return $user;
    }
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $fieldAuthUser = Config::get('ldap.fieldAuthUser');

        if($user->$fieldAuthUser == $credentials[$fieldAuthUser])
        {
            return true ;
        }

        return false;

    }

    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

}
