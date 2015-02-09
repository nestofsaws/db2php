<!--
Brian Zimorowicz
INLS 760, Capra, Project 2, Spring 2015
Assigned: Feb 06, PHP Read DB Display Web
-->

<!DOCTYPE html>
<html lang="en">
  <head>
	<title>P2</title>
	<link href="styles.css" rel="stylesheet">
	</title>
  </head>
  <body>
  <h1><a href='../inls760/p2.php'>Project 2</a></h1>
<?php
	require_once('/export/home/b/brianz/dbconnect.php');
	$query = "SELECT * FROM p2records";
	$query1 = "SELECT * FROM p2records";

    $rowcount = 0;
    $pages = 0;
    
    if ($result = mysqli_query($db,$query)) {
		$rowcount = mysqli_num_rows($result);
		$pages = ($rowcount / 25);
	}
	
	if (isset($_GET['page'])) { 
		$page  = $_GET['page']; 
		echo "Page " . $page . " of " . $pages;
	} else { 
		$page = 1;
		echo "Page " . $page . " of " . $pages; 
	};
	$page_start = ($page - 1) * 25;
	$url = "p2.php";
	
	
	echo "<table><thead><tr>";
	echo "<th><a href='p2.php?sortby=authors'>Author(s)</a></th>";
	echo "<th><a href='p2.php?sortby=title'>Title</a></th>";
	echo "<th><a href='p2.php?sortby=publication'>Publication</a></th>";
	echo "<th><a href='p2.php?sortby=year'>Year</a></th>";
	echo "<th><a href='p2.php?sortby=type'>Type</a></th>";
	
    
	if ($_GET['sortby'] == 'authors'){
		$query .= " ORDER BY authors LIMIT $page_start, 25";
		$url = "p2.php?sortby=authors&";
	} elseif ($_GET['sortby'] == 'title'){
		$query .= " ORDER BY title LIMIT $page_start, 25";
		$url = "p2.php?sortby=title&";
	} elseif ($_GET['sortby'] == 'publication'){
		$query .= " ORDER BY publication LIMIT $page_start, 25";
		$url = "p2.php?sortby=publication&";
	} elseif ($_GET['sortby'] == 'year'){
		$query .= " ORDER BY year LIMIT $page_start, 25";
		$url = "p2.php?sortby=year&";
	} elseif ($_GET['sortby'] == 'type'){
		$query .= " ORDER BY type LIMIT $page_start, 25";
		$url = "p2.php?sortby=type&";
	} else {
		$query .= " LIMIT $page_start, 25";
	} 



	
	if ($result = mysqli_query($db,$query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			echo "<tr><td>" . $row['authors'] . "</td>" .
			"<td><a href='" .$row['url'] . "' target='_blank'>" . 
			$row['title'] . "</a></td>" .
			"<td>" . $row['publication'] . "</td>" .
			"<td>" . $row['year'] . "</td>" .
			"<td>" . $row['itemnum'],$row['type'] . "</td></tr>" ;
		}
	}
	echo "</tbody></table><br />";

	if (empty($_GET['sortby'])){
		for ($p = 1; $p <= $pages; $p++) {
    		echo "<a href='" . $url . "?page=" . $p . "'>Page " . $p . " </a>";
		};
	} elseif (!empty($_GET['sortby'])) {
		for ($p = 1; $p <= $pages; $p++) {
    		echo "<a href='" . $url . "page=" . $p . "'>Page " . $p . " </a>";
	}; 
}

echo "<hr>";
if ($result = mysqli_query($db,$query1)) {
		while ($row = mysqli_fetch_assoc($result)) {
			//$emps = array();
			$a = $row['authors'];
			$fields = explode(' and ',$a);
			$firstAuth = $fields['0'];
			$surName = explode(' ',$firstAuth);
			echo "<br />";
			$nameNum = max(array_keys($surName));
			//sort($surName);
			print_r($surName);
        	
        }		
}

	/* close connection */
	//$mysqli->close($db);
?>
</body>
</html>
