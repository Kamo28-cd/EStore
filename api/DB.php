<?php 
//$username='unlockyo';
//$password='oP8Shf44n5';
//$database='unlockyo_work'; use studenthub database on local server and upload its table contents into unlockyo_work database on cp9 domains live server because domains wont let you name the database to only studenthub, it needs to have unlockyo_
//for local server (offline test server) use dbname=studenthub username = root and password = Epsilion28   
class DB {
	
	private $pdo;
	
	public function __construct($host, $dbname, $username, $password) {
		$pdo = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo = $pdo;
	}
	
	public function query($query, $params = array()) {
		$statement = $this->pdo->prepare($query);
		$statement->execute($params);
		
		if (explode(' ', $query)[0] == 'SELECT') {
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data;
		}


	}
}

?>