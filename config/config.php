<?php
/******************************* PATHS ***************************************/
/*
* Setup your root server path
*/
//define your server  url
define('URL', 'http://raiffagro.dev/');


/*************************** DEFAULT_CONTROLLER *****************************/
/*
* Define your default controller
*/
define('DEFAULT_CONTROLLER', 'login');


/*************************** ERROR_PAGE *****************************/
/*
* Define your default error page
*/
define('ERROR404', 'error');


/*************************** LYBRARY FOLDER *********************************/
/**
* Setup your path to library folder
*/
define('LIBS', 'libs/');


/******************************* HESH ***************************************/
/*
*Hesh salt do not change if it already use an application.
*IMPORTANT!!!: change HESH_SALT value only if it use an application from start
*/ 
define('HESH_SALT','67sd45ergdfg31BBTssasdDFE83476');


/************************ DATABASE CONNECTION PARAMS ************************/
/*
*Database info definition for localhost on my server
*/
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'otkupsir_raiffagro');
define('DB_USER', 'root');
define('DB_PASS', '');
?>