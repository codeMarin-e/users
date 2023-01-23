<?php
    namespace Marinar\Users\Database\Seeders;

    use App\Models\Package;
    use Illuminate\Database\Seeder;
    use Marinar\Users\MarinarUsers;

    class MarinarUsersInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;
            static::$packageName = 'marinar_users';
            static::$packageDir = MarinarUsers::getPackageMainDir();

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
