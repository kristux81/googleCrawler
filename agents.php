<?php

// for windows machine
$win_agents = array( "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.16 Safari/537.36",
                     "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
					 "Mozilla/5.0 (Windows NT 6.1; Intel Mac OS X 10.6; rv:7.0.1) Gecko/20100101 Firefox/7.0.1",
					 "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)",
					 "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
					 "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
					 "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)",
					 "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36 OPR/18.0.1284.68",
					 "Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.16",
					 );
					 
function get_agent()
{
 global $win_agents;
 $agents = $win_agents;
 
 $max = count($agents )- 1;
 $id = rand(0,$max);
 
 //echo " ++++++++++++++++++++++++++ Agent : [" . $agents[$id] . "]\n";
 return $agents[$id];
}
