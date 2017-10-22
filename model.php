<?php


class User{
  // Declaration des attributs
  private $id;
  private $name;
  private $score;

  // Declaration des methodes
  public function __construct($pseudo, $id){
    $this->name = $pseudo;
    $this->id = $id;
    $this->score = 0;
  }

  public function getId(){ return $this->id; }

  public function getName(){ return $this->name; }

  public function getScore(){ return $this->score; }

  public function setScore( $newscore ){ $this->score = $newscore; }

}

class DBInterface{
  //Declaration des attributs
  private $cnx;

  //Declaration des methodes
  public function __construct(){
   // $this->cnx = new PDO("mysql:dbname=ollivier;host=localhost", "ollivier", "kkHL_qY7");
    //$this->cnx = new PDO("mysql:dbname=jeu;host=localhost", "root", "root");
    $this->cnx = new PDO("mysql:dbname=coffin;host=localhost", "coffin", "CraaKj_U");
  }

  public function saveScore($score, $pseudo){
    $sql = "insert into user (user, lastscore, lastegame) values ('$pseudo','$score',now() )";


    $this->cnx->query( $sql );
  }

  function displayScore(){
 	$sql="select * from user order by lastscore desc limit 0,9";
	$answer= $this->cnx->query($sql);
	$res=$answer->fetchAll(PDO::FETCH_ASSOC);

	return $res;
  }

  function createUser($user){
    $sql = "insert into user (user) values ( '$user' )";
  	$this->cnx->query( $sql );
  	$sql2 = "select id from user where user = '$user'";
  	$answer=$this->cnx->query($sql2);
  	$id=$answer->fetchAll(PDO::FETCH_ASSOC);

  	return $id;
   }
 } 


?>
