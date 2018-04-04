<?php
	require 'config.php';
	
	/** 
	 * shortenLink(): generates & stores shortened link for designated url, returns shortened link
	 * 
	 * @param string url to shorten
	 * @return string
	 */
	function shortenLink($url) {
	
		$url = isset($_GET['url']) ? urldecode(trim($_GET['url'])) : '';
		
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
		    die('Enter a URL.');  
		} 
		
	    $db = new MySQLi(mysql_host, mysql_user, mysql_password, mysql_db);
		if ($db->connect_errno) {
		    printf("Connect failed: %s\n", $db->connect_error);
		    exit();
		}

		$db->set_charset('utf8');
		
		$url = $db->real_escape_string($url);
		$result = $db->query('SELECT short_url FROM shortener WHERE real_url = "' . $url . '" LIMIT 1');
		
		// Let's check if it's already been shortened
		if ($result && $result->num_rows > 0) { 
			$existing = $result->fetch_object()->short_url;

			echo $url . " has already been shortened to: " . domain."/".$existing;
			exit();
		
		// If not, let's generate it
		} else {
	        $short_url = getShortURL($db);

		    $query = 'INSERT INTO shortener (short_url, real_url, visits, date_added) VALUES ("' . $short_url . '", "' . $url . '", 0, NOW())';
	        $res = $db->query($query);

	        if ($res) {
	        	$link = domain."/".$short_url;
	            echo "Your shortlink: " . $link;
	     	} else {
	        	echo "Failed to store shortlink in database.";
	        }
		}

       	$db->close();
	}


	/** 
	 * getShortURL(): generate random substring of len: short_len for shortened link
	 * 
	 * @param object $db
	 * @return string shortlink ext
	 */
	function getShortURL($db) {
	    //use urls last part as is for short code
	    $short_link = substr(str_shuffle(short_characters), 0, short_len);

	    if (!shortExists($db, $short_link)) {
	    	return $short_link;
		} else {
			getShortURL(); // try again, hehe
		}
	}


	/**
	 * shortExists(): returns true if selected short url exists
	 * 
	 * @param object $db, string $short
	 * @return boolean
	 */
	function shortExists($db, $short) {
	    $query = 'SELECT COUNT(*) FROM shortener WHERE short_url = "' . $short . '"';
	    $res = $db->query($query);
	    
	    $rows = $res->fetch_array();
	    
	    return  $rows[0] === 1;
	}
?>