<?php

    /**
     * &lt;pre&gt;var_dump($var);&lt;/pre&gt; die();
     * @param mixed $var
     */
    function ddie($var = NULL) {
        echo '<pre>';
        var_dump($var);
        die();
    }

    /**
     * print_r($var); die();
     */
    function ppie($var = NULL) {
        print_r($var);
        die();
    }

    /**
     * Prints PHP code inside a tag, for further execution.
     * @param string $php
     * @param bool $return = FALSE
     * @example <code>php("echo 'Copyright ', date('Y')")</code> will echo <code><?php echo 'Copyright ', date('Y')?></code> and php will parse it.
     * @return string the $php code inside &lt;?php ?&gt; tags
     */
    function php($php, $return = FALSE) {
        if (!$return) {
           echo '<?php ' . $php . ' ?>';
        } else {
           return '<?php ' . $php . ' ?>';
        }
    }

    /**
     * All <b>Fatal Error</b>s enter this function automatically.
     * Hence we use this to register/catch a Fatal Error and log it (<b>\Kernel\Logger</b>).
     */
    register_shutdown_function(function() {
        require_once __ROOT__ . '/app/Kernel/Dispatch/Logger.php';
        $error = error_get_last();

        if($error !== NULL) {
            \Kernel\Dispatch\Logger::error($error);
        }
    });

    /**
     * spl_autoload_register is automatically called when PHP can't find a specified class.
     * This scans some folders and includes the aforementioned file.
     */
    spl_autoload_register(function($className) {
        $exploded = explode('\\', $className);

        // We get the namespace... if it has one
        if(count($exploded) == 1) {
            $namespace = "";
            $name = $exploded[0];
        } else {
            list($namespace, $name) = $exploded;
        }

        // local redeclaration; it's faster
        $root = __ROOT__;

        if ($namespace) {
            if (file_exists($req = $root . '/app/Kernel/' . $name . '.php')) {
                // ...
            } elseif (file_exists($req = $root . '/app/controllers/' . $name . '.php')) {
                // ...
            } elseif (file_exists($req = $root . '/app/models/' . $name . '.php')) {
                // ...
            } else {
                return false;
            }
        } else {
            if (file_exists($req = $root . '/src/controllers/' . $name . '.php')) {
                // ...
            } elseif (file_exists($req = $root . '/src/models/' . $name . '.php')) {
                // ...
            } else {
                return false;
            }
        }
        // Require the file, if any
        require_once $req;
        return TRUE;
    });

    /**
     * Returns an image of the <b>\$text</b>, to avoid spam.
     * @param string $text The text to convert to image
     * @param array|[175,20] $size The size of the image
     * @param array|[50,50,50] $color The RGB values of the text color
     */
    function email_png($text, $size = array(175, 20), $color = array(50, 50, 50)) {
        header('Content-type: image/png');

        $im = imagecreate($size[0], $size[1]);
        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
        imagestring($im, 5, 0, 0, $text, $color);
        // Using imagepng() results in clearer text compared with imagejpeg()
        imagepng($im);
        imagedestroy($im);
    }

    /**
     * Starts a clock to compute execution time. Set a key if you want to use more than one.
     * @param string $key An optional key to compute overlapping times.
     * @example
     * <code>
     * clock_start('all');
     * clock_start('query');
     *
     * $result = query('SELECT * ...')
     * clock_end('query');
     *
     * doSomething($result);
     * echo $result;
     * clock_end('all');
     *
     * $queryTime = clock_time('query');
     * $totalTime = clock_time('all');
     * </code>
     * @return float The time the clock starts; format: seconds.microseconds
     */
    function clock_start($key = 'main') {
        $time = explode(' ', microtime());
        $start = (float)$time[0] + (float)$time[1];
        $GLOBALS['_clock_'][$key]['start'] = $start;
        return $start;
    }

    /**
     * Stops a clock that was previously set. Returns 0 if the clock has not been started.
     * @param string $key The clock you want to stop.
     *
     * @return float The time the clock stops; format: seconds.microseconds.
     */
    function clock_end($key = 'main') {
        if (!$GLOBALS['_clock_'][$key]) {
            return 0;
        }
        $time = explode(' ', microtime());
        $end = (float)$time[0] + (float)$time[1];
        $GLOBALS['_clock_'][$key]['end'] = $end;
        return $end;
    }

    /**
     * Returns the execution time of a previously stopped clock. Returns -1 if the clock has not been stopped.
     * @param string $key The clock you want the total time of.
     * @param bool|false $all Returns [end_time - start_time]. If true, it also returns start_time and end_time
     *
     * @return float The total time; format: seconds.microseconds
     */
    function clock_time($key = 'main', $all = false) {
        if (!empty($GLOBALS['_clock_'][$key]['end'])) {
            return -1;
        }
        $GLOBALS['_clock_'][$key]['diff'] = $GLOBALS['_clock_'][$key]['end'] - $GLOBALS['_clock_'][$key]['start'];
        if (!$all) {
            return $GLOBALS['_clock_'][$key]['diff'];
        } else {
            return [
                'start' => $GLOBALS['_clock_'][$key]['start'],
                'end' => $GLOBALS['_clock_'][$key]['end'],
                'diff' => $GLOBALS['_clock_'][$key]['diff']
            ];
        }
    }

    /**
     * Saves an associative array to a file <b>${path}.ini</b>
     * @param $assoc_arr array The array to save
     * @param bool|false $has_sections Whether the config file has sections or is of the form &lt;KEY&gt;=&lt;VALUE&gt;
     * @param string $path The absolute path to save the $assoc_arr to
     *
     * @return array <b>[success => &lt;success&gt;, reason => &lt;When an error, returns the reason&gt;]</b>
     */
    function save_ini_file($assoc_arr, $has_sections=FALSE, $path) {
        if (empty($assoc_arr) || !count($assoc_arr)) {
            return ['success' => 0, 'reason' => "Array is empty. You might not have any active plugins"];
        }
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
                $content .= "\n";
            }
        }
        $handle = NULL;
        if (!$handle = fopen($path, 'w')) {
            return ['success' => 0, 'reason' => 'Cannot open handle "' . $path . '"'];
        }
        if (!fwrite($handle, $content)) {
            return ['success' => 0, 'reason' => 'Cannot write $content into "' . $path . '"'];
        }
        fclose($handle);
        if (function_exists('shell_exec')) {
            shell_exec('chmod 666 ' . $path);
        }
        return ['success' => 1];
    }

    /**
     * Returns the client's IP address.
     *
     * @return string client's ip address.
     */
    function getClientIP() {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns the client's User Agent (if any).
     *
     * @return string browser's UA (user agent).
     */
    function getClientUserAgent() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        } else {
            return "";
        }
    }

    /**
     * Spit headers that force cache volatility.
     *
     * @return void
     */
    function nocache() {
        header('Expires: Tue, 13 Mar 1979 18:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    /**
     * Copy or move a folder recursively
     *
     * @param string $src The source folder
     * @param string $dst The destination folder
     * @param string $action The action to perform: copy (default) or rename (= move)
     */
    function folder_recurse($src,$dst, $action = 'copy') {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    folder_recurse($src . '/' . $file,$dst . '/' . $file, $action);
                }
                else {

                    $action($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Deletes a directory and all the files in it
     */
    function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }