<?php

/*
 *  Copyright © All Rights Reserved by Vizrex (Private) Limited 
 *  Usage or redistribution of this code is strictly prohibited
 *  without written consent of Vizrex (Private) Limited.
 *  Queries are welcomed at copyright@vizrex.com
 */

/**
 * Description of LaratrustIngestProvider
 *
 * @author Zeshan
 */

namespace Vizrex\LaratrustIngest;

use Vizrex\Laraviz\BaseServiceProvider;

class LaratrustIngestProvider extends BaseServiceProvider
{
    public function register(){}
    
    public function boot()
    {
        // Commands
        $this->commands(['Vizrex\LaratrustIngest\Console\Commands\UpdateRolesAndPermissions']);
    }
}
