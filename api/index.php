<?php 
require_once("DB.php");


$db = new DB('127.0.0.1', 'dbname', 'username', 'password');


if ($_SERVER['REQUEST_METHOD'] == "GET") {
	 if ($_GET['url'] == "musers") {
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$users = $db->query("SELECT DISTINCT s.username AS Sender, r.username AS Receiver, s.id AS SenderID, r.id AS ReceiverID, s.profileimg AS SenderImg, r.profileimg AS ReceiverImg FROM messages LEFT JOIN users s ON s.id=messages.sender LEFT JOIN users r ON r.id=messages.receiver WHERE (s.id=:userid OR r.id=:userid)",array(":userid"=>$userid));
		$u = array();
		foreach ($users as $user) {
				if (!in_array(array('username'=>$user['Receiver'],'id'=>$user['ReceiverID'],'profileimg'=>$user['ReceiverImg']), $u)) {
						array_push($u, array('username'=>$user['Receiver'],'id'=>$user['ReceiverID'],'profileimg'=>$user['ReceiverImg']));
					}
				if (!in_array(array('username'=>$user['Sender'],'id'=>$user['SenderID'],'profileimg'=>$user['SenderImg']), $u)) {
						array_push($u, array('username'=>$user['Sender'],'id'=>$user['SenderID'],'profileimg'=>$user['SenderImg'])); 
				}
			
			}
		echo json_encode($u);
	} else if ($_GET['url'] == "musers3") {
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$ms = $db->query('SELECT DISTINCT s.username AS Sender, r.username AS Receiver, s.id AS SenderID, r.id AS ReceiverID, s.profileimg AS SenderImg, r.profileimg AS ReceiverImg, messages.read FROM messages LEFT JOIN users s ON s.id=messages.sender LEFT JOIN users r ON r.id=messages.receiver WHERE (s.id=:userid OR r.id=:userid)',array(":userid"=>$userid));

$msg = array();
		foreach ($ms as $m) {
				if (!in_array(array('rusername'=>$m['Receiver'],'rid'=>$m['ReceiverID'], 'read'=>$m['read'], 'profileimg'=>$m['ReceiverImg']), $msg)) {
						array_push($msg, array('rusername'=>$m['Receiver'],'rid'=>$m['ReceiverID'], 'read'=>$m['read'],'profileimg'=>$m['ReceiverImg']));
					}
				if (!in_array(array('susername'=>$m['Sender'],'sid'=>$m['SenderID'], 'read'=>$m['read'],'profileimg'=>$m['SenderImg']), $msg)) {
						array_push($msg, array('susername'=>$m['Sender'],'sid'=>$m['SenderID'], 'read'=>$m['read'],'profileimg'=>$m['SenderImg'])); 
				}
			
			}
			
			echo json_encode($msg);
		} else if ($_GET['url'] == "auth") {
		
	} else if ($_GET['url'] == "messages") {
		$sender = $_GET['sender'];
		
		$token = $_COOKIE['SNID'];
		
		$receiver = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$messages = $db->query('SELECT messages.id, messages.body, messages.msg_date, s.username AS Sender, r.username AS Receiver FROM messages LEFT JOIN users s ON messages.sender = s.id LEFT JOIN users r ON messages.receiver = r.id WHERE (r.id=:r AND s.id=:s) OR r.id=:s AND s.id=:r', array(':r'=>$receiver, ':s'=>$sender));
		echo json_encode($messages);
		
	} else if ($_GET['url'] == "messages2") {
		$sender = $_GET['sender'];
		
		$token = $_COOKIE['SNID'];
		
		$receiver = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$lastmsgs = $db->query('SELECT id, receiver, sender, body, messages.read, msg_date FROM messages WHERE (sender, receiver, msg_date) IN (SELECT sender, receiver, MAX(msg_date) FROM messages WHERE ((sender=:sender AND receiver=:receiver) OR (sender=:receiver AND receiver=:sender)) GROUP BY receiver)', array(':receiver'=>$receiver, ':sender'=>$sender));
		
		
		
		$lastmsg = array();
		foreach ($lastmsgs as $lastm) {
			if (strlen($lastm['body']) > 26) {
			$lmsg = substr($lastm['body'], 0, 26)."...";	
		
		} else {
			$lmsg = $lastm['body'];	
		}
				if (!in_array(array('receiver'=>$lastm['receiver'], 'sender'=>$lastm['sender'],'id'=>$lastm['id'], 'body'=>$lmsg, 'read'=>$lastm['read']), $lastmsg)) {
						array_push($lastmsg, array('receiver'=>$lastm['receiver'], 'sender'=>$lastm['sender'],'id'=>$lastm['id'], 'body'=>$lmsg, 'read'=>$lastm['read'], 'msg_date'=>$lastm['msg_date']));
					}
			
			}
			
		
		echo json_encode($lastmsg);
		
	} else if ($_GET['url'] == "messages3") {
		$sender = $_GET['sender'];
		
		$token = $_COOKIE['SNID'];
		
		$receiver = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$messages = $db->query('SELECT messages.id, messages.body, messages.read, messages.msg_date, s.username AS Sender, r.username AS Receiver FROM messages LEFT JOIN users s ON messages.sender = s.id LEFT JOIN users r ON messages.receiver = r.id WHERE (r.id=:r AND s.id=:s) OR r.id=:s AND s.id=:r', array(':r'=>$receiver, ':s'=>$sender));
		echo json_encode($messages);
		
	} else if ($_GET['url'] == "filterstores") {
		$category = $_GET['category'];
		$sortBy = $_GET['sortBy'];

		if ($category == 'null') {} 
		
	} else if ($_GET['url'] == "searchpage") {
		
		
	$tosearch = explode(" ", $_GET['query']);
	if (count($tosearch) == 1) {
			$tosearch = str_split($tosearch[0], 2);
		}
	
	
	$whereclause = "";
	$paramsarray = array(':body'=>'%'.$_GET['query'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
			if ($i % 2) {
			if ($_GET['mfilter'] == "") {
				$whereclause .= "  OR body LIKE :p$i";

			} else if ($_GET['mfilter'] == "top") {
				$whereclause .= "  OR body LIKE :p$i";

			} else if ($_GET['mfilter'] == "latest") {
				$whereclause .= "  OR body LIKE :p$i";

			} else if ($_GET['mfilter'] == "people") {
				$whereclause .= "  OR username LIKE :p$i";

			} else if ($_GET['mfilter'] == "skills") {
				$whereclause .= "  OR skills.skill_name LIKE :p$i";

			} else if ($_GET['mfilter'] == "picture") {
				$whereclause .= "  OR body LIKE :p$i";

			}
			$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
	//when changing subject and grade remember to change  posts.subject and  posts.grade below to the relevant category and industy or job type or whatever you will change it to
	if ($_GET['mfilter'] == "") {
		$start = (int)$_GET['start'];
		$posts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.user_id, posts.likes, posts.subject,  posts.grade, posts.postimg, users.username, users.profileimg FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body '.$whereclause.' LIMIT 5 OFFSET '.$start.'', $paramsarray);
		//echo '<pre>';
		echo json_encode($posts);
		//echo '</pre>';
	} else if ($_GET['mfilter'] == "top") {
		$start = (int)$_GET['start'];
		$posts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.user_id, posts.likes, posts.subject,  posts.grade, posts.postimg, users.username, users.profileimg FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body '.$whereclause.' ORDER BY posts.likes DESC LIMIT 5 OFFSET '.$start.'', $paramsarray);
		//echo '<pre>';
		echo json_encode($posts);
		//echo '</pre>';
	} else if ($_GET['mfilter'] == "latest") {
		$start = (int)$_GET['start'];
		$posts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.user_id, posts.likes, posts.subject,  posts.grade, posts.postimg, users.username, users.profileimg FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body '.$whereclause.' ORDER BY posts.posted_at DESC LIMIT 5 OFFSET '.$start.'', $paramsarray);
		//echo '<pre>';
		echo json_encode($posts);
		//echo '</pre>';
	} else if ($_GET['mfilter'] == "people") {
		$start = (int)$_GET['start'];
		//AVG(rate) as rating FROM star_rating WHERE rated_user_id =:business_id
		$posts = $db->query('SELECT users.id, users.username, users.category, users.industry, users.profileimg, users.coverimg FROM users WHERE  users.username LIKE :body '.$whereclause.' ORDER BY users.username DESC LIMIT 5 OFFSET '.$start.'', $paramsarray);
		//echo '<pre>';
		echo json_encode($posts);
		//echo '</pre>';
	} else if ($_GET['mfilter'] == "skills") {
		$start = (int)$_GET['start'];
		$posts = $db->query('SELECT skill_lookup.skill_id, skills.skill_name, skill_lookup.user_id, users.id, users.industry, users.category, users.username, users.profileimg FROM skill_lookup, users, skills WHERE skill_lookup.user_id = users.id AND skill_lookup.skill_id = skills.id AND skills.skill_name LIKE :body '.$whereclause.' LIMIT 5 OFFSET '.$start.'', $paramsarray);
		//echo '<pre>';
		echo json_encode($posts);
		//echo '</pre>';
	} else if ($_GET['mfilter'] == "picture") {
		$start = (int)$_GET['start'];
		$posts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.user_id, posts.likes, posts.subject,  posts.grade, posts.postimg, users.username, users.profileimg FROM posts, users WHERE users.id = posts.user_id AND posts.postimg !="" AND posts.body LIKE :body '.$whereclause.' LIMIT 5 OFFSET '.$start.'', $paramsarray);
		//echo '<pre>';
		echo json_encode($posts);
		//echo '</pre>';
	}
	
	} else if ($_GET['url'] == "search") {
		
		
	$tosearch = explode(" ", $_GET['query']);
	if (count($tosearch) == 1) {
			$tosearch = str_split($tosearch[0], 2);
		}
	
	
	$whereclause = "";
	$paramsarray = array(':body'=>'%'.$_GET['query'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
			if ($i % 2) {
			$whereclause .= "  OR body LIKE :p$i";
			$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
	//when changing subject and grade remember to change  posts.subject and  posts.grade below to the relevant category and industy or job type or whatever you will change it to
	$posts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.user_id, posts.likes, posts.subject,  posts.grade, posts.postimg, users.username, users.profileimg FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body '.$whereclause.' LIMIT 5', $paramsarray);
	//echo '<pre>';
	echo json_encode($posts);
	//echo '</pre>'; 
	
	} else if ($_GET['url'] == "msgsearch") {
		
		
	$tosearch = explode(" ", $_GET['query']);
	if (count($tosearch) == 1) {
			$tosearch = str_split($tosearch[0], 2);
		}
	
	
	$whereclause = "";
	$paramsarray = array(':users'=>'%'.$_GET['query'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
			if ($i % 2) {
			$whereclause .= "  OR username LIKE :p$i";
			$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
	
	$posts = $db->query('SELECT users.id, users.username, users.profileimg, users.verified FROM users WHERE users.username LIKE :users '.$whereclause.' LIMIT 5', $paramsarray);
	//echo '<pre>';
	echo json_encode($posts);
	//echo '</pre>'; 
	
	} else if ($_GET['url'] == "storesearch") {
		
	$sboxfilter = $_GET['sboxfilter'] ?? 'store';

	if ($sboxfilter =='store') {
	
	$tosearch = explode(" ", $_GET['query']);
	if (count($tosearch) == 1) {
			$tosearch = str_split($tosearch[0], 2);
		}
	
	
	$whereclause = "";
	$paramsarray = array(':stores'=>'%'.$_GET['query'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
			if ($i % 2) {
			$whereclause .= "  OR store_name LIKE :p$i";
			$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
	
	$posts = $db->query('SELECT stores.id, stores.store_name, stores.store_profimage, stores.store_coverimage, stores.store_type, stores.verified FROM stores WHERE stores.store_name LIKE :stores '.$whereclause.' LIMIT 5', $paramsarray);
	
	echo json_encode($posts);

	} else if ($sboxfilter == 'product') {
	$tosearch = explode(" ", $_GET['query']);
	if (count($tosearch) == 1) {
			$tosearch = str_split($tosearch[0], 2);
		}
	
	
	$whereclause = "";
	$paramsarray = array(':products'=>'%'.$_GET['query'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
			if ($i % 2) {
			$whereclause .= "  OR product_title LIKE :p$i";
			$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
	
	$posts = $db->query('SELECT stores.id AS storeid, stores.store_name, products.id AS productid, products.product_title, products.oldprice, products.price, products.product_image, products.product_description, products.product_type FROM stores, products WHERE stores.id = products.store_id AND products.product_title LIKE :products '.$whereclause.' LIMIT 5', $paramsarray);
	
	echo json_encode($posts);	
		
	} 
	
	} else if ($_GET['url'] == "productsearch") {
		
		
	$tosearch = explode(" ", $_GET['query']);
	if (count($tosearch) == 1) {
			$tosearch = str_split($tosearch[0], 2);
		}
	
	
	$whereclause = "";
	$paramsarray = array(':products'=>'%'.$_GET['query'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
			if ($i % 2) {
			$whereclause .= "  OR product_title LIKE :p$i";
			$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
	
	$posts = $db->query('SELECT products.id, products.product_title, products.oldprice, products.price, product.product_image, product.product_description, product.product_type FROM products WHERE product.product_title LIKE :products '.$whereclause.' LIMIT 5', $paramsarray);
	
	echo json_encode($posts);
	 
	
	} else if ($_GET['url'] == "cartproducts") {
		$store_name = $_GET['storename'];
		//$token = $_COOKIE['SNID'];
		$output = "";

		//$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$store_id = $db->query('SELECT id FROM stores WHERE store_name=:storename', array(':storename'=>$store_name))[0]['id'];
		$cartproducts = $db->query('SELECT id, product_title AS title, oldprice, price, product_image AS image FROM products WHERE store_id=:storeid', array(':storeid'=>$store_id));
		$output .= "[";
			foreach($cartproducts as $cart) {
				
				$output .= "{";
					$output .= '"id": '.$cart['id'].',';
					$output .= '"title": "'.$cart['title'].'",';
					$output .= '"oldprice": '.$cart['oldprice'].',';
					$output .= '"price": '.$cart['price'].',';
					$output .= '"image": "'.$cart['image'].'"';
					
					
				$output .= "},";
				
				}
		
		
		$output = substr($output, 0, strlen($output)-1);
		
		$output .= "]";

		echo json_encode($cartproducts);
		//echo json_encode($output);
	} else if ($_GET['url'] == "loadstores") {
		$sort = $_GET['sort'] ?? 'all';
		$filter = $_GET['filter'] ?? 'all';
		//$token = $_COOKIE['SNID'];
		$output = "";

		//$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		//$store_id = $db->query('SELECT id FROM stores WHERE store_name=:storename', array(':storename'=>$store_name))[0]['id'];
		//sort
		if ($sort == 'all') {
			
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY creation_date ASC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY creation_date ASC', array(':storetype'=>$filter));
			}
		} else if ($sort =='bestpicks') {
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY bestpicks DESC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY bestpicks DESC', array(':storetype'=>$filter));
			}
		} else if ($sort =='newest') {
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY creation_date DESC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY creation_date DESC', array(':storetype'=>$filter));
			}
		} else if ($sort =='highestrated') {
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY creation_date DESC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY creation_date DESC', array(':storetype'=>$filter));
			}
		} else if ($sort =='verified') {
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY verified DESC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY verified DESC', array(':storetype'=>$filter));
			}
		} else if ($sort =='nearme') {
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY creation_date DESC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY creation_date DESC', array(':storetype'=>$filter));
			}
		} else if ($sort =='onsale') {
			if ($filter == 'all') {
				$allstores = $db->query('SELECT * FROM stores ORDER BY onsale DESC');
			} else {
				$allstores = $db->query('SELECT * FROM stores WHERE store_type LIKE :storetype ORDER BY onsale DESC', array(':storetype'=>$filter));
			}
		}
		
		

		$output = "[";
			foreach($allstores as $stores) {
				$user_id = $stores['user_id'];
				$username = $db->query('SELECT username FROM users WHERE id=:uid', array(':uid'=>$user_id))[0]['username'];
				$output .= "{";
					$output .= '"StoreId": '.$stores['id'].',';
					$output .= '"OwnerId": "'.$stores['user_id'].'",';
					$output .= '"OwnerName": "'.$username.'",';
					$output .= '"StoreName": "'.$stores['store_name'].'",';
					$output .= '"ProfileImg": "'.$stores['store_profimage'].'",';
					$output .= '"StoreType": "'.$stores['store_type'].'",';
					$output .= '"CreationDate": "'.$stores['creation_date'].'",';
					$output .= '"StoreImg": "'.$stores['store_coverimage'].'",';
					$output .= '"Verified": '.$stores['verified'].'';
					
					
				$output .= "},";
				
				}
		
		
		$output = substr($output, 0, strlen($output)-1);
		
		$output .= "]";

		//echo json_encode($allstores);
		http_response_code(200);
		echo $output;
	} else if ($_GET['url'] == "users") {
		
		$token = $_COOKIE['SNID'];
		
		$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$username = $db->query('SELECT username FROM users WHERE id=:uid', array(':uid'=>$user_id))[0]['username'];
		echo $username;
	
	} else if ($_GET['url'] == "userid") {
		
		$token = $_COOKIE['SNID'];
		
		$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		echo $user_id;
	
	} else if ($_GET['url'] == "userprof") {
		
		$token = $_COOKIE['SNID'];
		
		$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$userprof = $db->query('SELECT * FROM users WHERE id=:uid', array(':uid'=>$user_id));
		
		echo json_encode($userprof);
	
	} else if ($_GET['url'] == "num_follow") {
		$username = $_GET['username'];
		
		
		
		$user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];

		$num_followers = $db->query('SELECT * FROM followers WHERE user_id=:uid', array(':uid'=>$user_id));
		$num_followers = sizeof($num_followers);
		$num_following = $db->query('SELECT * FROM followers WHERE follower_id=:uid', array(':uid'=>$user_id));
		$num_following = sizeof($num_following);

		echo '{"Followers":'.$num_followers.',"Following":'.$num_following.'}';
		//echo json_encode($userprof);
	
	} else if ($_GET['url'] == "fulluserprof") {
		
		$token = $_COOKIE['SNID'];
		$response = "";
		$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$userprofinfo = $db->query('SELECT profiles.id,profiles.user_id,profiles.first_name,profiles.last_name,profiles.user_location,profiles.works_at,users.id,users.email,users.profileimg,users.coverimg FROM profiles,users WHERE profiles.user_id=:uid AND users.id=:uid', array(':uid'=>$user_id));
		//if ($userprofinfo) {
			$response = "[";
				foreach($userprofinfo as $userprof) {
					
	
					$response .= "{";
						$response .= '"ProfId": '.$userprof['id'].',';
						$response .= '"ProfUserId": '.$userprof['user_id'].',';
						$response .= '"ProfFname": "'.$userprof['first_name'].'",';
						$response .= '"ProfLname": "'.$userprof['last_name'].'",';
						$response .= '"ProfLocation": "'.$userprof['user_location'].'",';
						$response .= '"ProfWorksAt": "'.$userprof['works_at'].'",';
						$response .= '"ProfEmail": "'.$userprof['email'].'",';
						$response .= '"ProfileImage": "'.$userprof['profileimg'].'",';
						$response .= '"CoverImage": "'.$userprof['coverimg'].'",';
						$response .= '"ProfFJobName": "'.$userprof['coverimg'].'",';
						$response .= '"ProfSJobName": "'.$userprof['coverimg'].'",';
						$response .= '"ProfLJobName": "'.$userprof['coverimg'].'",';
						$response .= '"ProfHSName": "'.$userprof['coverimg'].'",';
						$response .= '"ProfUGName": "'.$userprof['coverimg'].'",';
						$response .= '"ProfPGName": "'.$userprof['coverimg'].'",';
						$response .= '"ProfFacebook": "'.$userprof['coverimg'].'",';
						$response .= '"ProfWhatsApp": "'.$userprof['coverimg'].'",';
						$response .= '"ProfLinkedIn": "'.$userprof['coverimg'].'"';
						//$response .= '"SkillName": "'.$userprof['skill_name'].'"';
						//$response .= '"SkillDescription": "'.$userprof['skill_description'].'",';
						//$response .= '"SkillImage": "'.$userprof['skill_image'].'"';	
					$response .= "},";

	
	
					}
					$response = substr($response, 0, strlen($response)-1);
					$response .= "]";
	
					http_response_code(200);
					echo $response;
			
			//echo json_encode($userprofinfo);
		//} else if(!$userprofinfo) {
		//	echo json_encode($response);
		//} 
		
	
	} else if ($_GET['url'] == "userskills") {
		$token = $_COOKIE['SNID'];
		$response = "";
		$user_id = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$skill_lookup = $db->query('SELECT skill_lookup.skill_id FROM skill_lookup WHERE user_id=:uid', array(':uid'=>$user_id)); 
		$lookup_id = $db->query('SELECT skill_lookup.id FROM skill_lookup WHERE user_id=:uid', array(':uid'=>$user_id));

		$skill_lookup = array_column($skill_lookup,'skill_id');
		$lookup_id = array_column($lookup_id,'id');

		//print_r($skill_lookup);
		$response = "[";
		//foreach($skill_lookup as $lookup) {
			
		//	$skillchecked = $db->query('SELECT skills.id, skills.skill_name, skills.skill_image, skills.skill_description FROM skills WHERE skills.id=:skillid', array(':skillid'=>$lookup));
			
		//}
		$i=0;
		$limitcount = 0;
		$lookup = 0;
		$skillsinfo = $db->query('SELECT skills.id, skills.skill_name, skills.skill_image, skills.skill_description FROM skills');
		$length = count($skillsinfo);	
			foreach($skillsinfo as $skills) {
				
				//checking if array index(or key) exists then if it does exist it should define ischecked as checked and if not it should define ischecked as an empty string so we can place the result in the json response
				if (array_key_exists($i,$skill_lookup)) {
					$lookup = $lookup_id[$i];
					$ischecked = "checked";
					$limitcount = $i+1;
				} else {
					$ischecked = "";
					$lookup = 0;
				}
				$i++;
					$response .= "{";
						$response .= '"SkillID": '.$skills['id'].',';
						$response .= '"SkillLookupID": '.$lookup.',';
						$response .= '"SkillName": "'.$skills['skill_name'].'",';
						$response .= '"SkillImage": "'.$skills['skill_image'].'",';
						$response .= '"SkillDescription": "'.$skills['skill_description'].'",';
						$response .= '"SkillChecked": "'.$ischecked.'",';
						$response .= '"SkillCount": '.$limitcount.'';
					$response .= "},";

			}
			
		
		$response = substr($response, 0, strlen($response)-1);
		$response .= "]";
		http_response_code(200);
		echo $response;	
	} else if ($_GET['url'] == "comments" && isset($_GET['postid'])) {
		$output = "";
		$postID = $_GET['postid'];
		$comments = $db->query('SELECT comments.id, comments.comment, users.username, users.profileimg, comments.posted_at, comments.commentimg, comments.likes  FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid'=>$_GET['postid']));
		
		$cmexist = $db->query('SELECT posts.commented FROM posts WHERE id =:postid', array(':postid'=>$postID))[0]['commented'];
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		if ($cmexist == 1) {
			
		$output .= "[";
			foreach($comments as $comment) {
				$iLiked = 1;
				if (!$db->query('SELECT comment_id FROM comment_likes WHERE comment_id=:commentid AND user_id=:userid',array(':commentid'=>$comment['id'],':userid'=>$userid))){
				$iLiked = 0;
				}
				$output .= "{";
					$output .= '"CommentId": '.$comment['id'].',';
					$output .= '"Comment": "'.$comment['comment'].'",';
					$output .= '"CommentedBy": "'.$comment['username'].'",';
					$output .= '"CommenterImg": "'.$comment['profileimg'].'",';
					$output .= '"CommentDate": "'.$comment['posted_at'].'",';
					$output .= '"CommentImage": "'.$comment['commentimg'].'",';
					$output .= '"Likes": '.$comment['likes'].',';
					$output .= '"ILiked": '.$iLiked.'';
				$output .= "},";
				
				}
		//$l = strlen($output);		
		//if (mb_substr($output,$l,1,"UTF-8") != "[") {
		
		$output = substr($output, 0, strlen($output)-1);
		//$output .= "]";
		//}
		$output .= "]";
		}else {
			$output = "[{}]";
			}
		http_response_code(200);
		echo $output;
		
	
	} else if ($_GET['url'] == "posts") {
		$start = (int)$_GET['start'];
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$followingposts = $db->query('SELECT posts.id, posts.posted_at, posts.body, posts.postimg, posts.likes, posts.subject, posts.grade, users.username, users.profileimg, followers.user_id FROM posts, users, followers WHERE (posts.user_id = users.id AND posts.user_id = followers.user_id) ORDER BY posts.posted_at DESC, posts.likes DESC LIMIT 5 OFFSET '.$start.';', array(':userid'=>$userid));
$response = "[";
foreach($followingposts as $post) {
	$iLiked = 1;
	if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$post['id'],':userid'=>$userid))){
	$iLiked = 0;
	}
	
	$response .= "{";
		$response .= '"PostId": '.$post['id'].',';
		$response .= '"PostBody": "'.$post['body'].'",';
		$response .= '"PostedBy": "'.$post['username'].'",';
		$response .= '"PostDate": "'.$post['posted_at'].'",';
		$response .= '"PostSubject": "'.$post['subject'].'",';
		$response .= '"PostGrade": '.$post['grade'].',';
		$response .= '"PostImage": "'.$post['postimg'].'",';
		$response .= '"ProfileImage": "'.$post['profileimg'].'",';
		$response .= '"Likes": '.$post['likes'].',';
		$response .= '"ILiked": '.$iLiked.'';
	$response .= "},";

	
	
	}
	$response = substr($response, 0, strlen($response)-1);
	$response .= "]";
	
	http_response_code(200);
	echo $response;
		
	} else if ($_GET['url'] == "profileposts") {
		$start = (int)$_GET['start'];
		
		
		$userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
		//users.id AND users.id =
		$followingposts = $db->query('SELECT posts.id, posts.posted_at, posts.body, posts.postimg, posts.likes, posts.subject, posts.grade, users.username, users.profileimg FROM posts, users WHERE posts.user_id =users.id AND users.id = :userid ORDER BY posts.posted_at DESC LIMIT 5 OFFSET '.$start.';', array(':userid'=>$userid));
$response = "[";
foreach($followingposts as $post) {
	$iLiked = 1;
	if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$post['id'],':userid'=>$userid))){
	$iLiked = 0;
	}
	$response .= "{";
		$response .= '"PostId": '.$post['id'].',';
		$response .= '"PostBody": "'.$post['body'].'",';
		$response .= '"PostedBy": "'.$post['username'].'",';
		$response .= '"PostSubject": "'.$post['subject'].'",';
		$response .= '"PostGrade": '.$post['grade'].',';
		$response .= '"PostDate": "'.$post['posted_at'].'",';
		$response .= '"PostImage": "'.$post['postimg'].'",';
		$response .= '"ProfileImage": "'.$post['profileimg'].'",';
		$response .= '"Likes": '.$post['likes'].',';
		$response .= '"ILiked": '.$iLiked.'';
	$response .= "},";

	
	
	}
	$response = substr($response, 0, strlen($response)-1);
	$response .= "]";
	
	http_response_code(200);
	echo $response;
		
	} else if ($_GET['url'] == "singlepost")  {
		$postid = $_GET['postid'];
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$followingposts = $db->query('SELECT posts.id, posts.posted_at, posts.body, posts.postimg, posts.likes, posts.subject, posts.grade, users.profileimg, users.username FROM posts, users WHERE posts.id=:postid AND users.id = posts.user_id', array(':postid'=>$postid));
		//echo $followingposts;
		$response = "[";
		foreach($followingposts as $post) {
		$iLiked = 1;
			if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$post['id'],':userid'=>$userid))){
		$iLiked = 0;
		}
	
		$response .= "{";
			$response .= '"PostId": '.$post['id'].',';
			$response .= '"PostBody": "'.$post['body'].'",';
			$response .= '"PostedBy": "'.$post['username'].'",';
			$response .= '"PostDate": "'.$post['posted_at'].'",';
			$response .= '"PostSubject": "'.$post['subject'].'",';
			$response .= '"PostGrade": '.$post['grade'].',';
			$response .= '"PostImage": "'.$post['postimg'].'",';
			$response .= '"ProfileImage": "'.$post['profileimg'].'",';
			$response .= '"Likes": '.$post['likes'].',';
			$response .= '"ILiked": '.$iLiked.'';
		$response .= "},";

	
	
	}
	$response = substr($response, 0, strlen($response)-1);
	$response .= "]";
	
	//http_response_code(200);
	echo $response;
	//echo $followingposts;
	} else if ($_GET['url'] == "profileposts") {
		$start = (int)$_GET['start'];
		
		
		$userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
		//users.id AND users.id =
		$followingposts = $db->query('SELECT posts.id, posts.posted_at, posts.body, posts.postimg, posts.likes, posts.subject, posts.grade, users.username, users.profileimg FROM posts, users WHERE posts.user_id =users.id AND users.id = :userid ORDER BY posts.posted_at DESC LIMIT 5 OFFSET '.$start.';', array(':userid'=>$userid));
$response = "[";
foreach($followingposts as $post) {
	$iLiked = 1;
	if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$post['id'],':userid'=>$userid))){
	$iLiked = 0;
	}
	$response .= "{";
		$response .= '"PostId": '.$post['id'].',';
		$response .= '"PostBody": "'.$post['body'].'",';
		$response .= '"PostedBy": "'.$post['username'].'",';
		$response .= '"PostSubject": "'.$post['subject'].'",';
		$response .= '"PostGrade": '.$post['grade'].',';
		$response .= '"PostDate": "'.$post['posted_at'].'",';
		$response .= '"PostImage": "'.$post['postimg'].'",';
		$response .= '"ProfileImage": "'.$post['profileimg'].'",';
		$response .= '"Likes": '.$post['likes'].',';
		$response .= '"ILiked": '.$iLiked.'';
	$response .= "},";

	
	
	}
	$response = substr($response, 0, strlen($response)-1);
	$response .= "]";
	
	http_response_code(200);
	echo $response;
		
	} else if ($_GET['url'] == "notifications") {
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

		$seen = 0; 
		//seen = 0 is no and seen = 1 is yes
		
		$notifications = $db->query('SELECT notifications.id, notifications.type, notifications.receiver, notifications.sender, notifications.extra, notifications.notification_seen FROM notifications WHERE notifications.receiver=:userid and notifications.notification_seen=:seen', array(':userid'=>$userid,':seen'=>$seen));
		$numberofnotif = sizeof($notifications);
		//echo 'There are '.$numberofnotif.' rows in the table';
		echo "{";
			echo '"Notifications":';
			echo $numberofnotif;
					
		echo "}";

	}
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
		
	
	if ($_GET['url'] == "message") {
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		
		$body = $postBody->body;
		$receiver = $postBody->receiver;
		$db->query("INSERT INTO messages VALUES ('', :body, :sender, :receiver, '0', NOW(), '')", array(':body'=>$body, ':sender'=>$userid, ':receiver'=>$receiver));
		
	
	 }  else if ($_GET['url'] == "musers2") {
		$token = $_COOKIE['SNID'];
		
		$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$msgBody = file_get_contents("php://input");
		$msgBody = json_decode($msgBody);
		
		$sentby = $msgBody->sentby;
		 		
		$db->query("UPDATE messages SET messages.read=1 WHERE (receiver=:userid AND sender=:sentby)", array(':userid'=>$userid,':sentby'=>$sentby));
		
	 } else if ($_GET['url'] == "create-account") {
		
		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		$profileimg = "profile_img/default.png";
		$coverimg = "cover_img/default.png";
		
		$username = $postBody->username;
		$email = $postBody->email;
		$password = $postBody->password;
		$category = $postBody->category;
		$industry = $postBody->industry;
		
		$firstname = $postBody->first_name;
		$lastname = $postBody->last_name;
		$userlocation = $postBody->location;
		$worksat = $postBody->works_at;
		$acctype = $postBody->acc_type;
		$accmode = $postBody->acc_mode;
		$gender = $postBody->gender;
		$dateofbirth = $postBody->dateofbirth;
		
		if (!$db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
	
			if (strlen($username) >= 3 && strlen($username) <= 32) {
				
				if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
					
						if (strlen($password) >= 6 && strlen($password) <= 60) {
				
						if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
							
						if (!$db->query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
				
	
								$db->query('INSERT INTO users VALUES (\'\', :username, :password, :email, :category, :industry, :profileimg, :coverimg,\'\')', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email, ':category'=>$category, ':industry'=>$industry,':profileimg'=>$profileimg,':coverimg'=>$coverimg));
								if ($db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))) {
									$userid =$db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
									$db->query('INSERT INTO profiles VALUES (\'\', :user_id, :first_name, :last_name, :user_location, :works_at, :acc_type, :acc_mode, NOW(), :dateofbirth, :gender)', array(':user_id'=>$userid, ':first_name'=>$firstname, ':last_name'=>$lastname, ':user_location'=>$userlocation, ':works_at'=>$worksat, ':acc_type'=>$acctype, ':acc_mode'=>$accmode, ':dateofbirth'=>$dateofbirth, ':gender'=>$gender));

								}
								echo '{"Success": "Your Account Has Successfully Been Created!"}';
								http_response_code(200);
								
						} else {
								echo '{"Error": "Email Already In Use"}';
								http_response_code(409);
							
						}
							
						} else {
								echo '{"Error": "Invalid Email"}';
								http_response_code(409);
						}
				} else {
						echo '{"Error": "Invalid Password"}';
						http_response_code(409);
				}
				} else {
						echo '{"Error": "Invalid Username"}';
					http_response_code(409);
				}
			} else {
					echo '{"Error": "Invalid Username or Password"}';
					http_response_code(409);	
			}
			
	} else {
		echo '{"Error": "User Already Exists!"}';
		http_response_code(409);
	}
		
	}
	if ($_GET['url'] == "auth") {
		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		
		$username = $postBody->username;
		$password = $postBody->password;
		
		if ($db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
			if (password_verify($password, $db->query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
				$cstrong = True;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
				$user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
				$db->query('INSERT INTO login_token VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
				//echo '{"Token": "'.$token.'"}';	
				setcookie("SNID", $token, time() + 60*60*24*7,'/',NULL, NULL,TRUE);
				setcookie("SNID_", '1',time() + 60*60*24*3,'/',NULL, NULL,TRUE);
				//header("Location: home");
				echo '{"Success": "Loggin in"}';
				http_response_code(200);
				
				} else {
					echo '{"Error": "Invalid username or password"}';
					http_response_code(401);
					}
			} else {
				echo '{"Error": "Invalid username or password"}';
				http_response_code(401);
				
			}
	} else if ($_GET['url'] == "loggedin") {
		//include('../classes/DB.php');
		require_once('../classes/Login.php');

		if (Login::isLoggedIn()) {
			$userid = Login::isLoggedIn();
			$logged = 1;
			echo $logged;
		} else {
			$logged = 0;
			echo $logged;		
		
		}
	
	} else if ($_GET['url'] == "likedpost") {
		$postId = $_GET['id'];
		$token = $_COOKIE['SNID'];
		
		$likerId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$postId,':userid'=>$likerId))){
						$iLiked = 0;
		} else {
			$iLiked = 1;
		}
		echo "{";
			echo '"ILiked":';
			echo $iLiked;
		echo "}";
		
	}  else if ($_GET['url'] == "upload") {
		$formname = $_GET['formname'];

		$query = "UPDATE posts SET postimg=:postimg WHERE id=:postid";
		if(isset($_POST["image"])) {
			$data = $_POST["image"];
		
			$image_array_1 = explode(";",$data);
		
			$image_array_2 = explode(",", $image_array_1[1]);
		
			$data = base64_decode($image_array_2[1]);
	
			$imageName = time().'.png';
			
			file_put_contents($imageName,$data);
	
			//$image_file = addslashes(file_get_contents($imageName));
				
			//$db->query('UPDATE posts SET postimg=:postimg WHERE id=:postid', array (':postimg'=>$imgpost,':postid'=>$postid));
			$folder='./img_posts/';
			$final_file = ''.$imageName.'';
			$new_loc = $folder.$final_file;
			
			if(copy($final_file,'../img_posts/'.$final_file)) {
				
			//move_uploaded_file($final_file,$folder.$final_file);
				$imageName = $new_loc;
				unlink($final_file);
				//$preparams = array($formname=>$new_loc);
			
				//$params = $preparams + $params;
	
				//$db->query($query,$params);
		
	
		
				echo $imageName;
			}
		}

	} else if  ($_GET['url'] == "forgot-password")  {
		//include('../classes/DB.php');
		require_once('../classes/Mail.php');

		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		
		
		
		$cstrong = True;
		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
		//$email = $_POST['email'];
		$email = $postBody->email;
		$user_id = $db->query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
		
		$db->query('INSERT INTO password_tokens VALUES (\'\', :token, :user_id, :email, NOW())', array(':token'=>sha1($token), ':user_id'=>$user_id, ':email'=>$email));
		//Mail::sendMail('Forgot Password', "<a href='https://buble.co.za/change-password?token=".$token."&email=".$email."'>Click here to reset your password</a>", $email);
		//echo "Email Sent!";
		//header('Location: change-password?token='.$token.'');
		//exit();
		echo $token;
	} else if  ($_GET['url'] == "change-password")  {
		$token = $_GET['token'];
		//Login::isLoggedIn();
		//the token we are getting is from the ajax request and has already been encrypted with sha1, so we dont have to encrypt it again, if it wasnt we would encrypt it again
		//before using it in the array below
		$userid = $db->query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>$token))[0]['user_id'];


		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
				
		
		if ($userid) {
			//if (isset($_POST['changepassword'])) {
			
		$oldpassword =$postBody->oldpassword;
		$newpassword =$postBody->newpassword; 
		$newpasswordrepeat = $postBody->newpasswordrepeat;
		//$userid = Login::isLoggedIn();
		
				if (password_verify($oldpassword, $db->query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])) {
					
					if ($newpassword == $newpasswordrepeat){
						
						if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
									
									$db->query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT),':userid'=>$userid));
								
									
									echo "{Success: You've successfully changed your Password}";
									http_response_code(200);
						}
								//$passwordmatch = True;
								
					} else {
								$passwordmatch = False;
								//echo "Passwords do not match!";
								echo "{Error: Passwords do not match!}";
								http_response_code(401);
					}
			
				} else {
					$currentPass = "Current Password Incorrect";
					//echo "Current Password Incorrect";
					echo "{Error: Current Password Incorrect}";
					http_response_code(401);
				}	
			//}
		} else if (!$userid) {
			
			if (isset($_GET['token'])) {
				$token = $_GET['token'];
				if ($db->query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))){
				$userid = $db->query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
				$tokenIsValid = True;
				
			

				//$newpassword = $_POST['newpassword'];
				//$newpasswordrepeat = $_POST['newpasswordrepeat'];
			
					if ($newpassword == $newpasswordrepeat){
						
						if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
									
							$db->query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT),':userid'=>$userid));
											
							echo "{Success: You've successfully changed your Password}";
							http_response_code(200);
							$db->query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$userid));
						}
						
					} else {
						$passwordmatch = False;
						echo "{Error: Passwords do not match!}";
						http_response_code(401);
					}
			
			
				
						
				} else {
					echo '{"Error": "Invalid token"}';
					http_response_code(400);
				}
			
			}
		
		} else {
				
			echo '{"Error": "Invalid User"}';
			http_response_code(400);
		}
	

	} else if ($_GET['url'] == "signout") {
		//require_once('../classes/DB.php');
		//require_once('../classes/Login.php');
		$token = $_COOKIE['SNID'];
		
		$userId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		$alldevices = $postBody->alldevices;
		if (isset($token)) {
			if ($userId) {
				if ($alldevices == 'yes') {
					//$db->query('DELETE FROM login_token WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
					$db->query('DELETE FROM login_token WHERE user_id=:userid', array(':userid'=>$userId));
					echo '{"Success": "Signing out of all devices"}';
					http_response_code(200);
		
				} else if ($alldevices == 'no') {
						if (isset($_COOKIE['SNID'])) {
							$db->query('DELETE FROM login_token WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
							echo '{"Success": "Signing out"}';
							http_response_code(200);
						}
						setcookie('SNID', '1', time()-3600);
						setcookie('SNID_', '1'. time()-3600);
						
				}
			} else {
				echo '{"Error": "Invalid User"}';
				http_response_code(400);
			}
	
		} else {
			echo '{"Error": "Invalid Token"}';
			http_response_code(400);
		}
	} else if ($_GET['url'] == "likes") {
		//require_once('../classes/DB.php');
		require_once('../classes/Login.php');
		require_once('../classes/Notify.php');

		$postId = $_GET['id'];
		$token = $_COOKIE['SNID'];
		
		$postBody = $db->query('SELECT body FROM posts WHERE id=:postid',array(':postid'=>$postId))[0]['body'];
		$likerId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		

					
		if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
						$db->query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
						$db->query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
						Notify::createNotify("", $postId, $postBody);
						//$iLiked = 0;
					} else {
							$db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
						$db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
						//$iLiked = 1;
					
					}
					//if (!$db->query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$postId,':userid'=>$likerId))){
						//$iLiked = 0;
					//}
					$likes = $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['likes'];
					echo "{";
					echo '"Likes":';
					echo $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['likes'];
					//echo '"ILiked":';
					//echo $iLiked;
					echo "}";
				//$response = "[";
					
					//$response .= "{";
						//$response .= '"Likes": '.$likes.'';
						//$response .= '"ILiked": '.$iLiked.'';
					//$response .= "},";

					//$response = substr($response, 0, strlen($response)-1);
				//$response .= "]";
	
				//http_response_code(200);
				//echo $response;
		} else if ($_GET['url'] == "follow") {
			require_once('../classes/Login.php');
			require_once('../classes/Notify.php');
			
			$token = $_COOKIE['SNID'];
			$followerid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
		
			$username = $postBody->username;
			$follow_btnId = $postBody->follow_btnId;
			
			$userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];

			$senderId = $followerid;
			$receiverId = $userid;
			if ($follow_btnId =="follow-profile-btn") {

				if ($userid != $followerid) {

		

					if(!$db->query('SELECT follower_id FROM followers WHERE user_id=:userid', array(':userid'=>$userid))) {
						$db->query('INSERT INTO followers VALUES (\'\', :userid, :followerid, NOW())', array(':userid'=>$userid, ':followerid'=>$followerid));
						
						Notify::createFollowNotify($senderId,$receiverId);
					} else {
						echo 'Already following';
					}
					$isFollowing = "True";
					echo $isFollowing;
				}
			}

			if ($follow_btnId =="unfollow-profile-btn") {
			
				if ($userid != $followerid) {

					if($db->query('SELECT follower_id FROM followers WHERE user_id=:userid', array(':userid'=>$userid))) {
						$db->query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
					} 
					$isFollowing = "False";
					echo $isFollowing;
				}
			}
		
			if($db->query('SELECT follower_id FROM followers WHERE user_id=:userid', array(':userid'=>$userid))) {
				
					//echo 'Already following';
					$isFollowing = "True";
					echo $isFollowing;
			}
		
							
		} else if($_GET['url'] == "insert_rating") {
		$token = $_COOKIE['SNID'];
		$connect = $db;	
		//$profileuserid = $_GET['userid'];

		//loggedinuserid is the id of the person thats logged in and is rating someone
		$loggedinuserid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$fetchBody = file_get_contents("php://input");
		$fetchBody = json_decode($fetchBody);
		
		//profile userid is the id of the person that is being rated (the person who's profile we are on)
		$profileuserid = $fetchBody->userid;
		$business_id = $fetchBody->business_id;
		$index = $fetchBody->index;

		if ($loggedinuserid != $profileuserid) {
		$db->query('INSERT INTO star_rating VALUES (\'\', :rated_user_id, :rater_user_id, :rate, NOW())', array(':rated_user_id'=>$profileuserid, ':rater_user_id'=>$loggedinuserid, ':rate'=>$index));
		echo "done";
		}


		} else if($_GET['url'] == "fetch_rating") {
			//star rating begin
		//include("DB.php");
		//$profileuserid = $_GET['userid'];
		$connect = $db;
		
		$fetchBody = file_get_contents("php://input");
		$fetchBody = json_decode($fetchBody);
		
		$profileuserid = $fetchBody->userid;
		
		
		$result = $db->query("SELECT * FROM users ORDER BY id DESC");
		$output = '';
		
		function count_rating($business_id,$connect) {
			require_once("DB.php");
			$db = $connect;
			$output = 0;
			$result = $db->query("SELECT AVG(rate) as rating FROM star_rating WHERE rated_user_id =:business_id", array(':business_id'=>$business_id));
			$total_row = sizeof($result);
			if($total_row > 0) {
				foreach($result as $row) {
					$output = round($row["rating"]);
				}
			}
			return $output;
		}
		foreach($result as $row){
			if ($row['id']==$profileuserid){
			$rating = count_rating($row["id"], $connect);
			$color = '';
			$output .= '<ul class="list-inline" data-rating="'.$rating.'" title="Average Rating - '.$rating.'">';
			for($count=1;$count<=5;$count++) {
				if($count<=$rating){
					$color = 'color:#ffcc00;';
					$startype = 'fas fa-star fa-2x';
				} else {
					$color = 'color:#ffcc00;';
					$startype = 'far fa-star fa-2x';
				}
			//&#9733; place this just before</li> below
			$output .='<li title="'.$count.'" id="'.$row['id'].'-'.$count.'" data-index="'.$count.'" data-business_id="'.$row['id'].'" data-rating="'.$rating.'" class="rating '.$startype.'" style="padding: 0 4px; cursor:pointer; '.$color.' font-size:16px;"></li>';
			} 
			$output .= '</ul>';
			}
		}
		echo $output;

		
		//star rating end		
		} else if($_GET['url'] == "index_fetch_rating") {
			//star rating begin
		//include("DB.php");
		//$profileuserid = $_GET['userid'];
		$connect = $db;

		$fetchBody = file_get_contents("php://input");
		$fetchBody = json_decode($fetchBody);
		
		$profileuserid = $fetchBody->userid;
		
		$result = $db->query("SELECT * FROM users ORDER BY id DESC");
		$output = '';
		
		function count_rating($business_id,$connect) {
			require_once("DB.php");
			$db = $connect;
			$output = 0;
			$result = $db->query("SELECT AVG(rate) as rating FROM star_rating WHERE rated_user_id =:business_id", array(':business_id'=>$business_id));
			$total_row = sizeof($result);
			if($total_row > 0) {
				foreach($result as $row) {
					$output = round($row["rating"]);
				}
			}
			return $output;
		}
		foreach($result as $row){
			
			$rating = count_rating($row["id"], $connect);
			$color = '';
			$output .= '<ul class="list-inline" data-rating="'.$rating.'" title="Average Rating - '.$rating.'">';
			for($count=1;$count<=5;$count++) {
				if($count<=$rating){
					$color = 'color:#ffcc00;';
					$startype = 'fas fa-star fa-2x';
				} else {
					$color = 'color:#ffcc00;';
					$startype = 'far fa-star fa-2x';
				}
			//&#9733; place this just before</li> below
			$output .='<li title="'.$count.'" id="'.$row['id'].'-'.$count.'" data-index="'.$count.'" data-business_id="'.$row['id'].'" data-rating="'.$rating.'" class="rating '.$startype.'" style="padding: 0 4px; cursor:pointer; '.$color.' font-size:16px;"></li>';
			} 
			$output .= '</ul>';
			
		}
		echo $output;

		
		//star rating end		
		} else if ($_GET['url'] == "edit_pro_coverpic") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$editBody = json_decode($editBody);
		
			$image = $editBody->image;
			$imagetype = $editBody->imagetype;
		
			if ($imagetype == "propic") {
				//echo $imagetype;
				$db->query('UPDATE users SET profileimg=:profilepic WHERE id=:userid', array(':profilepic'=>$image, ':userid'=>$userid));
		
		
				
			} else if ($imagetype == "coverpic") {
				//echo $imagetype;
		 		$db->query('UPDATE users SET coverimg=:coverpic WHERE id=:userid', array(':coverpic'=>$image, ':userid'=>$userid));	
			}
			
		} else if ($_GET['url'] == "edit_coverphoto") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$editBody = json_decode($editBody);
		
			$fname = $editBody->fname;
			$lname = $editBody->lname;
			$email = $editBody->email;
		
			if ($fname != "") {
				$db->query('UPDATE profiles SET first_name=:fname,last_name=:lname WHERE user_id=:userid', array(':fname'=>$fname,':lname'=>$lname, ':userid'=>$userid));
		
		
			if ($email != "") {
				$db->query('UPDATE users SET email=:e_mail WHERE id=:userid', array (':e_mail'=>$email,':userid'=>$userid));
				}	
			} else {
		 		echo "Oops! You forgot to edit something";	
			}
			
		} else if ($_GET['url'] == "edit_personalinfo") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$editBody = json_decode($editBody);
		
			$fname = $editBody->fname;
			$lname = $editBody->lname;
			$email = $editBody->email;
		
			if ($fname != "") {
				$db->query('UPDATE profiles SET first_name=:fname,last_name=:lname WHERE user_id=:userid', array(':fname'=>$fname,':lname'=>$lname, ':userid'=>$userid));
		
		
			if ($email != "") {
				$db->query('UPDATE users SET email=:e_mail WHERE id=:userid', array (':e_mail'=>$email,':userid'=>$userid));
				}	
			} else {
		 		echo "Oops! You forgot to edit something";	
			}
			
		} else if ($_GET['url'] == "edit_skills") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$arrayBody = json_decode($editBody,true);
			$editBody = json_decode($editBody);
			
			$skillExist = $db->query('SELECT id, user_id, skill_id FROM skill_lookup WHERE user_id=:userid', array(':userid'=>$userid));
				//echo "skillid is";
				//echo $skillID;
				if(count($skillExist) == 0) {
					foreach($editBody as $body){
						$skillID = $body->myskills;
						if ($skillID != "") {
							$db->query('INSERT INTO skill_lookup VALUES (\'\', :userid, :skillid)', array (':userid'=>$userid,':skillid'=>$skillID));
							echo "Skills updated successfully";
						} else {
		 					echo "Oops! You forgot to edit something";	
						}
					}
				} else {
					$old_lookup_id = $db->query('SELECT id FROM skill_lookup WHERE user_id=:userid', array(':userid'=>$userid));
					$old_skill_id = $db->query('SELECT skill_id FROM skill_lookup WHERE user_id=:userid', array(':userid'=>$userid));
					
					$old_lookup_id = array_column($old_lookup_id,'id');
					$new_lookup_id = array_column($arrayBody,'lookup_id');
					$skillID = array_column($arrayBody,'myskills');
					//print_r($old_lookup_id);
					$result = array_diff($old_lookup_id,$new_lookup_id);
					$result2 = array_diff($new_lookup_id,$old_lookup_id);
					print_r($new_lookup_id);
					$count_old_id = count($old_lookup_id);
					$count_new_id = count($new_lookup_id);
					$i=0;
					for($k=0; $k<max($count_old_id,$count_new_id); $k++) {
						
						if (array_key_exists($k,$result)) {
							
							$db->query('DELETE FROM skill_lookup WHERE id=:lookupid', array(':lookupid'=>$result[$k]));
							echo "Result deleted successfully";
						} else if((!array_key_exists($k,$result)) && (array_key_exists($k,$result2))) {
							$db->query('INSERT INTO skill_lookup VALUES (\'\', :userid, :skillid)', array (':userid'=>$userid,':skillid'=>$skillID[$k]));
							echo "New Record Added Successfully";
						}
					}
					foreach($editBody as $body){
						
						
						$skillID = $body->myskills;
						$lookup_id = $body->lookup_id;
						
						
						
						if ($skillID != "") {
							print_r($lookup_id[$i]['id']);
							$db->query('UPDATE skill_lookup SET skill_id=:skillid WHERE user_id=:userid AND id=:lookupid', array (':userid'=>$userid,':skillid'=>$skillID,':lookupid'=>$lookup_id));
							echo "Skills updated successfully";
							$i++;
						} else {
		 					echo "Oops! You forgot to edit something";	
						}
					}
				}
				
			
			
		} else if ($_GET['url'] == "edit_workexperience") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$editBody = json_decode($editBody);
		
			$from_year = $editBody->from_year;
			$to_year = $editBody->to_year;
			$job = $editBody->job;
			$ref_phone = $editBody->ref_phone;
			$job_duties = $editBody->job_duties;

			$workE_exists =  $db->query('SELECT id FROM work_experience WHERE user_id=:userid', array(':userid'=>$userid))[0]['id'];;
			if (count($workE_exists) !=0) {
				if (($from_year != "") && ($to_year != "") && ($job != "") && ($ref_phone != "") && ($job_duties != "")) {
					$db->query('UPDATE work_experience SET  user_id=:userid, job_year_from=:jobyearfrom, job_year_to=:jobyearto, company_name=:companyname, job_reference=:jobreference, job_duties=:jobduties WHERE id=:work_id', array(':userid'=>$userid, ':work_id'=>$workE_exists,':jobyearfrom'=>$from_year, ':jobyearto'=>$to_year, ':companyname'=>$job, ':jobreference'=>$ref_phone, ':jobduties'=>$job_duties));
		
		
						
				} else {
		 			echo "Oops! You forgot to edit something";	
				}
			} else {
				if (($from_year != "") && ($to_year != "") && ($job != "") && ($ref_phone != "") && ($job_duties != "")) {
					$db->query('INSERT INTO work_experience VALUES (\'\', :user_id, :job_year_from, :job_year_to, :company_name, :job_reference, :job_duties)', array(':user_id'=>$userid,':job_year_from'=>$from_year, ':job_year_to'=>$to_year, ':company_name'=>$job, ':job_reference'=>$ref_phone, ':job_duties'=>$job_duties));
		
		
						
				} else {
		 			echo "Oops! You forgot to edit something";	
				}
			}
			
		} else if ($_GET['url'] == "edit_education") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$editBody = json_decode($editBody);
		
			$from_year = $editBody->from_year;
			$to_year = $editBody->to_year;
			$inst_name = $editBody->institution_name;
			$course_name = $editBody->course_name;
			$achievements = $editBody->achievements;
			$type = $editBody->education_type;

			$education_id =  $db->query('SELECT id FROM education WHERE user_id=:userid', array(':userid'=>$userid))[0]['id'];;
			if (count($education_id) !=0) {
				if (($from_year != "") && ($to_year != "") && ($inst_name != "") && ($course_name != "") && ($achievements != "") && ($type !="")) {
					$db->query('UPDATE education SET  user_id=:userid, education_year_from=:educationyearfrom, education_year_to=:educationyearto, institution_name=:institutionname, course_name=:coursename, achievements=:c_achievements, education_type=:educationtype WHERE id=:education_id', array(':userid'=>$userid, ':education_id'=>$education_id,':educationyearfrom'=>$from_year, ':educationyearfrom'=>$to_year, ':institutionname'=>$inst_name, ':coursename'=>$course_name, ':c_achievements'=>$achievements, 'educationtype'=>$type));
		
		
						
				} else {
		 			echo "Oops! You forgot to edit something";	
				}
			} else {
				if (($from_year != "") && ($to_year != "") && ($inst_name != "") && ($course_name != "") && ($achievements != "") && ($type !="")) {
					$db->query('INSERT INTO education VALUES (\'\', :user_id, :education_type, :institution_name, :education_year_from, :education_year_to, :course_name, :achievements)', array(':userid'=>$userid, ':education_id'=>$education_id,':educationyearfrom'=>$from_year, ':educationyearfrom'=>$to_year, ':institutionname'=>$inst_name, ':coursename'=>$course_name, ':c_achievements'=>$achievements, 'educationtype'=>$type));
		
		
						
				} else {
		 			echo "Oops! You forgot to edit something";	
				}
			}
			
		} else if ($_GET['url'] == "edit_contactdetails") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
			
			$editBody = file_get_contents("php://input");
			$editBody = json_decode($editBody);
		
			
			$email = $editBody->email;
			$cell = $editBody->cell;
			$tel = $editBody->tel;
			$social_fb = $editBody->social_facebook;
			$social_wa = $editBody->social_whatsapp;
			$social_li = $editBody->social_linkedin;
			//$web_url = $editBody->web_link;
			//$social_o1 = $editBody->social_other1;
			//$social_o2 = $editBody->social_other2;
			

			$contact_id =  $db->query('SELECT id FROM contact_details WHERE user_id=:userid', array(':userid'=>$userid))[0]['id'];;
			if (count($contact_id) !=0) {
				if (($email != "") && ($cell != "") OR ($tel != "") OR ($social_fb != "") OR ($social_wa != "") OR ($social_li !="")) {
					$db->query('UPDATE contact SET email=:email,cell_number=:cellnum, tel_number=:telnum, social_facebook=:social_fb, social_whatsapp=:social_wa, social_linkedin=:social_li WHERE id=:contactid', array(':email'=>$email,':cellnum'=>$cell, ':telnum'=>$tel, ':social_fb'=>$social_fb,':social_wa'=>$social_wa,':social_li'=>$social_li));
					
					$db->query('UPDATE users SET email=:e_mail WHERE id=:userid', array (':e_mail'=>$email,':userid'=>$userid));
				
		
						
				} else {
		 			echo "Oops! You forgot to edit something";	
				}
			} else {
				if (($email != "") && ($cell != "") OR ($tel != "") OR ($social_fb != "") OR ($social_wa != "") OR ($social_li !="")) {
					$db->query('INSERT INTO contact VALUES (\'\', :email,: cell_number, :tel_number, :social_facebook, :social_whatsapp, :social_linkedin, \'\', \'\', \'\' )', array(':email'=>$email,':cellnum'=>$cell, ':telnum'=>$tel, ':social_fb'=>$social_fb,':social_wa'=>$social_wa,':social_li'=>$social_li));
		
		
						
				} else {
		 			echo "Oops! You forgot to edit something";	
				}
			}

			
		
				
			
			
		} else if ($_GET['url'] == "viewednotif") {
			$token = $_COOKIE['SNID'];
		
			$userid = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

			$seen = 1;
			$notseen = 0; 
			//seen = 0 is no and seen = 1 is yes
		
			$db->query('UPDATE notifications SET notifications.notification_seen=:seen WHERE notifications.receiver=:userid AND notifications.notification_seen=:notseen', array(':userid'=>$userid,':seen'=>$seen,':notseen'=>$notseen));
			$notifications = $db->query('SELECT notifications.id, notifications.type, notifications.receiver, notifications.sender, notifications.extra, notifications.notification_seen FROM notifications WHERE notifications.receiver=:userid and notifications.notification_seen=:seen', array(':userid'=>$userid,':seen'=>$notseen));
			$numberofnotif = sizeof($notifications);
			//echo 'There are '.$numberofnotif.' rows in the table';
			echo "{";
				echo '"Notifications":';
				echo $numberofnotif;
					
			echo "}";
		
		} else if ($_GET['url'] == "deletepost") {
		$token = $_COOKIE['SNID'];
		$userId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$postId = $_GET['postid'];
		
		$poster = $db->query('SELECT user_id FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['user_id'];	
		
		if ($db->query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$postId,':userid'=>$userId))) {
			if ($poster == $userId) {
				$db->query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$postId,':userid'=>$userId));
				$db->query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$postId));
				echo 'Your post '.$postId.' has been deleted';
			}
		}
		
		} else if ($_GET['url'] == "editedposts") {
		$token = $_COOKIE['SNID'];
		
		$userId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$postId = $_GET['postid'];
		$subject = 'Maths';
		$grade = 12;
		
		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		
		$body = $postBody->body;
		$poster = $postBody->poster;
		$imgpost = $postBody->imgpost;
		
		if ($body != "") {
		$db->query('UPDATE posts SET body=:postbody WHERE id=:postid AND user_id=:userid', array(':postbody'=>$body, ':userid'=>$userId,':postid'=>$postId));
		
		
		if ($imgpost != "") {
			$db->query('UPDATE posts SET postimg=:postimg WHERE id=:postid', array (':postimg'=>$imgpost,':postid'=>$postId));
			}	
		} else {
		 echo "Please enter post";	
		}
        } else if ($_GET['url'] == "reportpost") {
		$token = $_COOKIE['SNID'];
		
		$reporterId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
				

		$postId = $_GET['postid'];

		$followingposts = $db->query('SELECT posts.user_id, posts.posted_at, posts.body, posts.postimg, posts.likes FROM posts WHERE posts.id =:postid', array(':postid'=>$postId));
		$posterId = $followingposts[0]['user_id'];
		$reportBody = file_get_contents("php://input");
		$reportBody = json_decode($reportBody);
		
		//$reporttype = $_POST['myCheckboxes'];
		
		$reporttype = $reportBody->myCheckboxes;
		//$poster = $postBody->poster;
		//$imgpost = $postBody->imgpost;
		
		//if ($body != "") {
		$db->query('INSERT INTO reports VALUES (\'\', :reporttype, :postid, :posterid, :reporterid, NOW())', array(':postid'=>$postId, ':posterid'=>$posterId, ':reporterid'=>$reporterId,':reporttype'=>$reporttype));
		
		//$reportId = $db->query('SELECT id FROM reports WHERE reporter_id=:reporterid AND post_id=:postid ORDER BY ID DESC LIMIT 1;', array(':reporterid'=>$reporterId,':postid'=>$postId))[0]['id'];
		
		echo "You successfully reported this post, our review team will get back to you";
		
		
		
		
        } else if ($_GET['url'] == "createpost") {
		require_once('../classes/Login.php');
		require_once('../classes/Notify.php');

		$token = $_COOKIE['SNID'];
		
		$userId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		$subject = 'Maths';
		$grade = 12;
		
		$postBody = file_get_contents("php://input");
		$postBody = json_decode($postBody);
		
		$body = $postBody->body;
		$poster = $postBody->poster;
		$imgpost = $postBody->imgpost;
		
		if ($body != "") {
		$db->query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, :subject, :grade, \'NULL\', \'0\')', array(':postbody'=>$body, ':userid'=>$userId, ':subject'=>$subject, ':grade'=>$grade));
		
		$postid = $db->query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$userId))[0]['id'];
		Notify::createNotify($body,$postid,$body);
		
		
		
		
		if ($imgpost != "") {
			$db->query('UPDATE posts SET postimg=:postimg WHERE id=:postid', array (':postimg'=>$imgpost,':postid'=>$postid));
			Notify::createNotify($body,$postid,$body);
			}	
		} else {
		 echo "Please enter post";	
		}
        } else if ($_GET['url'] == "createcomment") {
		require_once('../classes/Login.php');
		require_once('../classes/Notify.php');
		//$commentId = $_GET['id'];	
		$token = $_COOKIE['SNID'];
		
		$userId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		$commentBody = file_get_contents("php://input");
		$commentBody = json_decode($commentBody);
		
		$body = $commentBody->body;
		$commenter = $commentBody->commenter;
		$imgcomment = $commentBody->imgcomment;
		$postId = $_GET['postid'];
		//if ($userid == $commenter) {
		
			//if ($imgcomment == "0") {
				if (!$db->query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
						echo 'Invalid post ID';
				 } else {
							$db->query('INSERT INTO comments VALUES (\'\', :comment, :userid, NOW(), :postid, 0, \'\')', array(':comment'=>$body, ':userid'=>$userId, ':postid'=>$postId));
							
							$db->query('UPDATE posts SET commented =1 WHERE id=:postid', array(':postid'=>$postId));
							$commentId = $db->query('SELECT comments.id FROM comments WHERE post_id=:postid AND user_id=:userid ORDER BY posted_at DESC', array(':postid'=>$postId, ':userid'=>$userId))[0]['id'];
							
							Notify::createcmNotify($body, $commentId,$body);
				 }
			//} //"else {" (for image comment comes here)
			
			//"}" the end of the image comment code
					//} else {
							//die('Incorrect User');
							//}
		
		
		
		// uncomment "else {" (this will be for if the comment has an image or  not)
		// insert image comment code here then insert code from Image::upload image here too see below code
		//$commentid = Comment::createImgComment($_POST['commentbody'], $_GET['postid'], $userid);	
					//Image::uploadImage('commentimg', "UPDATE comments SET commentimg=:commentimg WHERE id=:commentid", array (':commentid'=>$commentid));
		// uncomment the closing tag for the else part of the if statement here: "}"

	 } else if ($_GET['url'] == "likedcomment") {
		$commentId = $_GET['id'];
		$token = $_COOKIE['SNID'];
		
		$likerId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		if (!$db->query('SELECT comment_id FROM comment_likes WHERE comment_id=:commentid AND user_id=:userid',array(':commentid'=>$commentId,':userid'=>$likerId))){
						$iLiked = 0;
		} else {
			$iLiked = 1;
		}
		echo "{";
			echo '"ILiked":';
			echo $iLiked;
		echo "}";
		
	} else if ($_GET['url'] == "commentlikes") {
		//require_once('../classes/DB.php');
		require_once('../classes/Login.php');
		require_once('../classes/Notify.php');

		$commentId = $_GET['id'];
		$token = $_COOKIE['SNID'];

		$postId = $db->query('SELECT post_id FROM comments WHERE id=:commentid',array(':commentid'=>$commentId))[0]['post_id'];
		$commentBody = $db->query('SELECT comment FROM comments WHERE id=:commentid',array(':commentid'=>$commentId))[0]['comment'];
		$likerId = $db->query('SELECT user_id FROM login_token WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
		
		if (!$db->query('SELECT user_id FROM comment_likes WHERE comment_id=:commentid AND user_id=:userid', array(':commentid'=>$commentId, ':userid'=>$likerId))) {
						$db->query('UPDATE comments SET likes=likes+1 WHERE id=:commentid', array(':commentid'=>$commentId));
						$db->query('INSERT INTO comment_likes VALUES (\'\', :commentid, :userid)', array(':commentid'=>$commentId, ':userid'=>$likerId));
						Notify::createcmNotify("", $commentId,$commentBody);
					} else {
							$db->query('UPDATE comments SET likes=likes-1 WHERE id=:commentid', array(':commentid'=>$commentId));
						$db->query('DELETE FROM comment_likes WHERE comment_id=:commentid AND user_id=:userid', array(':commentid'=>$commentId, ':userid'=>$likerId));
						
					
					}
					echo "{";
					echo '"Likes":';
					echo $db->query('SELECT likes FROM comments WHERE id=:commentid', array(':commentid'=>$commentId))[0]['likes'];
					echo "}";
		} 
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
		if ($_GET['url'] == "auth") {
				if (isset($_GET['token'])) {
						if ($db->query("SELECT token FROM login_token WHERE token=:token", array(':token'=>sha1($_GET['token'])))) {
							$db->query('DELETE FROM login_token WHERE token=:token', array(':token'=>sha1($_GET['token'])));
							echo '{"Status": "Success"}';
							http_response_code(200);
						} else {
							echo '{"Error": "Invalid token"}';
							http_response_code(400);
							}
					} else {
						echo '{"Error": "Mal-formed request"}';
						http_response_code(400);
						}
		}

} else {
	http_response_code(405);	
}

?>
