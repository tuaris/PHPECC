<html>
<head>
<title>Simple PHP ECC Front End Demo</title>
</head>

<body>
<h1>PHP ECC</h1>
<p>This demo genorates an EC keypair in HEX format using Matyas Danter's phpecc libraries.</p>
<?php
//Use base 256
define('MAX_BASE', 256);

//Force Either BCMATH or GMP, Autodetected otherwise, prefers GMP
//if(!defined('USE_EXT')) define ('USE_EXT', 'BCMATH');
//if(!defined('USE_EXT')) define ('USE_EXT', 'GMP');

include 'autoload.inc.php';
include 'classes/PHPECC.class.php';
include 'classes/SECurve.class.php';

$keypair = PHPECC::hex_keypair_genorate();
?>

<div>
<strong>Private Key</strong>: <pre><?php echo $keypair['private']; ?></pre>
</div>

<div>
<strong>Public Key</strong>: <pre><?php echo $keypair['public']; ?></pre>
</div>
</body>
</html>