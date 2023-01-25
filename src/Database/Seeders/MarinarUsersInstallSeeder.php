<?php
    namespace Marinar\Users\Database\Seeders;

    use App\Models\Package;
    use Illuminate\Database\Seeder;
    use Marinar\Users\MarinarUsers;

    class MarinarUsersInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_users';
            static::$packageDir = MarinarUsers::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
