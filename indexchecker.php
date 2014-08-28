<?php 

/*
This simple script was written quite a few years ago, but it is here to demonstrate I have used PHP and mySQL.

The purpose of this script was to check how many pages of my numerous 1000+ page websites were indexed by Google.
This script was run automatically every night by chron job, and the results were placed in the database. 

*/

set_time_limit  (0);

define('localhost', 'mysql.mydatabase.com'); // Location of database
define('mysql_user', 'user'); // Database username
define('mysql_password', 'password'); // Database password
define('database_name', 'dbname'); // Database name

$date = date('Y-m-d');

$con = mysql_connect(localhost,mysql_user,mysql_password);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db(database_name, $con);

$sites = file('/home/my_dh_username/sites.txt'); // This is a text file of all the websites I want to check, one line per site

echo"Number of pages indexed";

echo"<table width='300' border='1'>  <tr>";

foreach ($sites as $base_url) {

	sleep(rand(6, 12)); // This is to humanize the script and prevent Google from banning it for automated use

	$sitefix = str_replace("/", "%2F", $base_url); // Each of the websites in the list is passed into $base_url

	// Each website is placed into the Google query, which is simply "site:mywebsite.com."
	// This returns the number of pages of that website that are indexed in Google, which means people can find them in a search.
	$url = "http://www.google.com/search?q=site%3A".$sitefix."&ie=utf-8&oe=utf-8&aq=t&rls=org.mozilla:en-US:official&client=firefox-a";

	// This code scrapes the text off the page
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curl_scraped_page = curl_exec($ch);
	curl_close($ch);
	$data = $curl_scraped_page;

	$about = '/of about/';
	$nomatch = '/did not match any/';

	// Google response follows 3 patterns, depending on what is found.
	if (preg_match($about, $data))
		{
		$regex = '/of about (.+?) from/';
		preg_match($regex,$data,$match);
		$page_count = strip_tags($match[1]);
		$page_count = str_replace(",", "", $page_count);
		}
	elseif (preg_match($nomatch, $data))
		{
		$page_count = 0;
		}
	else 
		{
	$regex = '/Results (.+?) - (.+?) of (.+?) from/';
		preg_match($regex,$data,$match);
		$page_count = strip_tags($match[3]);
		$page_count = str_replace(",", "", $page_count);
		}	

	   echo "<td>".$base_url."</td> <td>".$page_count."</td> </tr>"; 
	      
	   $total=$total+$page_count; // this is to total all pages indexed for all websites

	   // places results in database so we can compare results over time
	   $sql_query = mysql_query("INSERT INTO pages_indexed (ID, date, base_url, page_count) VALUES 
	('', '$date', '$base_url', '$page_count')") or die (mysql_error()); 

} // end of for each loop

   echo "<td><b>Total Pages Indexed</b></td> <td><b>".$total."</b></td> </tr>"; 

echo"</table>";

?>
