<?php
/**
 * @AUTHOR  MJZ
 * @ABOUT   set basic parameters and defines, need for each and every page
*/
session_start();
ob_start();
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
ini_set('max_execution_time',"1250");
ini_set('memory_limit',"128M");
header('Access-Control-Allow-Origin: *');
define('AUTHOR' , 'MJZ' );
define('START_TIME' , microtime(true) );
define('START_MEMO' , memory_get_usage() );
define('LOCAL_HOST', ($_SERVER['SERVER_NAME'] == "localhost") ? 1 : 0 );
define('SI',md5(AUTHOR . date("YDM",time()) ));
define('SELF_DIR',$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));//."index.php/"
define('TITLE',str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
define('IX', count(explode("/",trim(TITLE,"/"))));
define('ADMIN',1);
if(isset($_SESSION[SI]) && START_TIME > $_SESSION[SI]['START_TIME'] * 60 *60){
    session_destroy();
}
if(!isset($_SESSION[SI])){
    $_SESSION[SI]['START_TIME'] = START_TIME;
}
if(!isset($_SESSION[SI]['lang'])){
    $_SESSION[SI]['lang'] = "en";
}
define('GORP',$_SERVER['REQUEST_METHOD']);
define('LANG',$_SESSION[SI]['lang']);

global $DBOCFG;
$DBOCFG = [];
$DBOCFG['online'] = [
    "host"=>"sql130.main-hosting.eu",
    "database"=>"u150998421_nkjobs",
    "user"=>"u150998421_nkjobs",
    "password"=>"nISi21Jwd10"
];
$DBOCFG['hostinger'] = $DBOCFG['online'];
$DBOCFG['local'] = [
    "host"=>"localhost",
    "database"=>"u150998421_nkjobs",
    "user"=>"root",
    "password"=>""
];
$DBOCFG = $DBOCFG["local"];