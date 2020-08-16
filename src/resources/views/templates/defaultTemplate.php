<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo (!empty($_title) ? $_title . ' | ' : '') . __SITE_NAME__;?></title>
    <meta property="og:title" content="<?php echo (!empty($_title) ? $_title . " | " : "") . __SITE_NAME__;?>">
    <meta name="description" content="<?php echo __SITE_DESCR__;?>">
    <meta property="og:description" content="<?php echo __SITE_DESCR__;?>">
    <meta property="og:url" content="<?php echo __SITE_URL__ . $_SERVER['REQUEST_URI'];?>">
    <meta property="og:image" content="<?php echo (!empty($_ogImage) ? $_ogImage : (__SITE_URL__ . $this->asset('images/favicon.png', TRUE))); ?>">
    <base href="/">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php $this->asset(!empty($_favicon) ? $_favicon : 'images/favicon.png');?>" rel="shortcut icon" type="image/x-icon" />
</head>
<body>
<app-root></app-root>
</body>
</html>