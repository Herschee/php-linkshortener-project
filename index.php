<?php 
	// - MARK: index.php
	// Author: Henry Wrightman
	// URL Shortner - WonderfulLife Project

	require 'config.php';
	require 'lib.php';

	if (isset($_GET['ext'])) {
		//
		// Handle shortened link & redirect
		//
	    
	    $ext = strip_tags(strtolower($_GET['ext']));
	    $db = new MySQLi(mysql_host, mysql_user, mysql_password, mysql_db);
		if ($db->connect_errno) {
		    printf("Connect failed: %s\n", $db->connect_error);
		    exit();
		}
	    
	    $db->set_charset('utf8');
	    
	    $escapedExt = $db->real_escape_string($ext);
	    
	    // query for redir link
	    $redirRes = $db->query('SELECT real_url FROM shortener WHERE short_url = "' . $escapedExt . '"');
	    
	    if ($redirRes && $redirRes->num_rows > 0) {
	    	// update visit count
	        $query = 'UPDATE shortener SET visits = visits + 1 WHERE short_url = "' . $escapedExt . '"';

	        $res = $db->query($query);
	       
	        $redir = $redirRes->fetch_object()->real_url;
	    } else {
	        die("Shortened link doesn't exist!");
	    }

	    // route to link
	    header('Location: ' . $redir);

	    // close
	    $db->close();
	    exit();
	
	} else if (isset($_GET['url'])) {
		// 
		// Generate shortened link from form 
		// 

		echo shortenLink($_GET['url']);

	} else if (isset($_GET['ext'] && isset($_GET['stats']))) {

		if ($_GET['stats'] == 'visits') {
			$visits = getVisits($_GET['ext']);

			if ($visits >= 0) {
				echo "This link has been visited " . $visits . " times!";
			} else {
				echo "This shortlink does not exist!";
			}
		}

	} else {
		// 
		// Shortener form
		//

	    $db = new MySQLi(mysql_host, mysql_user, mysql_password, mysql_db);
		if ($db->connect_errno) {
		    printf("Connect failed: %s\n", $db->connect_error);
		    exit();
		}

		echo file_get_contents("shorten_form.html");
	}
?>