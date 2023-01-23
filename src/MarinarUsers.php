<?php
    namespace Marinar\Users;

    use Marinar\Users\Database\Seeders\MarinarUsersInstallSeeder;

    class MarinarUsers {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function injects() {
            return MarinarUsersInstallSeeder::class;
        }
    }
