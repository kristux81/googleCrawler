<?php

// search string for google
$keywords= array( /* 'Engineering Industries in Gujarat',    */ 
                     'industrial automation companies in INDIA',
			     );

//your search domain
$domain = ".com";

// search from page till page on google
$begPage=0;
$endPage=20;

// add more bad words here
$bad_words = array ( "yourname@",
                     "customerservice@",
 					 "spam@",
					 "name@",
					 "enquiries@",
					 "forums@",
				     "theguardian.com",
					 "bookings@",
					 "help",
					 "webmaster@",
					 "support",
					 ".png",
				     ".jpg",
					 ".gif",
					 ".gov",
					 "complaints@",
					 "protection@",
					 "example",
					 "hr@",
					 "naukri",
					 "monster",
					 "facebook",
					 "timesjobs",
					 "abc@",
					 "xyz.com",
					 "justdial",
					 "study",
					 "campus",
                     "shine",
					 "info",
					 "career",
					 "college",
					 "cv@",
					 "student",
					 ".edu.",
					 "education",
					 "recruiter",
					 "xxx",
					 "admission",
					 "news",
					 "about",
					 "telcoma.in",
					 "admin@",
		           );
	
	
// database configuration
$db_name = "Business";
$db_host = "localhost";
$db_login = "root";
$db_pass = "";
$table_ids = "business_ids";
$table_keywords = "keywords";
