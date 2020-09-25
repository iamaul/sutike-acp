<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;

class PresetModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelia:preset {name : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preset module of the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));
        $this->comment("\n");
        $this->warn('  ========== WARNING!!! ==========');

        if($this->confirm("Are you sure to preset module {$name}?")){
            tap(new Filesystem, function ($files) use ($name) {
                $files->deleteDirectory(app_path('Http/Requests/'.Str::Studly($name)));
                $files->deleteDirectory(app_path('Http/Controllers/'.Str::Studly($name)));
                $files->deleteDirectory(resource_path('views/'.__v().'/'.Str::slug(Str::plural($name))));
                $files->delete(app_path('Models/'.Str::Studly($name).'.php'));
                if(array_key_exists(Str::plural($name), get_json_migrations())) {
                    $files->delete(database_path('migrations/'.get_json_migrations()[Str::plural($name)].'.php'));
                }
            });

            $this->deleteMenu();
            $this->deletePermissions();

            if(array_key_exists(Str::plural($name), get_json_migrations())){
                // $this->info("Please waiting...");
                str_replace(
                    '\\App\\Models\\'. Str::studly(Str::singular($name).'::routes();'),
                    '',
                    file_get_contents(base_path('routes/web.php'))
                );
                // $this->composer->dumpAutoloads();

                $this->info(Str::studly(Str::singular($name)) . ' scaffolding preset succecsfully.');
                $this->comment("\n");
                $this->info('Now, remove \\App\\Models\\'. Str::studly(Str::singular($name)) .'::routes(); on web.php');
                $this->removeJsonObject();
            }
        }
    }

    protected function deleteMenu()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));
        \App\Models\Menu::whereEnName(Str::replaceArray('_', [' '], Str::plural($name)))->delete();
        set_json_menu();
        set_json_user();
    }

    protected function deletePermissions()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));
        \App\Models\Permission::whereIndex(Str::slug(Str::plural($name)))->delete();
        set_json_permissions();
    }

    protected function removeJsonObject()
    {
        $name = Str::plural($name = str_replace('-', '_', trim($this->argument('name'))));
        $json_data = [];
        foreach (get_json_migrations() as $key => $value) 
            if($key != $name) $json_data[$key] = $value;
        set_json_migrations($json_data);
    }
}
