<?php
include 'config.php' ;
include 'agents.php' ;

set_time_limit(0);

$execdate = date("h-i_dmY");

//connection to database
$dbo = mysqli_connect($db_host, $db_login, $db_pass, $db_name);

// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit(1);
}

//default search domain
if( empty($domain))
{
      $domain = ".com";
}
	  
foreach($keywords as $keyword)
{
	//add keyword and domain to keywords table
	$sql_add_keyword = "insert into ".$table_keywords." values ('".$keyword."', '".$domain."');";
	mysqli_query($dbo, $sql_add_keyword);
	
	//extract id for each keyword
	$numbers = range($begPage, $endPage);
    shuffle($numbers);
	
    foreach ( $numbers as $page ){
	
	  // random sleep (for 5-15 sec randomly)
		sleep(rand(5,15));

		//crawl starting page
		$content=googleCrawler($keyword, $page, $domain);
		
		//echo $content; die();
		
		//get links from this page
		$urlLinks=getUrlLinks($content);
		
		foreach($urlLinks as $name)
		{
		    $matches = array();
    		$url = $name;
			
			// get page content 
			$string = curl_get_content( $url );
			
			// this regex handles more email address formats like a+b@google.com.sg
			$pattern 	= 	'/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';

			// preg_match_all returns an associative array
			preg_match_all($pattern, $string, $matches);

			// the data you want is in $matches[0], dump it with var_export() to see it
			$matches = array_unique($matches[0]);
			
			//create table in database
			if(count($matches)>0)
			{
				$name_temp	=	substr($url,7);
				$name_temp_array = explode("/", $name_temp);
				$name = $name_temp_array[0];
				
				foreach($matches as $match)
				{
					echo "website: [".$name."]  --->  ID: ".$match;
					
					// skip if it's a bad word
					if (isABadWord($match) || isABadWord($name) ){
					    echo "........... [SKIP BAD]\n";
						continue;
					}
					// skip if it's already there
					elseif(ifIDAlreadyExists($match, $table_ids)){
					    echo "........... [SKIP EXISTING]\n";
						continue;
					}
					// if it seems to be a good word and not added to records yet.
					else{
						$sql = "insert into ".$table_ids." values('".$name."', '".$match."', '".$keyword."' , '".$domain."')";
						mysqli_query($dbo, $sql);
					}
					echo "\n";
				}
			}
		}
	}
}
echo "[DONE]";


// ----------------- API ---------------------------

function isABadWord($word){
global $bad_words;

	foreach( $bad_words as $bad){
		if (strpos(strtolower($word),$bad) !== false) {
			return 'true';
		}
	}
	
	return false ;
}

function ifIDAlreadyExists($id, $tablename)
{
global $dbo;

	$sql = "select * from ".$tablename." where id = '".$id."';";
	$res = mysqli_query($dbo, $sql);
	$i=0;
	while($row=mysqli_fetch_array($res, MYSQL_ASSOC)){
		$i++;
	}
	if($i>0)
		return true;
	else
		return false;
}

function log_content($url, $content)
{
 global $execdate ;

  parse_str(parse_url($url, PHP_URL_QUERY));  
  if(empty($q)){
		$file = "logs/tmp";
  }
  else 
  {
	if (!file_exists("logs/" . $execdate)) {
	  mkdir("logs/" . $execdate, null, true);
	}
	
	if(empty ($start)){
	   $start = '0' ;
	}
	$file = "logs/" . $execdate . "/" . $q . "_" . $start . ".html" ;
  }
  file_put_contents($file, $content);
}

// get content using curl 
function curl_get_content($url,$follow=true)
{
		$curl = curl_init();
		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ( $curl, CURLOPT_USERAGENT, get_agent());
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, $follow);
		
		// debug option
		//curl_setopt ( $curl, CURLINFO_HEADER_OUT, true );
		
		$content = curl_exec($curl);
				
		// debug headers
		//echo curl_getinfo ( $curl );
		
		curl_close($curl);
		
		// debug content
		log_content($url, $content);		
		return $content;
}

function googleCrawler($keyword, $pageNum, $domain)
{	
        echo " >>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Search Page : [" . $pageNum . "] \n";
		if(substr_count($keyword,' ')>0)
			$keywords=str_replace(' ','+',$keyword);
		else 
			$keywords=$keyword;
		
		$url = "http://www.google".$domain."/search?q=".$keywords;
		if( $pageNum > 0) 
		{
		   $index=($pageNum*10);
		   $url .= "&start=".$index;		   
		}
	
		return curl_get_content($url);
}

function getInside($haystack, $start, $end)
{
		$startIndex = strpos($haystack, $start) + strlen($start);
		$endIndex = strpos($haystack, $end);
		$final_string = substr($haystack, $startIndex, $endIndex-$startIndex);
		return $final_string;
}

function extractUrl($result, $content)
{
		$websites = array();
		$string =  $content;
		$pattern 	= 	'/\/url\?q=/';
		preg_match_all($pattern, $string, $matches);
		if (!empty($matches)) {
		  $url = getInside($result, "?q=", "&amp");
		  return ($url != '') ? $url : false;
		}
		return false;
}

function getUrlLinks($content) 
{
		$links = array();
		$html = preg_replace('/\n|\r/', '', $content);
		preg_match("/<li class=\"g\">(.*)<\/li>/", $html, $allOrganic);
		$allOrganic = implode(",", $allOrganic);
		$results = explode("<li class=\"g\">", $allOrganic);
		$length = count($results);
		$i=0;
		 while($i < $length) {
			  $url = extractUrl($results[$i], $content);
			  if ($url && strpos($url, 'http') == 0) {
				$links[]=$url;
			  }
			  $i++;
		}
		return $links;
}
