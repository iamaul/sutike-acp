<?php

use App\Models\User;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Menu::truncate();

        recursive_menu('PUT', get_json_menu());

        set_json_menu();

        set_json_user();

        $this->command->comment("\n");
        $this->command->info('----------------------------------------------');
        $this->command->info('================= CREATE MENU ================');
        $this->command->info('----------------------------------------------');
        $this->command->comment("\n");
        foreach (Menu::get() as $i => $v):
        	++$i;
        	$this->command->info("$i. " . strtoupper($v->en_name));
        endforeach;
        $this->command->comment("\n");
    }
}