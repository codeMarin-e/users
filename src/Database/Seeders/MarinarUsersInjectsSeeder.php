<?php
    namespace Marinar\Users\Database\Seeders;

    use Illuminate\Database\Seeder;

    class MarinarUsersInjectsSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static $alreadyInjectedInLine = [];

        public function run() {
            $this->getRefComponents();

            $this->injectAddon();
        }

        private function injectAddon() {

            $this->refComponents->task("Inject addon - box_sidebar.blade.php", function(){
                $filePath = implode(DIRECTORY_SEPARATOR, [ base_path(), 'resources', 'views', 'components', 'admin', 'box_sidebar.blade.php']);
                if(!realpath($filePath)) return false;

                if(!file_put_contents($filePath, $this->putBeforeInContent(
                    $filePath, "{{--  @HOOK_ADMIN_SIDEBAR  --}}", "\t<x-admin.sidebar.users_option />\n"
                ))) return false;
                return true;
            });

            $this->refComponents->task("Inject addon - marinar.php", function(){
                $filePath = implode(DIRECTORY_SEPARATOR, [ base_path(), 'config','marinar.php']);
                if(!realpath($filePath)) return false;
                if(!file_put_contents($filePath, $this->putBeforeInContent(
                    $filePath, "// @HOOK_MARINAR_CONFIG_ADDONS", "\t\t\\Marinar\\Users\\MarinarUsers::class, \n"
                ))) return false;
                return true;
            });

        }

    }
