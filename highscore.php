<?php 
	require "model.php";
	$i = 1;
	$dbi = new DBInterface();
	$data = $dbi->displayScore();
 ?>
<html>
	<head>
		<title>Highscore</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css?d=<?php echo date('dm'); ?>">

		<script type="text/javascript">

		

		function kevinSpacey () {
			var kevin = document.getElementById("spacey");

			window.setInterval(function(){

				var left = Math.random() * document.getElementById("pageDeScore").clientWidth;
				var top = Math.random() * document.getElementById("pageDeScore").clientHeight;

				if(left > Math.random() * document.getElementById("pageDeScore").clientWidth){
					kevin.style.left = left - 66;
				}
				else{
					kevin.style.left = left;
				}

				if(top > Math.random() * document.getElementById("pageDeScore").clientHeight){
					kevin.style.top = top - 99;
				}
				else{
					kevin.style.top = top;
				}
				
	
			}, 5000);
			
		}

		window.addEventListener("load", kevinSpacey, false);

		</script>
	
	</head>
	<body id="pageDeScore">
		
		<section id="content">
			<h1>Highscore</h1>
			<table>
			<?php foreach ($data as $row){ ?>
				<tr>
					<td><?php echo $i++?></td>
					<td><?php echo $row["user"] ?></td>
					<td><?php echo $row["lastscore"] ?></td>
				</tr>
				<?php } ?>
			</table>

			<a href="index.php"> Retour à l'accueil </a>
			
		</section>

		

		<img src="sprites/spacey.png" id="spacey" width="66px" height="99px">

	</body>
</html>