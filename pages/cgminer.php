<?php
namespace lib;
use lib\Cgminer;
use PDO;
$cgminer = new Cgminer('127.0.0.1', 4028);

$hashrate = $cgminer->hashrate();
$shares = $cgminer->shares();
$devs = $cgminer->devices();
$pools = $cgminer->pools();
//$switchpool = $cgminer->switchpool(0);
$stats = $cgminer->stats();
?>

<div class="container details">
<div style="display:inline">
	<h2>Average Hashrate</h2>
	<div id="graph_hash"></div>
	<h2>Hour Shares</h2>
	<div id="graph_shares"></div>
</div>
<script type="text/javascript">
var timestamp_data = [
<?php
	$query = $db->query("SELECT * FROM (SELECT * FROM `share_statistics` ORDER BY `id` DESC LIMIT 24) tmp ORDER BY tmp.id ASC");
<<<<<<< HEAD
	$periode = '{"period": '.time().'000, "hashrate": '.$hashrate['Average'].'}';
=======
	$periode = '{"period": '.time().'000, "hashrate": '.$hashrate['Average'].', "shares": 0, "rejected": '.($hashrate['Rejected']-$result['total_rejected']).'}';
>>>>>>> 406384bd43c7fb92a15a0bb575697b03e7f5ea4f
	$share_count = 0;
	$rejected = 0;
	$interval_count = 0;

	$counter = 0; //Interval Count in Min.
	while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
	$share_count += ($result['time_shares']*$result['share_difficulty']);
	$rejected += $result['time_rejected'];
		if ($counter >= $interval_count) {
			if ($periode != "") {$periode .= ",";} 
			$periode .= '{"period": '.$result['time'].'000, 
						  "hashrate": '.$result['hash'].', 
						  "shares": '.$share_count.',
					      "rejected": '.$rejected.'}';
			$counter = 0;
			$share_count = 0;
			$rejected = 0;
		}
		$counter++;
	}
	echo $periode;
?>
];
Morris.Line({
  element: 'graph_hash',
  data: timestamp_data,
  xkey: 'period',
  ykeys: ['hashrate'],
  labels: ['Average Hashrate'],
  dateFormat: function (x) { return new Date(x).toDateString(); }
});
Morris.Line({
  element: 'graph_shares',
  data: timestamp_data,
  xkey: 'period',
  ykeys: ['shares', 'rejected'],
  labels: ['Accepted Pool Shares', 'Pool Rejected Shares'],
  dateFormat: function (x) { return new Date(x).toDateString(); }
});
</script>
<?
	$config_file = '../../home/pi/cgminer.conf';
	
	if (isset($_POST['restart']) && $_POST['restart'] == "Restart") {
	// Restart CGMiner
		$restart = $cgminer->restart();
		echo '<script language="javascript" type="text/javascript" src="js/reload.js"></script>';
		echo '<div class="failedsave">Miner wird neu gestartet - <b id="cID3"><script>countdown(15,\'cID3\');</script></b> - </div>';
	}
	if (isset($_POST['save']) && $_POST['save'] == "Speichern" && $_POST['configtext'] != "") {
		if (!file_put_contents($config_file, $_POST['configtext'])) {
		echo '<div class="failedsave">Fehlende Schreibrechte beim Speichern der Config-Datei.<br>
			  Setze 0666 auf '.$config_file.'</div>';
		
		} else {
		echo '<script language="javascript" type="text/javascript" src="js/reload.js"></script>';
		echo '<div class="saved"><b id="cID3"><script>countdown(2,\'cID3\');</script></b> - Datei wurde erfolgreich gespeichert.<br></div>';
		}
	}
	
	$cgminer_config = file_get_contents($config_file);
