<?php
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>


*/

	header("Content-Type: text/plain");
	ini_set('zlib.output_compression',0);
	ob_start();
	ob_implicit_flush(true);
	ob_end_clean();

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR);

	include "teamNames.php";

	function getHTML($url)
	{
	$html = "";
		try
		{
			$html = file_get_contents($url);
		}
		catch(Exception $err)
		{
			$c = curl_init($url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			$html = curl_exec($c);
			curl_close($c);
		}
	return $html;
	}	
	
	function parsePlayerStats($html)
	{
		$innerHTML = "";
		$dom = new DOMDocument;
		$dom->loadHTML($html);
		$node = $dom->getElementById('info');
			
		foreach ($node->childNodes as $child) 
			$innerHTML .= $node->ownerDocument->saveHTML($child);
		
		$node = $dom->getElementById('other_info');
		foreach ($node->childNodes as $child) 
			$innerHTML .= $node->ownerDocument->saveHTML($child);

		return str_replace("\n\n","\n",preg_replace("(<(.*?)>)is","",preg_replace("(<br>)is","\n",$innerHTML)));
	}
	
	function getTeamIds($teamName)
	{
		$innerHTML = "";
		global $teamNames;
		foreach ($teamNames as $i => $value)
			if (strpos(strtolower($value), strtolower($teamName)) !== FALSE)
				$innerHTML .= $i ."; ".$value."\n";
		return $innerHTML;
	}
	function getTeamPlayers($teamId)
	{
		$innerHTML = "";
		$html = getHTML("http://pesstatsdatabase.com/PSD/Players.php?Club=".intval($teamId));
		$dom = new DOMDocument;
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);

		$nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), 'tb_psd')]//tr//td//img");
		$teamName = str_replace("-"," ",str_replace(".png","",end(explode("/",$nodes[0]->getAttribute("src")))));
		$innerHTML .= $teamName."\n\n";

		$nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), 'tb_psd')]//a");
		foreach($nodes as $node)
			if (strpos($node->getAttribute("href"),'Player.php')!==false)
			{
				$parts = parse_url($node->getAttribute("href"));
				parse_str($parts['query'], $query);
				$innerHTML .= $node->nodeValue."; ".$query['Id']."; \n";
			}
		return $innerHTML;
	}
	
	function findPlayers($name)
	{
		$innerHTML = "";
		$html = getHTML("http://pesstatsdatabase.com/PSD/Search.php?q=".str_replace(" ",'+',$name));
		$dom = new DOMDocument;
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);

		$nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), 'tb_psd')]//a");
		$cnt = 0;
		foreach($nodes as $node)
		{
			if ($i==0)
			{
				$parts = parse_url($node->getAttribute("href"));
				parse_str($parts['query'], $query);
				$innerHTML .= $node->nodeValue."; ".$query['Id']."; ";
				$i=1;
			}
			else
			{
				$parts = parse_url($node->getAttribute("href"));
				parse_str($parts['query'], $query);
				$innerHTML .= $node->nodeValue."; ".$query['Club']."; \n";
				$i=0;
			}
		}
		return $innerHTML;
	
	}

	$version = "Player.php";
	function getPlayerStats($id)
	{
		global $version;
		return parsePlayerStats(getHTML("http://pesstatsdatabase.com/PSD/".$version."?Id=".intval($id)));
	}

	
	if (empty($_GET))
	{
		echo "PSD API\n";
		echo "Usage: \n Get request \n Parameters: \n\n !important! \n * v = ??? - GAME VERSION (INTEGER, 6=pes6, 2018=pes18)\n\n One of rest:\n * p = ??? - playerId to fetch \n * s = ??? - playerName to search\n * t = ??? - teamId to fetch\n * n = ??? - teamName to search\n";
		exit();
		
	}
	
	if (isset($_GET['v']))
	{
		$num = intval($_GET['v']);
		if ($num>2000)  $num-=2000;
		
		if ($num<=13)
			$version = "Player_old2011.php";
		else if ($num==14)
			$version = "Player2014.php";
		else if ($num==15)
			$version = "Player2015.php";
		else if ($num==16)
			$version = "Player2016.php";
		else if ($num==17)
			$version = "Player2017.php";
		else
			$version = "Player.php";
		
	}
	if (isset($_GET['p']))
		echo getPlayerStats($_GET['p']);
	else if (isset($_GET['s']))
		echo findPlayers($_GET['s']);
	else if (isset($_GET['t']))
		echo getTeamPlayers($_GET['t']);
	else if (isset($_GET['n']))
		echo getTeamIds($_GET['n']);
	else	
		echo "Wrong parameter!";

?>
