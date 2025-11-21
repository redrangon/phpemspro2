<?php

define('PEP_VERSION','2.0');
define('PEP_PATH',dirname(__FILE__));
define('TIME',time());
session_start();
include 'public/lib/base.cls.php';
\PHPEMSPRO\base::run();