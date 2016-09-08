<?php

    DEFINE('__ROOT__', realpath(__DIR__ . '/..'));

    // we "init()" the thread
    require_once '../app/Kernel/AppKernel.php';
    \Kernel\AppKernel::init();