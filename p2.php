<!DOCTYPE html>
<html lang="en">
  <head>
  	<meta charset="utf-8"> 
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>P2</title>
	<!-- Latest compiled and minified Bootstrap CSS -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

	<!-- Bootstrap jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

	<!-- Latest compiled Bootstrap JavaScript -->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	
	<!-- Local styles for this page -->
	<link href="brianz_p2_styles.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Playfair+Display+SC' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div class="container">
      <div class="jumbotron">
        <h1 class='text-center'><a href='../inls760/p2.php'>Project 2</a></h1>
        <div class="row">
          <h3>
	      <div class='col-sm-4 text-left'>INLS 760, Capra<br>Assigned Feb 6</div>
	      <div class='col-sm-4 text-center'>  <br>Read DB, Display Web</div>
	      <div class='col-sm-4 text-right'>Spring 2015<br>Due: 5:00pm, Feb 25</div>
          </h3>
        </div>
      </div>
<?php
	require_once('/export/home/b/brianz/dbconnect.php');
	$query = "SELECT * FROM p2records";
	$drop = "ALTER TABLE p2records DROP firstAuthor";
	$add = "ALTER TABLE p2records ADD firstAuthor VARCHAR( 100 )";
	//dump any existing first author last name data, then add the column back to the db
	mysqli_query($db,$drop);
	mysqli_query($db,$add);
	
	if ($result = mysqli_query($db,$query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			//grab authors column, explode on 'and'
			$a = $row['authors'];
			$authors = explode(' and ',$a);
		
			//remove JR, al, and { } with regex
			$authors = preg_replace('/\{|\}|[J][r]|(\b([a][l]))/', '', $authors);
			
			//grab first author, explode on whitespace, 
			$firstAuth = trim($authors['0']);
			$wholeName = explode(' ',$firstAuth);
			
			//take the highest index of the first author array
			$surName = max(array_keys($wholeName));
			//escape special characters so MySQL doesn't complain
			$lastName = mysqli_real_escape_string($db, $wholeName[$surName]);
			
			// ...and stuff the lastname into the DB
			$insertLN = "UPDATE p2records SET firstAuthor='$lastName' WHERE itemnum=$row[itemnum]";
			mysqli_query($db,$insertLN);
        }		
	}
	//calculate the number of pages based off of total results
    $rowcount = 0;
    $pages = 0;
    if ($result = mysqli_query($db,$query)) {
		$rowcount = mysqli_num_rows($result);
		$pages = ($rowcount / 25);
	}
	
	//display what page the user is on if > 1
	if (isset($_GET['page'])) { 
		$page  = $_GET['page']; 
		$percent = ($page/$pages) * 100;
		echo "<button type='button' class='btn btn-success'>
				Page <span class='badge'>" . $page . "</span> of <span class='badge'>" . $pages . "</span>
			  </button>
			  <br><br>
			  <div class='progress'>
			  	<div class='progress-bar progress-bar-success' 
			  	role='progressbar' aria-valuenow='" . 
			      $page . "' aria-valuemin='1' aria-valuemax='" . $pages . "' 
			      style='width:" . $percent . "%'>Page " . $page . 
			    "</div>
			  </div>";
	} else { 
		$page = 1;
		echo "<button type='button' class='btn btn-success'>
				Page <span class='badge'>" . $page . "</span> of <span class='badge'>" . $pages . "</span>
			  </button>
			  <br><br>
			  <div class='progress'>
			    <div class='progress-bar progress-bar-success' 
			    role='progressbar' aria-valuenow='" . 
			    $page . "' aria-valuemin='1' aria-valuemax='" . $pages . "' 
			    style='width:10%'>Page " . $page . "
			    </div>
			  </div>";
	};
