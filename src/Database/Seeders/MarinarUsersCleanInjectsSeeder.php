<?php
    namespace Marinar\Users\Database\Seeders;

    use Illuminate\Database\Seeder;

    class MarinarUsersCleanInjectsSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public $injectClass = MarinarUsersInjectsSeeder::class;

        public function run() {
            $this->getRefComponents();

            $this->clearAddonInjects();
        }

        private function clearAddonInjects() {

            $this->refComponents->task("Clear addon inject - box_sidebar.blade.php", function(){
                $filePath = implode(DIRECTORY_SEPARATOR, [ base_path(), 'resources', 'views', 'components', 'admin', 'box_sidebar.blade.php']);
                if(!realpath($filePath)) return false;
                if(!file_put_contents($filePath,  $this->removeFromContent($filePath, ["<x-admin.sidebar.users_option />"])))
                    return false;
                return true;
            });

            $this->refComponents->task("Clear addon inject - marinar_users.php", function(){
                $filePath = implode(DIRECTORY_SEPARATOR, [ base_path(), 'config','marinar.php']);
                if(!realpath($filePath)) return false;
                if(!file_put_contents($filePath,  $this->removeFromContent($filePath, ["\\Marinar\\Users\\MarinarUsers::class,"])))
                    return false;
                return true;
            });

        }
    }
