<?php

/*
 *  Copyright © All Rights Reserved by Vizrex (Private) Limited 
 *  Usage or redistribution of this code is strictly prohibited
 *  without written consent of Vizrex (Private) Limited.
 *  Queries are welcomed at copyright@vizrex.com
 */

namespace Vizrex\LaratrustIngest\Utilities;

use App\Role;
use App\Permission;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Vizrex\Laraviz\Traits\LaravizModel;

/**
 * Description of PermissionImporter
 *
 * @author Zeshan
 */
class Importer
{

    use LaravizModel;

    const CHUNK_SIZE = 5;

    private $csvFilePath = null;
    private $data = [];

    private function __construct($csvFilePath)
    {
        $this->csvFilePath = $csvFilePath;
    }

    public static function getInstance(string $csvFilePath)
    {
        if (!file_exists($csvFilePath))
        {
            throw new \Vizrex\Exceptions\FileNotFoundException($csvFilePath . " not found!");
        }

        return new Importer($csvFilePath);
    }

    /*
     * This function asumes the csv to be in following format
     * where each column is separated by comma and csv should
     * have following headers:
     * 
     * permission_name,permission_display_name,permission_description,<role_1>,<role_2>,...,<role_n>
     * 
     * Each role should either contain "y" or "n" depending on whether that particular
     * permission should be associated with that role or not
     */

    public function import()
    {
        v_info("Import - Started");
        $data = $this->loadDataFromCSV();
        $this->clearTables();
        $roles = $this->createRoles($data['roles']);
        $rolePermissions = $this->createPermissions($data['permissions']);
        $this->assignPermissions($rolePermissions);
        v_info("Import - Completed");
    }

    private function clearTables()
    {
        $tablesToTruncate = [
            "permission_user",
            "permission_role",
            "permissions",
            "role_user",
            "roles"
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tablesToTruncate as $table)
        {
            v_info("Truncating $table...");
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function createPermissions($permissions)
    {
        $rolePermissions = [];

        foreach ($permissions as $permission)
        {
            $perm = Permission::create(get_fillables(new Permission(), $permission));

            v_debug(PHP_EOL . "Created new permission: "
                . PHP_EOL . "Name:\t" . $perm->name
                . PHP_EOL . "Disp:\t" . $perm->display_name
                . PHP_EOL . "Desc:\t" . $perm->description);

            foreach ($permission['assignments'] as $roleName)
            {
                if (!isset($rolePermissions[$roleName]))
                {
                    $rolePermissions[$roleName] = [];
                }

                array_push($rolePermissions[$roleName], $perm->id);
            }
        }

        return $rolePermissions;
    }

    private function assignPermissions($rolePermissions)
    {
        foreach ($rolePermissions as $roleName => $permissionIds)
        {
            v_info("Assigning " . count($permissionIds) . " Permissions to Role $roleName");
            Role::where("name", $roleName)->first()->syncPermissions($permissionIds);
        }
    }

    private function readRoles($strRoles)
    {
        $roles = [];

        foreach ($strRoles as $strRole)
        {
            $parts = explode(";", $strRole);
            $roles[$strRole] = [
                'name' => $parts[0],
                'display_name' => isset($parts[1]) ? $parts[1] : null,
                'description' => isset($parts[2]) ? $parts[2] : null
            ];
        }

        return $roles;
    }

    private function createRoles($roles)
    {
        foreach ($roles as $role)
        {
            v_debug(PHP_EOL . "Creating new role:"
                . PHP_EOL . "Name:\t" . $role['name']
                . PHP_EOL . "Disp:\t" . $role['display_name']
                . PHP_EOL . "Desc:\t" . $role['description']);

            Role::create($role);
        }
    }

    private function loadDataFromCSV()
    {
        $data = [];
        Excel::load($this->csvFilePath, function($reader) use (&$data)
        {

            // Getting all results
            $results = $reader->get();

            // Get Headings
            $headings = $results->getHeading();
            $totalColumns = count($headings);

            // Parse and Create array of Roles
            $strRoles = array_slice($headings, 3);
            $roles = $this->readRoles($strRoles);

            $permissions = [];
            foreach ($results as $row)
            {
                $permissionName = $row["permission_name"];
                $assignments = [];
                foreach ($strRoles as $strRole)
                {
                    $roleName = $roles[$strRole]['name'];
                    if ($row[$strRole] == "y")
                    {
                        $assignments[] = $roleName;
                    }
                }

                $permissions[$permissionName] = [
                    "name" => $permissionName,
                    "display_name" => $row["permission_display_name"],
                    "description" => $row["permission_description"],
                    "assignments" => $assignments
                ];
            }

            $data["roles"] = $roles;
            $data["permissions"] = $permissions;
        });

        return $data;
    }

}
