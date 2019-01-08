<?php
date_default_timezone_set('America/Guatemala');
//Define constants
define('DEBUG', true);
define("DS", "/");
define("APP", __DIR__ . "/application");
define("SEC", __DIR__ . "/security");
define("LIB", __DIR__ . "/libraries");
define("OBJ", __DIR__ . "/objects");
define('ENT', OBJ . DS . "Entities");
define("SYSTEM", OBJ . "/system");
define("WRAPPER", OBJ . "/Parent");
define("CONFIG", __DIR__ . "/config");
define("IMG_PATH", __DIR__ . "/media/img");
define("PATH_UPLOAD_GENERAL", __DIR__ . "/media/uploads");
define('VIEWS', APP . "/views");
define('PDF_TEMPLATE',  APP . '/templates');
define('MAIL_TEMPLATE',  APP . '/mail');
define("APP_NAME", "FODES");
define("AJAX", "ajax");
define('USER', APP_NAME . "_USER");
define("USER_TYPE", APP_NAME . "_USER_TYPE");
define('MAX_FILE_SIZE', 2000000);
define('SQL_DT_FORMAT', 'Y-m-d H:i:s');
define('SHOW_DT_FORMAT', 'd/m/Y H:i:s');
define('VENDOR', __DIR__ . "/vendor");
define('TEMP_DIR', sys_get_temp_dir());

//Display errors
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? 'On' : 'Off');
//ini_set('open_basedir', TEMP_DIR);
//Run the application
require_once VENDOR . DS . 'autoload.php';
include_once SYSTEM . '/AppController.php';
AppController::getApp()->run();
