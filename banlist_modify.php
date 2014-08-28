<?php /**/ ?><?php 

/*
This simple script was written quite a few years ago, but it is here to demonstrate I have used PHP and mySQL.

The purpose of this script was to maintain a database of IP addresses I wanted to ban from accessing my sites.
With this script you could add or remove an IP address from a MySQL database, using a simple HTML form.

*/

// Database constants 
define('localhost', 'mysql.mywebsite.net'); 
define('mysql_user', 'myusername'); 
define('mysql_password', 'mypassword'); 
define('database_name', 'mydatabasename'); 

// IP address parts defined
define('_OCT0', pow(256, 3));
define('_OCT1', pow(256, 2));
define('_OCT2', 256);

function iptoint($inStr) {
	preg_match('/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/', $inStr, $elements);
		if (!$elements[4]) { 
		$inStr = $inStr.".0";
		}

		if (!preg_match('/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/', $inStr, $parts))  {
		echo "IPtoInt: Invalid IP Address ($inStr)"; 
		}
	return ($parts[1] * _OCT0) + ($parts[2] * _OCT1) + ($parts[3] * _OCT2);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<body><h1>BAN List Modifier</h1><br />

Input Ip Address To BAN Here (current address is <?php echo $_SERVER['REMOTE_ADDR']; ?>) :
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
         <textarea name="input_add"></textarea><br />
         <input type="submit" /><br />
    </form><br /><br />
      
<?php 

if(isset($_POST['input_add'])) {
	$bannedip = iptoint($_POST['input_add']);
	$ip_seg = intval($bannedip / 65536);
	$link = mysql_connect(localhost, mysql_user, mysql_password);
	if (!$link) { die('Could not connect: ' . mysql_error());
					} else { /* echo "Searching ... <br /><br />"; */ }
	mysql_select_db(database_name);

	$query = "SELECT * FROM bannedips WHERE bannedip = $bannedip"; // Check if IP is already banned
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_affected_rows() == 1) {
		echo "Ip Already Exists as $bannedip!<br />";
	} else {
		$query = "INSERT INTO bannedips (ipseg, bannedip) VALUES ('$ip_seg','$bannedip')";
		$result = mysql_query($query) or die(mysql_error());
		echo "ip added as $bannedip!<br /><br /><br />";
	} 
} // end if

?>

Input Ip Address To UN-ban Here:
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
         <textarea name="input_rem"></textarea><br />
         <input type="submit" /><br />
      </form><br /><br />

<?php if(isset($_POST['input_rem'])) {
	$bannedip = iptoint($_POST['input_rem']);
	$link = mysql_connect(localhost, mysql_user, mysql_password);
	if (!$link) { die('Could not connect: ' . mysql_error());
				} else { /* echo "Searching ... <br /><br />"; */ }

mysql_select_db(mydatabasename);
$query = "DELETE FROM bannedips WHERE bannedip = $bannedip LIMIT 1;";
$result = mysql_query($query) or die(mysql_error());

if (mysql_affected_rows() == 1) {
	echo "Ip UN-banned!<br />";
	} else {
	echo "No Such Ip in Database<br />";
	}

} // end if(isset($_POST['input_rem']))      
      
?>

</body>
</html>
