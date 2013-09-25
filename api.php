<?php

/* Echonest API key: CRKOKPGH9EAVT0DHX  */
/* Last.FM API key:  a54b38c820b48ef1790f3f266ed4e739 */
/* Tastekid API:     f: musica2239  k: ntc3nzywmwey */
/* Please don't abuse these and get me banned */

$q=urlencode($_POST["q"]);

if (empty($q)) {

	echo "<br>You need to enter an artist first! Durrr.";
	
} else {

	$echonest_ok = True;
	$tastekid_ok = True;
	$lastfm_ok = True;

	$echonest_search = @file_get_contents("http://developer.echonest.com/api/v4/artist/search?api_key=CRKOKPGH9EAVT0DHX&format=json&name=" . $q . "&results=1");
	if ($echonest_search === FALSE) 
	{
		$echonest_ok = False;
	} else {
		$echonest_search_decode = json_decode($echonest_search, true);
		$echonest_id = $echonest_search_decode["response"]["artists"][0]["id"];
	}

	if (empty($echonest_id)) {
		$echonest_ok = False;
	} else {
		$echonest = @file_get_contents("http://developer.echonest.com/api/v4/artist/similar?api_key=CRKOKPGH9EAVT0DHX&id=" . $echonest_id . "&format=json&results=10&start=0");
	}
	$echonest_decode = json_decode($echonest, true);
	if (empty($echonest_decode["response"]["artists"][0]["name"])) 
	{
		$echonest_ok = False;
	}

	
	
	$tastekid = @file_get_contents("http://www.tastekid.com/ask/ws?q=" . $q . "&format=JSON&f=musica2239&k=ntc3nzywmwey");
	if ($tastekid === FALSE) 
	{
		$tastekid_ok = False;
	} else {	
		$tastekid_decode = json_decode($tastekid, true);
	}
	
	if (empty($tastekid_decode["Similar"]["Results"][0]["Name"])) 
	{
		$tastekid_ok = False;
	}
	
	
	$lastfm = @file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getsimilar&artist=" . $q . "&api_key=a54b38c820b48ef1790f3f266ed4e739&format=json");
	if ($lastfm === FALSE) 
	{
		$lastfm_ok = False;
	} else {
		$lastfm_decode = json_decode($lastfm, true);
	}
	if (empty($lastfm_decode["similarartists"]["artist"][0]["name"])) {
		$lastfm_ok = False;
	}
	
	/* LastFM array */
	$lastfm_array = array();
	$weight = 100;
	if ($lastfm_ok) {
		for ($i=1; $i<=10; $i++)
		{
			$lastfm_array[($i-1)] = array("sanitized" => strtolower($lastfm_decode["similarartists"]["artist"][$i]["name"]), "original" => $lastfm_decode["similarartists"]["artist"][$i]["name"], "weight" => $weight, "color" => "blank");
			$weight -= 7;
		}
	}
	/* Echonest array */
	$echonest_array = array();
	$weight = 100;
	
	if ($echonest_ok) {
		for ($i=0; $i<=9; $i++)
		{
			$echonest_array[$i] = array("sanitized" => strtolower($echonest_decode["response"]["artists"][$i]["name"]), "original" => $echonest_decode["response"]["artists"][$i]["name"], "weight" => $weight, "color" => "blank");
		$weight -= 7;
		}
	}

	/* Tastekid array */
	$tastekid_array = array();
	$weight = 100;
	if ($tastekid_ok) {
	for ($i=0; $i<=9; $i++)
		{
			$tastekid_array[$i] = array("sanitized" => strtolower($tastekid_decode["Similar"]["Results"][$i]["Name"]), "original" => $tastekid_decode["Similar"]["Results"][$i]["Name"], "weight" => $weight, "color" => "blank");
		$weight -= 7;
		}
	}
	/* Colors */
	$colors = array(
	1 => "background-color:rgba(153, 0, 0, 0.5)",
	2 => "background-color:rgba(255, 102, 102, 0.5)",
	3 => "background-color:rgba(153, 76, 0, 0.5)",
	4 => "background-color:rgba(255, 128, 0, 0.5)",
	5 => "background-color:rgba(255, 255, 0,0.5)",
	6 => "background-color:rgba(0, 102, 0,0.5)",
	7 => "background-color:rgba(153, 255, 51,0.5)",
	8 => "background-color:rgba(153, 255, 204,0.5)",
	9 => "background-color:rgba(100,102,102,0.5)",
	10 => "background-color:rgba(0,204,204,0.5)",
	11 => "background-color:rgba(51, 51, 255,0.5)",
	12 => "background-color:rgba(204,0,204,0.5)",
	13 => "background-color:rgba(153,21,255,0.5)",
	14 => "background-color:rgba(255,102,178,0.5)",
	15 => "background-color:rgba(36,102,102,0.5)",
	16 => "background-color:rgba(255,215,0, 0.5)",
	17 => "background-color:rgba(204,6,51,0.5)",
	18 => "background-color:rgba(255,69,0,0.5)",
	19 => "background-color:rgba(107,142,35,0.5)",
	20 => "background-color:rgba(152,251,152,0.5)",
	21 => "background-color:rgba(102,205,170,0.5)",
	22 => "background-color:rgba(222,184,135,0.5)",
	23 => "background-color:rgba(139,69,19,0.5)",
	24 => "background-color:rgba(30,144,255,0.5)",
	25 => "background-color:rgba(175,238,238,0.5)",
	26 => "background-color:rgba(139,0,139,0.5)",
	27 => "background-color:rgba(255,0,255,0.5)",
	28 => "background-color:rgba(255,228,196,0.5)",
	29 => "background-color:rgba(188,143,143,0.5)",
	30 => "background-color:rgba(255,228,225,0.5)"
	);
	
	if ($echonest_ok && $tastekid_ok && $lastfm_ok) {
	
		$combined = array();
		foreach ($lastfm_array as &$name) {
			if (!isset($combined[$name['sanitized']])) {
				$combined[$name['sanitized']] = array(
					'original' => $name['original'],
					'weight'   => $name['weight'],
					'color'    => array_pop($colors),
					'count'    => 1,
				);
			} elseif ($combined[$name['sanitized']]['count'] < 3) {
				$combined[$name['sanitized']]['weight'] += $name['weight'];
				$combined[$name['sanitized']]['count']++;
			}
			$name['color'] = $combined[$name['sanitized']]['color'];
		}

		foreach ($echonest_array as &$name) {
			if (!isset($combined[$name['sanitized']])) {
				$combined[$name['sanitized']] = array(
					'original' => $name['original'],
					'weight'   => $name['weight'],
					'color'    => array_pop($colors),
					'count'    => 1,
				);
			} elseif ($combined[$name['sanitized']]['count'] < 3) {
				$combined[$name['sanitized']]['weight'] += $name['weight'];
				$combined[$name['sanitized']]['count']++;
			}
			$name['color'] = $combined[$name['sanitized']]['color'];
		}

		foreach ($tastekid_array as &$name) {
			if (!isset($combined[$name['sanitized']])) {
				$combined[$name['sanitized']] = array(
					'original' => $name['original'],
					'weight'   => $name['weight'],
					'color'    => array_pop($colors),
					'count'    => 1,
				);
			} elseif ($combined[$name['sanitized']]['count'] < 3) {
				$combined[$name['sanitized']]['weight'] += $name['weight'];
				$combined[$name['sanitized']]['count']++;
			}
			$name['color'] = $combined[$name['sanitized']]['color'];
		}


		foreach ($lastfm_array as &$name) {
			if ($combined[$name['sanitized']]['count'] === 1) {
				$combined[$name['sanitized']]['color'] = $name['color'] = 'blank';
			}
		}

		foreach ($echonest_array as &$name) {
			if ($combined[$name['sanitized']]['count'] === 1) {
				$combined[$name['sanitized']]['color'] = $name['color'] = 'blank';
			}
		}

		foreach ($tastekid_array as &$name) {
			if ($combined[$name['sanitized']]['count'] === 1) {
				$combined[$name['sanitized']]['color'] = $name['color'] = 'blank';
			}
		}

		function sortByOrder($a, $b) {
			return $b['weight'] - $a['weight'];
		}

		usort($combined, 'sortByOrder');
	}
	
	echo "<br><table cellpadding=\"1\">
	<tr>
	<th><img src=\"/images/lastfm.gif\" style=\"width:100%;\"></th>
	<th><img src=\"/images/echonest.png\" style=\"width:100%;\"></th>
	<th><img src=\"/images/tastekid.jpg\" style=\"width:100%;\"></th>
	<th>Aggregate</th>
	</tr>";

	
	for ($k=0; $k<=9; $k++)
	{
	echo "<tr>";
	/* The API is down, the artist could not be found, or the API key limit has been reached. I am too lazy to figure out which one it is. */
	if ($lastfm_ok)
	{
		echo "<td" . ($lastfm_array[$k]["color"] == "blank" ? ">" : (" style=\"" . $lastfm_array[$k]["color"] . "\">") ) . $lastfm_array[$k]["original"] . "</td>";
	} elseif ($k==0) {
		echo "<td>API is down or artist could not be found.</td>";
	} else {
		echo "<td>-</td>";
	}
	
	
	if ($echonest_ok)
	{
		echo "<td" . ($echonest_array[$k]["color"] == "blank" ? ">" : (" style=\"" . $echonest_array[$k]["color"] . "\">") ) . $echonest_array[$k]["original"]  . "</td>";
	} elseif ($k==0) {
		echo "<td>API is down or artist could not be found.</td>";
	} else {
		echo "<td>-</td>";
	}
	
	if ($tastekid_ok)
	{
		echo "<td" . ($tastekid_array[$k]["color"] == "blank" ? ">" : (" style=\"" . $tastekid_array[$k]["color"] . "\">") ) . $tastekid_array[$k]["original"]  . "</td>";
	} elseif ($k==0) {
		echo "<td>API is down or artist could not be found.</td>";
	} else {
		echo "<td>-</td>";
	}
	
	if ($echonest_ok && $tastekid_ok && $lastfm_ok) {
		echo "<td>" . $combined[$k]["original"] . "</td>";
	} elseif ($k==0) {
		echo "<td>Not enough data to aggregate.</td>";
	}
		echo "</tr>";
	}
}

?>
