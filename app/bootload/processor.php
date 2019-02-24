<?php

    switch ($_GET['action']) {
        // Save the values of an INI section
        case 'save_ini':
            $postINI = $_POST;
            $config_INI[$_GET['ini_section']] = $_POST;
            try {
                write_ini($config_INI, __DIR__ . '/../config/config.ini', TRUE);
            } catch (Exception $e) {
                die(json_encode(['success' => 0, 'responseData' => ['message' => 'Cannot write on "config.ini"']]));
            }
            die(json_encode(['success' => 1, 'responseData' => ['message' => 'Section "' . $_GET['ini_section'] . '" saved']]));
            break;

        // Refresh cache for Vendors (Includes, Routing and Permissions)
        case 'vendor-cache-refresh':
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/_vendor/cache?auth=8d0a49297f36ed144194ce447db3b4f72399b913';
            $response = file_get_contents($url);

            die($response); // it's a JSON object
            break;

        // Run framework diagnostics (database, cache, (non-)writable files...)
        case 'run-diagnosis':
            $config_db = $config_INI['Database'];
            $cacheFolderWritable = is_writable(__ROOT__ . '/app/cache');
            $configFileWritable = is_writable(__ROOT__ . '/app/config/config.ini');
            $versionWritable = is_writable(__ROOT__ . '/app/bootload');
            $appWritable = is_writable(__ROOT__ . '/app');
            $PDO_Supported = class_exists('PDO');
            $shell_execSupported = function_exists('shell_exec');
            $webappDatabaseEnabled = $config_db['DB_SUPPORT'] == '1';
            $webappConnexionParamsOK = TRUE;
            if ($webappDatabaseEnabled) {
                try {
                    new PDO ('mysql:host='.$config_db['HOST'].';dbname='.$config_db['DB_NAME'].';charset=utf8', $config_db['USERNAME'], $config_db['PASSWORD']);
                } catch (Exception $e) {
                    $webappConnexionParamsOK = FALSE;
                }
            }
            $vendorsEnabled = $config_INI['Vendor']['VENDOR_ENABLED'];
            $vendorsFolder = $config_INI['Vendor']['VENDOR_FOLDER'];

            ob_start();
            include 'blocks/diagnosis.php';
            $content = ob_get_clean();
            die(json_encode([
                'success' => 1,
                'responseData' => [
                    'message' => 'Diagnosis completed',
                    'content' => $content]]));
            break;

        case 'check-updates':

            $ch = curl_init(FW_UPDATE_HOST . FW_UPDATE_ROUTE);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'version=' . FW_VERSION . '&token=' . FW_TOKEN);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $rawResponse = curl_exec($ch);
            curl_close($ch);

            try {
                $res = json_decode($rawResponse);
            } catch (Exception $e) {
                die(json_encode([
                    'success' => 1,
                    'responseData' => [
                        'message' => 'Error on receiving a JSON string.',
                        'content' => '<pre>' . var_export($rawResponse, TRUE) . '</pre>']]));
            }

            $version = $res->version;
            $latest = array_pop($res->versions);
            ob_start();
            $checking = true;
            include 'blocks/check-versions.php';
            $content = ob_get_clean();
            die(json_encode([
                'success' => 1,
                'responseData' => [
                    'message' => FW_VERSION == $latest ? 'Your framework is up to date' : 'Updates available',
                    'content' => $content]]));
            break;

        case 'update-framework':
            $version = $_GET['version'];

            // checking that everything is in order to start
            $folders = array('controllers', 'bootload', 'Kernel', 'models');
            $errors = [];
            if (!is_writable(sys_get_temp_dir())) {
                $errors[] = "The /tmp folder <b>" . sys_get_temp_dir() . "</b> is not writable";
            }
            foreach ($folders as $f) {
                $destDir = realpath(__ROOT__ . '/app/' . $f);
                if (!file_exists($destDir)){
                    $errors[] = "$destDir does not exist";
                }
                if (!is_dir($destDir)){
                    $errors[] = "$destDir is not a directory!";
                }
                if (!is_writable($destDir)) {
                    $errors[] = "<p style='color:red'>" . __ROOT__ . '/app/' . "<b>$f</b> is not writable!</p>";
                }
            }
            if ($errors) {
                die(json_encode([
                    'success' => 0,
                    'responseData' => [
                        'message' => 'Some errors were found',
                        'content' => implode("<br/>", $errors)]]));
            }

            // We get and extract the zip file
            $zipContent = file_get_contents(FW_UPDATE_HOST . FW_UPDATEGET_ROUTE . '?token=' . FW_TOKEN . '&version=' . $version);
            $tmpFile = sys_get_temp_dir() . '/fw.zip';
            file_put_contents($tmpFile, $zipContent);
            $zip = new ZipArchive();
            if (!$zip->open($tmpFile)) {
                die(json_encode([
                    'success' => 0,
                    'responseData' => [
                        'message' => 'Could not open ZIP...']]));
            }
            $zip->extractTo(sys_get_temp_dir());

            $srcDir = sys_get_temp_dir() . '/app';
            $destDir = realpath(__ROOT__ . '/app');
