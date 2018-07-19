<?php

/*
 *  Copyright © All Rights Reserved by Vizrex (Private) Limited 
 *  Usage or redistribution of this code is strictly prohibited
 *  without written consent of Vizrex (Private) Limited.
 *  Queries are welcomed at copyright@vizrex.com
 */

namespace Vizrex\LaratrustIngest\Console\Commands;

use Vizrex\Laraviz\Console\Commands\BaseCommand;
use Vizrex\LaratrustIngest\Utilities\Importer;

class UpdateRolesAndPermissions extends BaseCommand
{
    private $csvFilePath = null;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles-and-permissions:update {csvFilePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update roles and permissions from CSV to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->csvFilePath = $this->argument("csvFilePath");
        Importer::getInstance($this->csvFilePath)->import();
    }

    protected function setNamespace()
    {
        $this->namespace = \Vizrex\LaratrustIngest\LaratrustIngestProvider::getNamespace();
    }

}
