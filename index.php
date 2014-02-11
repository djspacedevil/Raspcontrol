<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

session_start();

require 'config.php';

// authentification
if (isset($_SESSION['authentificated']) && $_SESSION['authentificated']) {
  if (empty($_GET['page'])) $_GET['page'] = 'home';
  $_GET['page'] = htmlspecialchars($_GET['page']);
  $_GET['page'] = str_replace("\0", '', $_GET['page']);
  $_GET['page'] = str_replace(DIRECTORY_SEPARATOR, '', $_GET['page']);
  $display = true;
  function is_active($page) {
    if ($page == $_GET['page'])
      echo ' class="active"';
  }
}
else {
  $_GET['page'] = 'login';
  $display = false;
}

$page = 'pages'. DIRECTORY_SEPARATOR.$_GET['page']. '.php';
$page = file_exists($page) ? $page : 'pages'. DIRECTORY_SEPARATOR .'404.php';

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Raspcontrol</title>
    <meta name="author" content="Nicolas Devenet" />
    <meta name="robots" content="noindex, nofollow, noarchive" />
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="icon" type="image/png" href="img/favicon.ico" />
    <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
    <link href="css/raspcontrol.css" rel="stylesheet" media="screen" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="https://raw.github.com/DmitryBaranovskiy/raphael/300aa589f5a0ba7fce667cd62c7cdda0bd5ad904/raphael-min.js"></script>
	<script src="/morris/morris.js"></script>
	<script src="/morris/lib/prettify.js"></script>
	<script src="/morris/lib/example.js"></script>
	<link rel="stylesheet" href="lib/example.css">
	<link rel="stylesheet" href="lib/prettify.css">
	<link rel="stylesheet" href="/morris/morris.css">
  </head>

  <body>

    <header>
      <div class="container">
        <a href="<?php echo INDEX; ?>"><img src="img/raspcontrol.png" alt="rbpi" /></a>
        <h1><a href="<?php echo INDEX; ?>">RaspControl</a></h1>
        <h2>Goesi's CGMiner Control Center</h2>
      </div>
    </header>

    <?php if ($display) : ?>

    <div class="navbar navbar-static-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="nav-collapse collapse">
			  <ul class="nav">
				<li<?php is_active('home'); ?>><a href="<?php echo INDEX; ?>"><i class="icon-home icon-white"></i> Home</a></li>
				<li<?php is_active('details'); ?>><a href="<?php echo DETAILS; ?>"><i class="icon-search icon-white"></i> Details</a></li>
				<li<?php is_active('cgminer'); ?>><a href="<?php echo CGMINER; ?>"><i class="icon-search icon-cog"></i> Miner</a></li>
				<li<?php is_active('services'); ?>><a href="<?php echo SERVICES; ?>"><i class="icon-cog icon-white"></i> Services</a></li>
				<li<?php is_active('disks'); ?>><a href="<?php echo DISKS; ?>"><i class="icon-disks icon-white"></i> Disks</a></li>
			  </ul>
			  <ul class="nav pull-right">
				<li><a href="<?php echo LOGOUT; ?>"><i class="icon-off icon-white"></i> Logout</a></li>
			  </ul>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>

    <div id="content">
      <?php if (isset($_SESSION['message'])) { ?>
      <div class="container">
        <div class="alert alert-error">
          <strong>Oups!</strong> <?php echo $_SESSION['message']; ?>
        </div>
      </div>
      <?php unset($_SESSION['message']); } ?>

<?php
  include $page;
?>

    </div> <!-- /content -->

    <footer>
      <div class="container">
        <p>Powered by <a href="http://sven-goessling">Sven Goessling</a> and <a href="https://github.com/Bioshox/Raspcontrol">Raspcontrol</a>.</p>
        <p>Sources are available on <a href="https://github.com/djspacedevil/Raspcontrol">Github</a>.</p>
		<p>Donation BTC: 1LvETe6uTP64hK3UR3oSAdzT5ZjLnttqBm  or  DEM: NWtFftChrx28mvYgqfopmDejxoHiZmAK7u</p>
      </div>
    </footer>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<?php
		// load specific scripts
		if ('details' === $_GET['page']) {
			echo '   <script src="js/details.js"></script>';
		}
	?>
  </body>
</html>