//            shell_exec("chmod -Rf 777 $destDir");
//            shell_exec("chmod -Rf 777 $destDir/*");

            foreach ($folders as $f) {
                $src = $srcDir . '/' . $f;
                $dest = $destDir . '/' . $f;
                if (!is_dir($src)) {
                    die(json_encode([
                        'success' => 0,
                        'responseData' => [
                            'message' => $src . ' is not a folder']]));
                }
                if (!is_writable($dest)) {
                    die(json_encode([
                        'success' => 0,
                        'responseData' => [
                            'message' => $dest . ' is not writable']]));
                }
                if (!file_exists($dest)) {
                    die($dest . ' does not exist');
                }
//                shell_exec("shopt -s dotglob nullglob");
//                shell_exec("rm -Rf $dest/*");
//                shell_exec("mv $src/* $dest");
                folder_action('unlink', $dest, $dest);
                folder_action('rename', $dest, $src);
            }
            rename($srcDir  . '/../app.php',  $destDir . '/../app.php');
            chmod($destDir . '/../app.php', 0777);
            rename($srcDir  . '/../.htaccess', $destDir . '/../.htaccess');
            chmod($destDir . '/../.htaccess', 0777);
            $zip->close();
//            shell_exec('chmod -Rf 777 ' . __ROOT__ . '/app/*');

            $changelog = file_get_contents(FW_UPDATE_HOST . '/version/v-' . $version . '/CHANGELOG.txt');
            file_put_contents($destDir . '/bootload/CHANGELOG.txt', $changelog);

            // We write the response
            ob_start();
            $checking = false;
            include 'blocks/check-versions.php';
            $content = ob_get_clean();
            die(json_encode([
                'success' => 1,
                'responseData' => [
                    'message' => 'Done',
                    'content' => $content]]));
            break;

        case 'configure-user-login-table':
            if (!$connectionParamsOK) {
                die(json_encode([
                    'success' => 0,
                    'responseData' => [
                        'message' => 'Database parameters are not valid'
                    ]]));
            }
            $configLogin = $config_INI['Login'];
            $query = "
                        SET NAMES utf8;
                        SET FOREIGN_KEY_CHECKS = 0;

                        CREATE TABLE `${configLogin['TABLE']}` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `${configLogin['TABLE_COLUMN_USER']}` varchar(255) NOT NULL,
                          `${configLogin['TABLE_COLUMN_PASS']}` varchar(255) NOT NULL,
                          `added_on` datetime NOT NULL,
                          `attempts` int(11) NOT NULL,
                          PRIMARY KEY (`id`,`${configLogin['TABLE_COLUMN_USER']}`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

                        SET FOREIGN_KEY_CHECKS = 1;";
            query($db, $query);
            $tables = array_keys(queryIndexed($db, 'show tables', [], false));
            die(json_encode([
                'success' => (int)in_array($configLogin['TABLE'], $tables),
                'responseData' => [
                    'message' => '',
                    'content' => '<code>' . implode('</code>&nbsp;<code>', $tables) . '</code>'
                ]]));
            break;

        case 'configure-new-table':
            if (!$connectionParamsOK) {
                die(json_encode([
                    'success' => 0,
                    'responseData' => [
                        'message' => 'Database parameters are not valid'
                    ]]));
            }

            $table = $_POST;
            $primaryKeys = [];
            $tableFields = [];

            foreach ($table['fields'] as $f) {
                $field = "`${f['col_name']}` ${f['col_type']} NOT NULL ";
                if ($f['col_autoincrement'] == '1') {
                    $field .= " AUTO_INCREMENT ";
                }
                if ($f['col_key'] == 1) {
                    $primaryKeys[] = "`" . $f['col_name'] . "`";
                }
                $tableFields[] = $field;
            }

            $query = "
                SET NAMES utf8;
                SET FOREIGN_KEY_CHECKS = 0;

                CREATE TABLE `${table['name']}` ( " .
                implode(",\n", $tableFields) .
                ', PRIMARY KEY (' . implode(', ', $primaryKeys) . ') ' .
                ') ENGINE=InnoDB DEFAULT CHARSET=latin1;
                SET FOREIGN_KEY_CHECKS = 1;';

            query($db, $query);
            $tables = array_keys(queryIndexed($db, 'show tables', [], false));
            die(json_encode([
                'success' => (int)in_array($table['name'], $tables),
                'responseData' => [
                    'message' => '',
                    'content' => '<code>' . implode('</code>&nbsp;<code>', $tables) . '</code>',
                    'query' => $query
                ]]));
            break;

        case 'raw-query':
            $query = $_POST['query'];
            $result = query($db, $query, [], TRUE);
            $content = "";
            if ($result) {
                $content = "<table class='table table-responsive -table-bordered'><thead><tr>";
                foreach (array_keys($result[0]) as $k) {
                    $content .= "<td style='font-weight:bold;'>$k</td>";
                }
                $content .= "</tr></thead><tbody>";
                foreach ($result as $r) {
                    $content .= "<tr>";
                    foreach ($r as $row) {
                        $content .= "<td>" . $row . "</td>";
                    }
                    $content .= "</tr>";
                }
                $content .= "</tbody></table>";
            }
            die(json_encode([
                'success' => 1,
                'responseData' => [
                    'message' => "Query executed!",
                    'content' => $content
            ]]));
            break;

        case 'generate-class':
            $name = $_POST['name'];
            $model = isset($_POST['model']);
            $vendor = isset($_POST['vendor']);

            ob_start();
            $controller = TRUE;
            include 'blocks/generate_file.php';
            $result = ob_get_clean();
            ///////////////
            ob_start();
            $controller = FALSE;
            include 'blocks/generate_file.php';
            $result2 = ob_get_clean();

            die(json_encode([
                'success' => 1,
                'responseData' => [
                    'controller' => $result,
                    'controllerName' => $name . 'Controller',
                    'model' => $result2 ?: "Not requested",
                    'modelName' => $name . 'Model'
                ]
            ]));
            break;

        default:
            die(json_encode(['success' => 0, 'responseData' => ['message' => "Action not configured"]]));
            break;
    }