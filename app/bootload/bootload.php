<?php
    session_start();

    $security = parse_ini_file('security.ini');
    define('BOOTLOAD_PASSWORD', $security['PASSWORD']);
    define('S_BASE', !empty($_SERVER['BASE']) ? $_SERVER['BASE'] : "");

    /* If you are working in local ( HTTP_HOST = *.local ) you don't need authentication. */
    if (    (
                isset($_SERVER['HTTP_CLIENT_IP'])
                || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
                || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
                    || php_sapi_name() === 'cli-server')
            ) ||
            ( isset($_SESSION['bootload_authenticated']) && $_SESSION['bootload_authenticated'] == TRUE ) ||
            ( isset($_POST['pass']) && $_POST['pass'] == BOOTLOAD_PASSWORD )
    ) {
        $_SESSION['bootload_authenticated'] = TRUE;
        if ( isset($_POST['pass']) ) {
            /* If it has just authenticated */
            header ('Location: ' . S_BASE . '/_app/bootload/ ' /* self */);
        }
        if ( isset($_GET['logout']) ) {
            $_SESSION['bootload_authenticated'] = FALSE;
            header('Location: ' . S_BASE . '/_app/bootload/ ' /* self */);
        }

        /* All set, user is not an intruder */

        // document root. Change this if you change the location of /bootload
        define('__ROOT__', __DIR__ . '/../..');

    } else {
        /* Intruder = TRUE */
        $_SESSION['bootload_authenticated'] = FALSE;

        header ('HTTP/1.0 403 Forbidden'); ?>
        <!DOCTYPE html>
        <html>
            <header>
                <title>403 Forbidden</title>
            </header>
            <body>
                <style>
                    body { font-family: 'Helvetica Neue', 'Helvetica', sans-serif; }
                    .screen_centered {
                        width: 50%;
                        text-align: center;
                        position:absolute;
                        left: 50%;
                        margin-left: -25%;
                        margin-top: 10%;
                    }
                    input {
                        font-family: inherit;
                        font-size: 15px;
                    }
                </style>
                <span class="screen_centered">
                    <h1>You are not allowed here</h1>
                    <form action="" method="post">
                        <p>The password is in the file <b>security.ini</b></p>
                        <input type="password" autocomplete="off" required="" name="pass" autofocus placeholder="Enter the password">
                        <input type="submit" value="Login">
                    </form>
                </span>
            </body>
        </html>
    <?php
        die();
    }

require_once 'blocks/functions_globals.php';

if (!empty($_GET['assets'])) {
    asset($_GET['assets']);
}
if (!empty($_GET['versioning'])) {
    versioning($_GET['versioning']);
}

define('LOADER', S_BASE . '/_app/bootloadassets/loader.gif');

$config_INI = parse_ini_file(__ROOT__ . '/app/config/config.ini', TRUE, INI_SCANNER_NORMAL);

    $config_db = $config_INI['Database'];
    try {
        $connectionParamsOK = TRUE;
        $db = new PDO ('mysql:host='.$config_db['HOST'].';dbname='.$config_db['DB_NAME'].';charset=utf8', $config_db['USERNAME'], $config_db['PASSWORD']);
    } catch (Exception $e) {
        $connectionParamsOK = FALSE;
    }

