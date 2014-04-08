<?php
/********************************************************************
*			   phpLite - SQLite Connection Script					*
*				   Author: Sven Gössling							*
*				  Site: Sven-Goessling.de							*
*																	*
*						Version: 1.0.0								*
*																	*
*	                     - Donations - 								*
*			BTC: 1LvETe6uTP64hK3UR3oSAdzT5ZjLnttqBm					*
*			DEM: NWtFftChrx28mvYgqfopmDejxoHiZmAK7u					*
********************************************************************/
namespace lib;
use lib\Cgminer;
use lib\SQLiteConn;
use PDO;

include_once(__DIR__ . '/../lib/cgminer.php');

/* SQLite Connection */
include_once(__DIR__ . '/sqlite_con.php');
$SQLITEdb = "/var/www/sq_db/cgminer";

$db = new SQLiteConn;
$db = $db->db_con($SQLITEdb);
/* SQLite Connection */

$cgminer = new Cgminer('127.0.0.1', 4028);

$hashrate = $cgminer->hashrate();
$shares = $cgminer->shares();
$devs = $cgminer->devices();
$pools = $cgminer->pools();
//$switchpool = $cgminer->switchpool(0);
//$stats = $cgminer->stats();

	$share_difficulty = 0;
	foreach ($pools as $pool) {
		if ($pool->{'Stratum Active'} == 1) {
			$share_difficulty = $pool->{'Last Share Difficulty'};
		}
	}

	if (isset($argv['1']) && $argv['1'] == 'new') {
		echo '-DELETE-'.PHP_EOL;
		$query = $db->query("DELETE FROM `share_statistics`");	
	}
	
	if (!isset($hashrate['Average'])) exit;

	$query = $db->query("SELECT * FROM `share_statistics` ORDER BY `id` DESC LIMIT 1");
	$result = $query->fetch(PDO::FETCH_ASSOC);

ECHO date ('H:i:s:u - d.m.y', time()).' - UPDATE NEW: ';
$time_shares = $hashrate['Accepted']-$result['total_shares'];
$time_rejected = $hashrate['Rejected']-$result['total_rejected'];
$query = $db->query("INSERT INTO `share_statistics` (`total_shares`,
												   `total_rejected`,
												   `stale`,
												   `hash`,
												   `time`,
												   `time_shares`,
												   `time_rejected`,
												   `share_difficulty`
												) VALUES (
												   ".$hashrate['Accepted'].",
												   ".$hashrate['Rejected'].",
												   ".$hashrate['Stale'].",
												   ".$hashrate['Average'].",
												   '".time()."',
												   '".$time_shares."',
												   '".$time_rejected."',
												   ".$share_difficulty."
												)") OR die ('Error SQL 1');

echo 'OK' . PHP_EOL;
//Entferne alle Einträge älter als 2 Tage
$localtime = time();
$removeold = $localtime - (60*60*24*2);
echo date ('H:i:s:u - d.m.y', time()).' - REMOVE OLD DATA: ';
$query = $db->query("DELETE FROM `share_statistics` WHERE `time` < ". $removeold) OR die('Error SQL 2');
echo 'OK'. PHP_EOL;
//$result = $query->fetch(PDO::FETCH_ASSOC);
//print_r($result);

?>