?>

	<table>
		<tr id="cgminer-version">
			<td class="check"><i class="icon-play-circle"></i> Miner</td>
			<td class="icon"></td>
			<td class="infos">
				Miner version: <span class="text-info"><?= $cgminer->cgversion() ?></span>
				<br />API version: <span class="text-info"><?= $cgminer->apiversion() ?></span>
				<br />Uptime: <span class="text-info">
					<?php
						$time = 0;
						if ($cgminer->uptime()/60 > 60) {
							$time = ($cgminer->uptime()/(60))/60 .' hours.';
						} else if ($cgminer->uptime() > 60) { 
							$time = $cgminer->uptime()/60 .' min.';
						} else {
							$time = $cgminer->uptime().' sec';
						}
						echo $time;
					?></span>
			</td>
		</tr>

		<tr id="cgminer-active-pools">
			<td class="check"><i class="icon-globe"></i> Active Pools</td>
			<td class="icon"></td>
			<td class="infos">
				<?php
					foreach ($pools as $pool) {
						if ($pool->{'Stratum Active'} == 1) {
						
						echo '<i class="icon-active"></i>' . $pool->{'URL'};
						echo ' | Last Share: <span class="text-info">'.date('H:i:s - d.m.Y', $pool->{'Last Share Time'}).'</span><br>';
						echo '-> Accepted: <font color="darkgreen">'.$pool->{'Accepted'}.'</font> | Rejected: <font color="orange">'.$pool->{'Rejected'}.'</font> | Stale: <font color="darkred">'.$pool->{'Stale'}.'</font><br>';
						}
						
					}
				?>
			</td>
		</tr>
		
		<tr id="cgminer-shares">
			<td class="check"><i class="icon-globe"></i> Shares</td>
			<td class="icon"></td>
			<td class="infos">
				<div class="progress">
					<div class="bar bar-<?php echo $shares->alert; ?>" style="width: <?php echo $shares->percentage; ?>%;<?php if ($shares->percentage > 50) echo 'color:#FFF;' ?>"><?php echo $shares->percentage; ?>%</div>
				</div>
				Accepted: <span class="text-success"><?= $shares->Accepted ?></span> &middot; Rejected: <span class="text-warning"><?= $shares->Rejected ?></span> &middot; Stale: <span class="text-warning"><?= $shares->Stale ?></span>
			</td>
		</tr>
		<tr id="cgminer-summary">
			<td class="check"><i class="icon-asterisk"></i> Hashrate</td>
			<td class="icon"></td>
			<td class="infos">
				Average: <span class="text-info">
					<? 	$_hashrate = 0;
						if ($hashrate['Average'] > 1000) { 
							$_hashrate = $hashrate['Average']/1000;
							echo $_hashrate . ' GH/s';
						} else {
							echo $_hashrate . ' MH/s'; 
						}
					?></span>
				<br />Last 5sec: <span class="text-info">
					<? 	$_hashrate = 0;
						if ($hashrate['5s'] > 1000) { 
							$_hashrate = $hashrate['5s']/1000;
							echo $_hashrate . ' GH/s';
						} else {
							echo $_hashrate . ' MH/s'; 
						}
					?></span>
			</td>
		</tr>
		<tr id="cgminer-devices" class="storage">
			<td class="check" rowspan="<?php echo sizeof($devs); ?>"><i class="icon-tasks"></i> Mining Devices</td>
			<?
			if ( sizeof($devs) == 0) { echo '<td></td><td></td><td>No Devices found</td>';}
			for ($i = 0; $i < sizeof($devs); $i++) {
				?>
				<td class="icon" style="padding-left: 10px;"><i class="<?= ($devs[$i]->Status == 'Alive' ? 'icon-ok' : 'icon-remove') ?>"></i></td>
				<td class="infos">
					<i class="icon-folder-open"></i> <?= $devs[$i]->Name . $devs[$i]->ID ?> --
						MHS Average: <span class="text-info"><?= $devs[$i]->{'MHS av'} ?></span>
						Last 5sec: <span class="text-info"><?= $devs[$i]->{'MHS 5s'} ?></span>
						HWs: <span class="text-info"><?= $devs[$i]->{'Hardware Errors'} ?></span>
						Shares: <span class="text-info"><?= $devs[$i]->{'Accepted'} ?></span>
					<div class="progress">
					<? $devs[$i]->{'Device Hardware%'} = ($devs[$i]->{'Hardware Errors'}/$devs[$i]->{'Accepted'})*100; ?>
						<div class="bar bar-<?= ($devs[$i]->{'Device Hardware%'} > 95 ? 'success' : ($devs[$i]->{'Device Hardware%'} > 90 ? 'warning' : 'danger')) ?>" style="width: <?php echo $devs[$i]->{'Device Hardware%'}; ?>%; <?php if ($devs[$i]->{'Device Hardware%'} > 50) echo 'color:#FFF;' ?>"><?php echo $devs[$i]->{'Device Hardware%'}; ?>%</div>
					</div>
					Last Share: <span class="text-info"><?= date('H:i:s - d.m.Y', $devs[$i]->{'Last Share Time'}) ?></span>
				</td>
			</tr>
			<?
			echo ($i == sizeof($devs)-1) ? null : '<tr class="storage">';
		}
		?>
	</tr>
	<tr id="cgminer-pools" class="storage">
		<td class="check" rowspan="<?php echo sizeof($devs); ?>"><i class="icon-globe"></i> All Mining Pools</td>
		<?
		for ($i = 0; $i < sizeof($pools); $i++) {
			$pools[$i]->{'Pool Accepted%'} = round(100 - $pools[$i]->{'Pool Rejected%'} - $pools[$i]->{'Pool Stale%'}, 2);
			?>
			<td class="icon" style="padding-left: 10px;"><i class="<?= ($pools[$i]->Status == 'Alive' ? 'icon-ok' : 'icon-remove') ?>"></i></td>
			<td class="infos">
				<i class="icon-folder-open"></i> <?= $pools[$i]->URL ?>
				<div class="progress">
					<div class="bar bar-<?= ($pools[$i]->{'Pool Accepted%'} > 95 ? 'success' : ($pools[$i]->{'Pool Accepted%'} > 90 ? 'warning' : 'danger')) ?>" style="width: <?php echo $pools[$i]->{'Pool Accepted%'}; ?>%;<?php if ($pools[$i]->{'Pool Accepted%'} > 50) echo 'color:#FFF;' ?>"><?php echo $pools[$i]->{'Pool Accepted%'}; ?>%</div>
				</div>
				Last Share: <span class="text-info"><?= date('H:i:s - d.m.Y', $pools[$i]->{'Last Share Time'}) ?></span>
				<br />User: <span class="text-info"><?= $pools[$i]->User ?></span>
				<br />Remote Errors: <span class="text-info"><?= $pools[$i]->{'Remote Failures'} ?></span>
			</td>
		</tr>
		<?
		echo ($i == sizeof($devs)-1) ? null : '<tr class="storage">';
	}
	?>
</tr>
</table>
<div style="font-weight: bold;">CGMiner.conf</div>
	<form action = "#" method = "POST">
		<textarea name="configtext" rows="20" style="width:100%;"><? if ($cgminer_config != "") echo $cgminer_config; ?></textarea>
		<input class="ButtonRestart" type="submit" name="restart" value="Restart">
		<input class="Buttonsave" type="submit" name="save" value="Speichern">
	</form>
	</div>
