<?php
/**
 * sandbox
 *
 * @package App.sandbox
 */
namespace sandbox;

use BEAR\Framework\Framework;

require dirname(__DIR__) . '/App.php';

$app = App::getInstance(__NAMESPACE__);
return $app;