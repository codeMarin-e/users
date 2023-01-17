<?php
    namespace Marinar\Users\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Users\MarinarUsers;

    class MarinarUsersCleanStubsSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static $addons = [];

        public function run() {
            static::$addons = config('marinar_users.addons');

            $this->getRefComponents();

            $this->cleanInjects(static::$addons);
            $this->clearFiles();
        }

        private function clearFiles() {
//            if(!$this->command->confirm('Are you sure you want to delete `users` files?', true)) return false;
            $this->refComponents->task("Clear stubs", function() {
                $stubDir = \Marinar\Users\MarinarUsers::getPackageMainDir().DIRECTORY_SEPARATOR.'old_stubs'.DIRECTORY_SEPARATOR.'v0.0.99';
                static::removeStubFiles($stubDir, $stubDir);

                $stubDir = \Marinar\Users\MarinarUsers::getPackageMainDir().DIRECTORY_SEPARATOR.'stubs';
                static::removeStubFiles($stubDir, $stubDir, true);
                return true;
            });
        }
    }
