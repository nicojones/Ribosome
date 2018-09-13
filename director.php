<?php

    define ('__ROOT__', __DIR__);
    define ('FILE_EXISTS', "The class %s exists. Please delete it first if you want to re-generate it.\n");
    define ('UNKNOWN_ACTION', "Sorry, the required action doesn't exist.\nTry 'help' for a list of available commands\n\n");
    define ('EMPTY_CLASS_NAME', "You must specify the class name, i.e. make:controller Product (would generate ProductController)\n");
    define ('HELP', "\nGenerate a 'controller', 'model' or 'both' by typing: `generate:<what> <name>`. \n" .
                    "\nExample:\n\ngenerate:controller User ----> will create UserController.php\n".
                    "generate:both Checklist  ----> will create ChecklistController.php + ChecklistModel.php\n\n");

    $option = $argv[1];

    switch ($option) {
        case 'help':
            die (HELP);
            break;

        default:
            if (strpos($option, ':') !== FALSE) {
                list($action, $object) = explode(':', $option);
                switch ($action) {
                    case 'generate':
                        if (empty($argv[2])) {
                            die(EMPTY_CLASS_NAME);
                        }
                        $name = $argv[2];
                        $vendor = FALSE;
                        $object = strtolower($object);
                        switch($object) {
                            case 'controller':
                            case 'model':
                                generateFile($object, $name, false);
                                break;

                            case 'both':
                                generateFile('controller', $name, false);
                                generateFile('model', $name, false);
                                break;

                            default:
                                die(UNKNOWN_ACTION);

                        }
                        break;

                    default:
                        die(UNKNOWN_ACTION);
                        break;

                }
            } else {
                die(UNKNOWN_ACTION);
            }
    }

    function generateFile($unparsedType, $name, $vendor) {
        $type = strtolower($unparsedType);
        $file = __ROOT__ . '/src/' . $type . 's/' . $name . ucfirst($type) . '.php';

        if (file_exists($file)) {
            echo sprintf(FILE_EXISTS, $name . ucfirst($type) . '.php');
            die();
        }

        echo "Creating " . $type . " . . .\n";
        $$type = TRUE; // i.e. $controller = TRUE;

        ob_start();
        include __ROOT__ . '/app/bootload/blocks/generate_file.php';
        $fileContents = ob_get_clean();

        file_put_contents(__ROOT__ . '/src/' . $type . 's/' . $name . ucfirst($type) . '.php', $fileContents);

        echo ucfirst($type) , " created!\n";

        return $fileContents;
    }