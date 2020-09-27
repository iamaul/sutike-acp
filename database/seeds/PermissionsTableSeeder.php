<?php

use App\Models\User;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // call own public function truncateLaratrustTables()
        $this->truncateLaratrustTables();

        $this->command->comment("\n");
        $this->command->info('----------------------------------------------');
        $this->command->info('============== CREATE PERMISSION =============');
        $this->command->info('----------------------------------------------');
        $this->command->comment("\n");

        // Remove Unused Permission
        $permission_json = array_map(function ($module, $value) {
            return $module;
        }, array_keys(get_json_permissions()), get_json_permissions());
        $unused_permissions = DB::table('permissions')->whereNotIn('index', $permission_json)->get();
        // remove permission_user and permission_role
        DB::table('permission_role')->whereIn('permission_id', $unused_permissions->pluck('id')->toArray())->delete();
        DB::table('permission_user')->whereIn('permission_id', $unused_permissions->pluck('id')->toArray())->delete();
        DB::table('permissions')->whereNotIn('index', $permission_json)->delete();

        // get or update superadmin user
        $role = Role::UpdateOrCreate([
            'name' => get_json_user()['default_role']
        ],[
            'name' => get_json_user()['default_role'],
            'display_name' => strtoupper(get_json_user()['default_role']),
            'description' => 'Role of ' . str_title(get_json_user()['default_role'])
        ]);

        // get unset permission
        $array_permissions = [];
        foreach (get_json_permissions() as $module => $value){
            foreach ($value as $v){
                $array_permissions[] = Permission::UpdateOrCreate([
                    'index' => $module,
                    'name' => $v
                ],[
                    'index' => $module,
                    'name' => $v,
                    'display_name' => str_title($v),
                    'description' => 'Permission of ' . str_title($v),
                ])->id;
            }
        }
        // get all list menu 
        $menus = Menu::select('id as menu_id')
            ->whereIn('en_name', get_json_user_menu())
            ->get()
            ->toArray();
        // set role superadmin for menu
        $role->menus()->sync($menus);
        // set role superadmin for permission
        $role->permissions()->sync($array_permissions);
        // get config/auth.json user superadmin
        $user = User::where('email', get_json_user()['user']['email'])->first();
        // set role superadmin
        if($user) $user->attachRole($role);
        // set new permission if permission empty
        if (!empty($permissions)){
        	$permission_all = Permission::select('id as permission_id')->get()->toArray();
            $user->permissions()->sync($permission_all);
        }
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return    void
     */
    public function truncateLaratrustTables()
    {
        // get config/auth.json user superadmin
        $user = User::where('email', get_json_user()['user']['email'])->first();
        
        // disabled foreign key
        Schema::disableForeignKeyConstraints();
        // remove permission and menu for role superadmin
        if(isset($user->roles[0]->id)){
            DB::table('permission_role')->where('role_id', $user->roles[0]->id)->delete();
            DB::table('menu_role')->where('role_id', $user->roles[0]->id)->delete();
        }
        // remove permission and menu for user superadmin
        if(isset($user->id)){
            DB::table('role_user')->where('user_id', $user->id)->delete();
            DB::table('permission_user')->where('user_id', $user->id)->delete();
        }
        // enabled foreign key
        Schema::enableForeignKeyConstraints();
    }
}
