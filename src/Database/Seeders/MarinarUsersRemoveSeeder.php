<?php
    namespace Marinar\Users\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Spatie\Permission\Models\Permission;

    class MarinarUsersRemoveSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public function run() {
            $this->getRefComponents();
            $this->clearDB();
            $this->call([
                \Marinar\Users\Database\Seeders\MarinarUsersCleanInjectsSeeder::class,
                \Marinar\Users\Database\Seeders\MarinarUsersCleanStubsSeeder::class,
            ]);

            $this->refComponents->info("Done!");
        }

        public function clearDB() {
            $this->refComponents->task("Clear DB", function() {
                Permission::whereIn('name', [
                    'users.view',
                    'user.create',
                    'user.view',
                    'user.update',
                    'user.delete',
                ])
                ->where('guard_name', 'admin')
                ->delete();
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                return true;
            });
        }
    }
