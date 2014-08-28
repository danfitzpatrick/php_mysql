<?

/*
This simple script was written quite a few years ago, but it is here to demonstrate I have used PHP and mySQL.

The purpose of this script was to log information about the users who visited my websites to a MySQL database.

Information I wanted to log was IP address, referrer, user agent, the domain (which of my sites did they visit) and the page visited.
The page visited corresponded to a single keyword so it helped me to see what subject matter was attracting the most vistors.

*/

//connecting to the database we setup
DEFINE('DB_HOST','mysql.mydatabase.com');
DEFINE('DB_USER','user');
DEFINE('DB_PASS','password');
DEFINE('DB_PRIMARY','dbname');

$con = mysql_connect(DB_HOST,DB_USER,DB_PASS);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db(DB_PRIMARY, $con) OR die ('Could not select the DB' . mysql_error());

// Server variables
$ip = $_SERVER['REMOTE_ADDR'];
$referer = $_SERVER['HTTP_REFERER'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$domain = THIS_DOMAIN; // script was used on multiple sites, so I need to know which site was visited in this case
$site = THIS_DOMAIN."/".$pagekeyword; // corresponds to the actual page of the site

if ($inquiry == 1) { 
 $redir = "REDIRECT";
 } else {
  $redir = "Normal";
  }

	$regex = '/[\?\&]q=(.+)/';
	preg_match($regex,$referer,$match);
	$keyword = $match[1];
	$check = strpos($keyword, '&');
	if ($check) {
	$keyword = substr($keyword, 0, ($check));
	 }

$keyword=urldecode($keyword);

	if (!stristr  ($pagekeyword, "favicon") ) {
	
$sql = "INSERT INTO clicks (keyword, source, ip, referer, useragent, time, site) VALUES ('$keyword','$redir','$ip','$referer','$useragent',NOW(),'$site')";
mysql_query($sql);

}

?>
