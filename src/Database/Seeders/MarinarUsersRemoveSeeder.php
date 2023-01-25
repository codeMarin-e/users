<?php
    namespace Marinar\Users\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Users\MarinarUsers;
    use Spatie\Permission\Models\Permission;

    class MarinarUsersRemoveSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_users';
            static::$packageDir = MarinarUsers::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoRemove();

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
