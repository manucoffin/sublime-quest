

<html>
<head>
	<title> home </title>
	<link rel="stylesheet" type="text/css" href="css/style.css?d=<?php echo date('dm'); ?>"/> 
	
</head>
<body>

	<img src="sprites/logo.png" id="logo" width="500px">
	<nav>
		<ul>
			<li><a href="controler.php" id="start"> New game  </a></li>
			<li><a href="highscore.php"> High Score  </a></li>
			<li><a href="credits.html" id="credits"> Credits  </a></li>
		</ul>
	</nav>

	<script type="text/javascript">

		// document.getElementById("start").addEventListener("click", function(){
		// 	document.location.href="controler.php";
		// }, false);


	</script>

</body>
</html>

<?php 
	if(isset($_GET["destroy"]) && $_GET["destroy"] == 1){
		$_SESSION = array();
	   if( isset( $_COOKIE[ session_name()] ) ){
	      setcookie(session_name(), time()-50000);
	      @session_destroy();      
	   }
	   die();
	   header("location:index.php");
	}
?>