?>	
      <div class='page-header'><h5>Click Column Head to Sort</h5></div>
	    <table class="table table-bordered table-striped table-hover">
		  <thead>
		    <tr>
			  <th>
			  <a href='brianz_p2_browse.php?sortby=authors'>
			    <abbr title='Sort by Author'>Author(s) <span class='glyphicon glyphicon-sort-by-alphabet'></span></abbr>
			  </a>
			  </th>
			  <th>
			  <a href='brianz_p2_browse.php?sortby=title'>
			    <abbr title='Sort by Title'>Title <span class='glyphicon glyphicon-sort-by-alphabet'></span></abbr>
			  </a>
			  </th>
			  <th>
			  <a href='brianz_p2_browse.php?sortby=publication'>
			    <abbr title='Sort by Publication'>Publication <span class='glyphicon glyphicon-sort-by-alphabet'></span></abbr>
			  </a>
			  </th>
			  <th>
			  <a href='brianz_p2_browse.php?sortby=year'>
			    <abbr title='Sort by Year'>Year <span class='glyphicon glyphicon-sort-by-alphabet'></span></abbr>
			  </a>
			  </th>
			  <th><a href='brianz_p2_browse.php?sortby=type'>
			    <abbr title='Sort by Type'>Type <span class='glyphicon glyphicon-sort-by-alphabet'></span></abbr>
			  </a>
			  </th>
		    </tr>	
		  </thead>
		  <tbody>	
<?php 

	//change the query and url based on selected sorting
	$page_start = ($page - 1) * 25;
	$url = $_SERVER['PHP_SELF'];   
	$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : 'default_value';
	if ($sortby == 'authors'){
		$query .= " ORDER BY firstAuthor LIMIT $page_start, 25";
		$url .= "?sortby=authors&";
	} elseif ($sortby == 'title'){
		$query .= " ORDER BY title LIMIT $page_start, 25";
		$url .= "?sortby=title&";
	} elseif ($sortby == 'publication'){
		$query .= " ORDER BY publication LIMIT $page_start, 25";
		$url .= "?sortby=publication&";
	} elseif ($sortby == 'year'){
		$query .= " ORDER BY year LIMIT $page_start, 25";
		$url .= "?sortby=year&";
	} elseif ($sortby == 'type'){
		$query .= " ORDER BY type LIMIT $page_start, 25";
		$url .= "?sortby=type&";
	} else {
		$query .= " LIMIT $page_start, 25";
	} 
	
	//display table of the query results
	if ($result = mysqli_query($db,$query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			echo "<tr>
				  <td>" . $row['authors'] . "</td>" .
				 "<td><a href='" . $row['url'] . "' target='_blank'>" . $row['title'] . "</a></td>" .
				 "<td>" . $row['publication'] . "</td>" .
				 "<td>" . $row['year'] . "</td>" .
				 "<td>" . $row['type'] . "</td></tr>" ;
		}
	}
?>
	      </tbody>
	    </table>
	    <div class ='row'>
	     <div class='col-sm-4'></div>
	       <div class='col-sm-4'>
	         <ul class='pagination'>
<?php	
	//estabish if the current view is being sorted or not
	if (empty($_GET['sortby'])){
		$url .= "?";
	}
	
	//set up variables for nav buttons 
	$prevPage = $page - 1;
	$nextPage = $page + 1;
	$activePage = "<li class='active'><a href='" . $url . "page=" . $page . "'>Page " . $page . " </a></li>";
	$activeFirst = "<li><a href='" . $url . "'>First</a></li>";
	$disabledFirst = "<li class='disabled'><a href='#'>First</a></li>";
	$activeLast = "<li><a href='" . $url . "page=" . $pages . "'>Last</a></li>";
	$disabledLast = "<li class='disabled'><a href='#'>Last</a></li>";
	$activePrevious = "<li><a href='" . $url . "page=" . $prevPage . "'>Prev</a></li>";
	$disabledPrevious = "<li class='disabled'><a href='#'>Prev</a></li>";
	$activeNext = "<li><a href='" . $url . "page=" . $nextPage . "'>Next</a></li>";
	$disabledNext = "<li class='disabled'><a href='#'>Next</a></li>";
	
	//determine what page is being viewed, render nav buttons	
	if ($page == $pages){
		echo $activeFirst;
		echo $activePrevious;
		echo $activePage;
		echo $disabledNext;
		echo $disabledLast;
	} elseif ($page == 1 OR (!isset($_GET['page']))) {
		echo $disabledFirst;
		echo $disabledPrevious;
		echo $activePage;
		echo $activeNext;
		echo $activeLast;
	} else {
		echo $activeFirst;
		echo $activePrevious;
		echo $activePage;
		echo $activeNext;
		echo $activeLast;
	}
	mysqli_close($db);
?>		
             </ul>
           </div>
           <div class='col-sm-4'></div>
        </div>
    </div>
  </body>
</html>