if (isset($_GET['action'])) {
    require_once 'processor.php';
    die();
} ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Bootload.php</title>
    </head>
    <body>
        <style>
            <?php echo file_get_contents(__ROOT__ . '/app/bootload/assets/paper_theme.css');?>
            <?php echo file_get_contents(__ROOT__ . '/app/bootload/assets/bootload.css');?>
        </style>
        <div id="all_content_loader" style="width:100px;margin-left:-50px;left:50%;position:fixed;top:200px">
            <img src="<?php echo LOADER?>" width="100%"/>
        </div>
        <div class="container container-fluid" id="all_content" style="display:none;">
            <div class="row" id="check_for_updates_box">
                <h2>
                    <span class="plus" id="toggle_plus_Framework_Versions" onclick="toggleSection('Framework_Versions')">+</span>
                    <span class="glyphicon glyphicon-transfer"></span>
                    Versioning and Upgrades <small id="label_upgrade" style="font-size: 13px;top: -8px;position: relative"></small></h2>
                <div class="col-xs-12 section_toggle" id="section_Framework_Versions">
                    <img id="frameworkversions_loader" src="<?= LOADER ?>" height="30px" style="display: none;" class="pull-left"/>
                    <a href="?action=check-updates" class="_async btn btn-default pull-left" id="check_for_updates_button"
                       data-loading-text="Checking..." data-loading="frameworkversions_loader"
                       data-success="$('#label_upgrade').addClass('label ' + (data.success ? 'label-success':'label-default')).html('<span class=\'glyphicon glyphicon-' + (data.success ? 'ok':'refresh') + '\'></span>')"
                       data-response-box="frameworkversions_results">Check for updates</a>
                    <br/>
                    <br/>
                    <br/>
                    <div class="row" id="frameworkversions_results"></div>
                </div>
            </div>
            <div class="row">
                <h2>
                    <span class="plus" id="toggle_plus_Run_Diagnosis" onclick="toggleSection('Run_Diagnosis')">+</span>
                    <span class="glyphicon glyphicon-flash"></span>
                    Run diagnosis for the framework</h2>
                <div class="col-xs-12 section_toggle" id="section_Run_Diagnosis">
                    <img id="diagnosis_loader" src="<?= LOADER ?>" height="30px" style="display: none;" class="pull-left"/>
                    <a href="?action=run-diagnosis" class="_async btn btn-default pull-left"
                       data-loading-text="Running..." data-loading="diagnosis_loader"
                       data-response-box="diagnosis_results">Run diagnosis</a>
                    <br/>
                    <br/>
                    <br/>
                    <span id="diagnosis_results"></span>
                </div>
            </div>
            <?php if (is_writable(__ROOT__ . '/app/config/config.ini')) { ?>
                <div class="row">
                    <h2>
                        <span class="plus" id="toggle_plus_Config_ini" onclick="toggleSection('Config_ini')">+</span>
                        <span class="glyphicon glyphicon-tasks"></span>
                        Edit <code>config.ini</code></h2>
                    <span class="section_toggle" id="section_Config_ini">
                        <div class="col-xs-12">
                            <p class="help-block">
                                From here you edit the <code>config.ini</code> file.<br/>
                                You are allowed certain flexibility; for total flexibility, edit the file manually.
                            </p>
                        </div>
                        <?php
                            $dirFiles = scandir('config_sections');
                            foreach ($dirFiles as $f) { ?>
                                <?php if ($f[0] == '.') continue; ?>
                                    <div class="col-xs-12">
                                        <?php include 'config_sections/' . $f;?>
                                        <hr/>
                                    </div>
                            <?php } ?>
                    </span>
                </div>
            <?php } ?>
            <?php if (file_exists(__ROOT__ . '/app/cache/vendor_ini.ini') && is_writable(__ROOT__ . '/app/cache/vendor_ini.ini')) { ?>
                <div class="row">
                    <h2>
                        <span class="plus" id="toggle_plus_Vendor_Cache_ini" onclick="toggleSection('Vendor_Cache_ini')">+</span>
                        <span class="glyphicon glyphicon-paperclip"></span>
                        Refresh vendors' cache</h2>
                    <div class="col-xs-12 section_toggle" id="section_Vendor_Cache_ini">
                        <p class="help-block"></p>
                        <img id="vendor_ini_loader" src="<?= LOADER ?>" height="30px" style="display: none;" class="pull-left"/>
                        <a href="?action=vendor-cache-refresh" class="_async pull-left btn btn-default" data-loading="vendor_ini_loader"
                            data-loading-text="Updating cache file">
                            Refresh <code>vendor_ini.ini</code> cache</a>
                    </div>
                </div>
            <?php } ?>
            <?php if ($connectionParamsOK && $config_db['DB_SUPPORT'] == '1') { ?>
                <div class="row">
                    <h2>
                        <span class="plus" id="toggle_plus_Database_Engine" onclick="toggleSection('Database_Engine')">+</span>
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                        Database</h2>
                    <div class="col-xs-12 section_toggle" id="section_Database_Engine">
                        <p class="help-block">This is a mini-tool to create simple tables. For advanced options
                        (<code>FOREIGN_KEYS</code>, etc.,) you must do it manually.</p>
                        <?php include 'blocks/bootload_database.php' ?>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <h2>
                    <span class="plus" id="toggle_plus_Generate_Class" onclick="toggleSection('Generate_Class')">+</span>
                    <span class="glyphicon glyphicon-file"></span>
                    Generate a controller or model</h2>
                <div class="col-xs-12 section_toggle" id="section_Generate_Class">
                    <form class="_form-async" action="?action=generate-class" method="POST" id="generate_class_form"
                          data-success="writeControllerCode(data.responseData)">
                        <div class="form-group">
                            <label for="class_name">Name of the class</label>
                            <input type="text" id="class_name" name="name" placeholder="Enter the class name ('Login', 'Foo',...)"
                                   class="form-control" autocomplete="off" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="class_model_also">Generate a <span id="model_name_placeholder">&lt;NAME&gt;</span>Model also?</label>
                            <input type="checkbox" value="1" id="class_model_also" name="model"/>
                        </div>
                        <div class="form-group">
                            <label for="class_is_plugin">Tick if it's a plugin (i.e. in <b>vendor</b> folder)</label>
                            <input type="checkbox" value="1" id="class_is_plugin" name="vendor"/>
                        </div>
                        <h4 id="will_generate_class"></h4>
<!--                    <img id="createclass_loader" src="--><?//= LOADER ?><!--" height="30px" style="display: none;" class="pull-left"/>-->
<!--                    <a href="?action=generate-class" class="btn btn-default pull-left"-->
<!--                       data-loading-text="Checking..." data-loading="createclass_loader"-->
<!--                        data-success="gid('createclass_controller').value = data.responseData.controller;-->
<!--                        gid('createclass_controller').innerHTML = data.responseData.model;-->
<!--                        gid('createclass_controllername').value = data.responseData.controllerName;-->
<!--                        gid('createclass_modelname').innerHTML = data.responseData.modelName;"-->
<!--                        >Generate default code</a>-->
                        <br/>
                        <input type="submit" value="Generate" class="btn btn-success"/>
                    </form>
                    <br/>
                    <br/>
                    <div class="row" id="createclass_results">
                        <div class="col-xs-6">
                            <h4>Controller <span id="createclass_controllername">will appear here</span></h4>
                            <textarea id="createclass_controller" class="form-control" style="min-height: 250px" onclick="this.select()"></textarea>
                        </div>
                        <div class="col-xs-6">
                            <h4>Model <span id="createclass_modelname">will appear here</span></h4>
                            <textarea id="createclass_model" class="form-control" style="min-height: 250px" onclick="this.select()"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <br/>
        <br/>
        <a href="?logout" id="logout_label">logout</a>
        <script>
            <?php echo file_get_contents(__ROOT__ . '/app/bootload/assets/jQuery.js');?>
            <?php echo file_get_contents(__ROOT__ . '/app/bootload/assets/bootload.js');?>
            <?php echo file_get_contents(__ROOT__ . '/app/bootload/assets/notify.js');?>

            document.getElementById('all_content_loader').style.display = 'none';
            document.getElementById('all_content').style.display = '';
        </script>
    </body>
</html>