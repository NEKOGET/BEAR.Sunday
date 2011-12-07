<?php

/**
 * Auto Loader using Aura.Autoload.
 *
 */
$loader = require  $system . '/vendor/Aura.Autoload/scripts/instance.php';
$loader->addPrefix('BEAR\Framework\\', $system . '/packages/BEAR.Framework/src');
$loader->addPrefix('BEAR\Resource\\', $system . '/vendor/BEAR.Resource/src');
$loader->addPrefix('Ray\Di\\', $system . '/vendor/Ray.Di/src');
$loader->addPrefix('Ray\Aop\\', $system . '/vendor/Ray.Aop/src');
$loader->addPrefix('restWorld', $appPath);
$loader->setClass('Smarty', $system . '/vendor/Smarty3/libs/Smarty.class.php');
$loader->setClass('Haanga', $system . '/vendor/haanga/lib/Haanga.php');
$loader->register();