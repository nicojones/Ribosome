<?php

    DEFINE ('FW_UPDATE_HOST', 'http://kupfer.es/core');
    DEFINE ('FW_UPDATE_ROUTE', '/_versioncheck');
    DEFINE ('FW_UPDATEGET_ROUTE', '/_versionget');
    DEFINE ('FW_TOKEN', 'fwRW7w3i8aHg4N315cCGgKi5mqCc3E');
    DEFINE ('FW_VERSION', getVersion());

    /**
     * @return string
     */
    function getVersion() {
        if (!file_exists(__ROOT__ . '/app/bootload/CHANGELOG.txt')) {
            return '0.0.0';
        }
        $changelog = file_get_contents(__ROOT__ . '/app/bootload/CHANGELOG.txt');
        preg_match("/\[([\d\.]+)\]/", $changelog, $matches);
        return $matches[1];
    }

    /**
     * @param $assoc_arr
     * @param $path
     * @param bool $has_sections
     *
     * @return bool
     */
    function write_ini($assoc_arr, $path, $has_sections = FALSE) {
        $content = "";

        if ($has_sections) {
            foreach ($assoc_arr as $key => $elem) {
                $content .= "[" . $key . "]\n";
                foreach ($elem as $key2 => $elem2) {
                    if(is_array($elem2)) {
                        for( $i = 0; $i < count($elem2); ++$i) {
                            $content .= $key2 . "[] = \"" . $elem2[$i]."\"\n";
                        }
                    }
                    elseif ($elem2=="") {
                        $content .= $key2." = \"\"\n";
                    } else {
                        $content .= $key2." = \"".$elem2."\"\n";
                    }
                }
                $content .= "\n";
            }
        } else {
            foreach ($assoc_arr as $key2 => $elem2) {
                if(is_array($elem2)) {
                    for( $i = 0; $i < count($elem2); ++$i) {
                        $content .= "    " . $key2 . "[] = \"" . $elem2[$i]."\"\n";
                    }
                }
                elseif ($elem2=="") {
                    $content .= $key2." = \"\"\n";
                } else {
                    $content .= $key2." = \"".$elem2."\"\n";
                }
            }
        }
        $handle = NULL;
        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        if (!fwrite($handle, $content)) {
            return false;
        }
        fclose($handle);
        return true;
    }

    /**
     * @param $db
     * @param $query
     * @param array $params
     * @param bool $fetchAll
     *
     * @return mixed
     */
    function query($db, $query, $params = array(), $fetchAll = FALSE) {
        $res = $db->prepare($query);
        $res->execute($params);
        if ($fetchAll) {
            return $res->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $res;
        }
    }

    /**
     * @param $db
     * @param $query
     * @param array $params
     * @param bool $unique
     *
     * @return array
     */
    function queryIndexed($db, $query, $params = array(), $unique = TRUE) {
        $res = $db->prepare($query);
        $res->execute($params);
        $rows = $res->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        if ($unique) {
            return array_map('reset', $rows);
        } else {
            return $rows;
        }
    }

    /**
     * @param $path
     */
    function asset($path) {
        $extension = [
            'css' => 'text/css',
            'js' => 'text/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpg',
            'gif' => 'image/gif'
        ];
        header('Content-type: ' . $extension[array_pop(explode('.', $path))]);
        readfile('assets/' . $path);
        die();
    }

    /**
     * @param $path
     */
    function versioning($path) {
        header('Content-type: text/plain');
        readfile($path);
        die();
    }

    /**
     * Copy or move a folder recursively
     * This function is also available at support_functions as folder_recurse()
     *
     * @param string $action The action to perform: copy (default) or rename (= move)
     * @param string $dst The destination folder
     * @param string $src The source folder
     */
    function folder_action($action, $dst, $src = '') {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    folder_action($action, $dst . '/' . $file, $src . '/' . $file);
                }
                else {
                    switch ($action) {
                        case 'unlink':
                            unlink($dst . '/' . $file);
                            break;
                        default:
                            $action($src . '/' . $file, $dst . '/' . $file);
                            chmod($dst . '/'. $file, 0777);
                            break;
                    }
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param $var
     * @param bool $die
     */
    function _die($var, $die = true) {
        echo "<pre>", var_export($var, true), "</pre>";
        if ($die) die;
    }