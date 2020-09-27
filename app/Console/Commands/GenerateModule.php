<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Console\DetectsApplicationNamespace;

class GenerateModule extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelia:module {name : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate module of the application';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'example.stub' => 'index.blade.php',
        'action.stub' => 'datatables/action.blade.php',
    ];

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
     * @author ken <wahyu.dhiraashandy8@gmail.com>
     * @since @version 0.1
     * @return mixed
     */
    public function handle()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        $this->createDirectories();
        $this->exportViews();

        file_put_contents(
            app_path('Http/Controllers/'.Str::studly(Str::singular($name)).'/'. Str::studly(Str::singular($name)) .'Controller.php'),
            $this->compileControllerStub()
        );

        file_put_contents(
            app_path('Http/Requests/'.Str::studly(Str::singular($name)).'/Request.php'),
            $this->compileRequestStub()
        );

        file_put_contents(
            app_path('Models/'. Str::studly(Str::singular($name)) .'.php'),
            $this->compileModelStub()
        );

        $table = Str::plural($name);
        $date = date('Y_m_d_His');

        file_put_contents(
            database_path('migrations/'. $date . '_' . "create_{$table}_table" .'.php'),
            $this->compileMigrationStub()
        );

        $json_migrations = get_json_migrations();
        $json_migrations[Str::plural($name)] = "{$date}_create_{$table}_table";
        set_json_migrations($json_migrations);

        $this->addMenu();
        $this->permissionGenerate();
        $this->callSilent('cache:clear');
        // $this->composer->dumpAutoloads();
        
        $this->info(Str::studly(Str::singular($name)) . ' scaffolding generated successfully.');
        $this->comment("\n");
        $this->info('Now, add \\App\\Models\\'. Str::studly(Str::singular($name)) .'::routes(); on web.php');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        if (! is_dir($views_directory = resource_path('views/'.__v().'/'.Str::slug(Str::plural($name))))) {
            mkdir($views_directory, 0755, true);
        }

        if (! is_dir($datatables = resource_path('views/'.__v().'/'.Str::slug(Str::plural($name)).'/datatables'))) {
            mkdir($datatables, 0755, true);
        }

        if (! is_dir($controllers_directory = app_path('Http/Controllers/'.Str::studly(Str::singular($name))))) {
            mkdir($controllers_directory, 0755, true);
        }

        if (! is_dir($requests_directory = app_path('Http/Requests/'.Str::studly(Str::singular($name))))) {
            mkdir($requests_directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        foreach ($this->views as $key => $value) {
            $v = __v().'/'.Str::slug(Str::plural($name)).'/'.$value;
            if (file_exists($view = resource_path('views/'.$v))) {
                if (! $this->confirm("The [{$v}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            file_put_contents(
                resource_path('views/'.$v),
                str_replace(
                    ['{{DummyClass}}', '{{DummyTable}}'],
                    [Str::studly(Str::plural($name)), Str::slug(Str::plural($name))],
                    file_get_contents(__DIR__.'/../stubs/make/views/'.$key)
                )
            );
        }
    }

    /**
     * Compiles the Controller stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        return str_replace(
            ['{{DummyNamespace}}', '{{DummyClass}}', '{{property}}', '{{dir}}'],
            [$this->getAppNamespace(), Str::studly(Str::singular($name)), Str::plural($name), Str::slug(Str::plural($name))],
            file_get_contents(__DIR__.'/../stubs/make/controllers/Controller.stub')
        );
    }

    /**
     * Compiles the Request stub.
     *
     * @return string
     */
    protected function compileRequestStub()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        return str_replace(
            ['{{DummyNamespace}}', '{{DummyClass}}', '{{property}}', '{{DummyTable}}', '{{Table}}'],
            [$this->getAppNamespace(), Str::studly(Str::singular($name)), Str::studly(Str::plural($name)), Str::lower(Str::singular($name)), Str::plural($name)],
            file_get_contents(__DIR__.'/../stubs/make/requests/Request.stub')
        );
    }

    /**
     * Compiles the Model stub.
     *
     * @return string
     */
    protected function compileModelStub()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        return str_replace(
            ['{{DummyNamespace}}', '{{DummyClass}}', '{{property}}', '{{uri}}'],
            [$this->getAppNamespace(), Str::studly(Str::singular($name)), Str::plural($name), Str::slug(Str::plural($name))],
            file_get_contents(__DIR__.'/../stubs/make/models/Model.stub')
        );
    }

    /**
     * Compiles the Model stub.
     *
     * @return string
     */
    protected function compileMigrationStub()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));

        return str_replace(
            ['DummyClass', 'DummyTable'],
            ['Create'.Str::studly(Str::plural($name)).'Table', Str::plural($name)],
            file_get_contents(__DIR__.'/../stubs/make/migrations/migration.stub')
        );
    }

    /**
     * Generate all permissions for module
     *
     * @return mixed
     */
    
    protected function permissionGenerate()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));
        $role = \App\Models\Role::first();
        $array_permissions = [];
        foreach (explode(',', "i,c,sh,st,e,u,d,o") as $p => $perm) {
            $permissionValue = collect(config('laravelia.permissions_maps'))->get($perm);
            $array_permissions[] = \App\Models\Permission::firstOrCreate([
                'index' => Str::slug(Str::plural($name)),
                'name' => $permissionValue . '-' . Str::slug(Str::plural($name)),
                'display_name' => Str::title($permissionValue . ' ' . Str::slug(Str::plural($name))),
                'description' => 'Permission of ' . Str::title($permissionValue . ' ' . Str::slug(Str::plural($name))),
            ])->id;
        }
        $menus = \App\Models\Menu::select('id as menu_id')->get()->toArray();
        $permissions = \App\Models\Permission::select('id as permission_id')->get()->toArray();
        $role->menus()->toggle($menus);
        $role->permissions()->toggle($permissions);

        set_json_permissions();
    }

    /**
     * Add new menu
     *
     * @return mixed
     */
    protected function addMenu()
    {
        $name = str_replace('-', '_', trim($this->argument('name')));
        $queued = \App\Models\Menu::max('queue');

        \App\Models\Menu::create(
            [
                'id' => Str::orderedUuid()->toString(),
                'parent' => null,
                'queue' => intVal($queued) + 1,
                'en_name' => Str::replaceArray('_', [' '], Str::plural($name)),
                'id_name' => Str::replaceArray('_', [' '], Str::plural($name)),
                'icon' => 'fa fa-windows',
                'route' => Str::slug(Str::plural($name)),
            ]
        );

        set_json_menu();
        set_json_user();
    }
}
