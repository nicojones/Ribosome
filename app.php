<?php

    DEFINE('__ROOT__', __DIR__);

    // we "init()" the thread
    require_once 'app/kernel/AppKernel.php';
    \Kernel\AppKernel::init();