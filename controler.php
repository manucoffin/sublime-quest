<?php

	require "model.php";
	session_start();

 
	$dbi = new DBInterface();

	if(isset($_POST["score"])){
		echo $_POST["score"];
			$dbi->saveScore($_POST["score"], $_SESSION["pseudo"]);
			$_SESSION["user"]->setScore($_POST["score"]);
		die();
	}

	//Si c'est la premiere, on rentre le pseudo dans la session et on crée un utilisateur dans la bdd
	if (isset($_SESSION["pseudo"]) == false ){
		if (isset($_POST["pseudo"])){
			$_SESSION["pseudo"] = $_POST["pseudo"];
			header("location:src_run.php");	
			$id = $dbi->createUser($_POST["pseudo"]);
			$_SESSION["user"] = new User($_POST["pseudo"], $id[0]["id"]);
		}
	}
	else{
		header("location:src_run.php");	
	}

	
	
	// si c'est la première visite on envoie le formulaire
	require "viewform.php";	

?>