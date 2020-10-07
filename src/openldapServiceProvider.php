<?php

namespace fabien44300\openldap;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;


class openldapServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $this->publishes([__DIR__.'/../config/ldap.php' => config_path('ldap.php'),], 'config');
        $modifyUserTable = 'openldap_modify_users_table.php';
        $usersMigration = __DIR__.'/../migrations/'.$modifyUserTable;
        $this->publishes([
            $usersMigration => $this->app['path.database'].'/migrations/'. date('Y_m_d_His_').$modifyUserTable,
        ]);

        Auth::provider('ldap', function($app, array $config) {
            return new openldapUserProvider();
        });

    }

}