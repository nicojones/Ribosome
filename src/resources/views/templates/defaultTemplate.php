<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="initial-scale=0.75">

        <link href="<?php $this->asset(!empty($_favicon) ? $_favicon : 'images/favicon.png');?>" rel="shortcut icon" type="image/x-icon" />

        <title><?php echo (!empty($_title) ? $_title . ' | ' : '') . __SITE_NAME__;?></title>
        <meta property="og:title" content="<?php echo (!empty($_title) ? $_title . " | " : "") . __SITE_NAME__;?>">
        <meta name="description" content="<?php echo __SITE_DESCR__;?>">
        <meta property="og:description" content="<?php echo __SITE_DESCR__;?>">
        <meta property="og:url" content="<?php echo __SITE_URL__ . $_SERVER['REQUEST_URI'];?>">
        <meta property="og:image" content="<?php echo (!empty($_ogImage) ? $_ogImage : (__SITE_URL__ . $this->asset('images/favicon.png', TRUE))); ?>">

        <link href="<?php $this->asset('css/style.css');?>"
              type="text/css" rel="stylesheet"/>
        <?php echo $_style; // Extra, custom-set styles?>
    </head>
    <body style='overflow-x: hidden'><?php // This is to avoid weird side-scrollings! ?>
        <div class="container-fluid _wrapper">

            <?php /* Header */ echo $_header; ?>

            <?php view('blocks.flash');?>

            <span id="body_box"><?php echo $_body; // Here goes all the body content ?>

            <?php /* feel free to remove this: it's just for demo */ if(\Core\Providers\Session::isAuthenticated()) { ?>
                <a href="<?php $this->path('Logout');?>" id="logout_label">logout</a>
            <?php } // ?>

            </span>
            <div class="_push"></div>

        </div>
        <?php echo $_footer; ?>
        <div id='fb-root'></div>

        <?php view('blocks.preloads');?>
        <script src="<?php $this->asset('js/script.js');?>" defer="defer"></script>
        <script type="text/javascript">
            var __PATH__ = "<?php echo __PATH__; // you'll need this PATH if you want to, say, add assets from javascript ?>";
            <?php echo $_js; // JS vars added from the controllers?>
        </script>
        <?php echo $_script; // Extra, custom-set scripts?>
    </body>
</html>