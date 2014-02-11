<?php

/*
 * To enable URL rewriting, please set the $rewriting variable on 'true'
 *
 * Ensure you have done every other steps described on
 * https://github.com/Bioshox/Raspcontrol/wiki/Enable-URL-Rewriting#configure-your-web-server
 */
$rewriting = false;

/*
 * Do NOT change the following lines
 */
error_reporting(0);

/* SQLite Connection */
include_once('functions/sqlite_con_run.php');
$SQLITEdb = "/var/www/sq_db/cgminer";

$db = db_con($SQLITEdb);
/* SQLite Connection */

define('INDEX', './');
define('LOGIN', 'login.php');
define('FILE_PASS', '/etc/raspcontrol/database.aptmnt');

if ($rewriting) {
  define('LOGOUT', './logout');
  define('DETAILS', './details');
  define('SERVICES', './services');
  define('DISKS', './disks');
  define('CGMINER', './cgminer');
}
else {
  define('LOGOUT', './login.php?logout');
  define('DETAILS', './?page=details');
  define('SERVICES', './?page=services');
  define('DISKS', './?page=disks');
  define('CGMINER', './?page=cgminer');
}

?>