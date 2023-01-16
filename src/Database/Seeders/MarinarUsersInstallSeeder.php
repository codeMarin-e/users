<?php
    namespace Marinar\Users\Database\Seeders;

    use App\Models\Package;
    use Illuminate\Database\Seeder;

    class MarinarUsersInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static $addons = [];

        public function run() {
            $this->getRefComponents();
            $this->stubFiles();
            $this->seedMe();

            $this->call([
                \Marinar\Users\Database\Seeders\MarinarUsersCleanInjectsSeeder::class,
                \Marinar\Users\Database\Seeders\MarinarUsersInjectsSeeder::class,
            ]);
            $this->giveGitPermissions();

            $this->refComponents->info("Done!");
        }

        private function stubFiles() {
            if(!realpath(base_path().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'marinar_users.php')) {
                return $this->copyStubs(\Marinar\Users\MarinarUsers::getPackageMainDir().DIRECTORY_SEPARATOR.'stubs');
            }

            //clean for updates

            static::$addons = config('marinar_users.addons')?? []; //because cleaning stubs clean marinar_users.addons
            $this->call([
                \Marinar\Users\Database\Seeders\MarinarUsersCleanStubsSeeder::class
            ]);

            $this->copyStubs(\Marinar\Users\MarinarUsers::getPackageMainDir().DIRECTORY_SEPARATOR.'stubs');

            //inject addons again
            foreach(static::$addons as $addonClass) {
                if(!method_exists($addonClass, 'injects')) continue;
                $this->call( $addonClass::injects() );
            }
        }

        private function seedMe() {
            $command = Package::replaceEnvCommand('php artisan db:seed --class="\\Database\\Seeders\\Packages\\Users\\MarinarUsersSeeder"');
            $this->refComponents->task("Seeding DB [$command]", function() use ($command){
                return $this->execCommand($command);
            });
        }

        private function giveGitPermissions() {
            $packageVendorDir = \Marinar\Users\MarinarUsers::getPackageMainDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.git';
            $command = Package::replaceEnvCommand("chmod -R 777 {$packageVendorDir}");
            $this->refComponents->task("Seeding DB [$command]", function() use ($command){
                return $this->execCommand($command);
            });
        }

    }
