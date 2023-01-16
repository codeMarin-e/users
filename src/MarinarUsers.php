<?php
    namespace Marinar\Users;

    use Marinar\Users\Database\Seeders\MarinarUsersCleanInjectsSeeder;
    use Marinar\Users\Database\Seeders\MarinarUsersInjectsSeeder;

    class MarinarUsers {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function cleanInjects() {
            return MarinarUsersCleanInjectsSeeder::class;
        }

        public static function injects() {
            return MarinarUsersInjectsSeeder::class;
        }
    }
