<?php
	require "model.php";
	session_start();
	$i = 1;
	$dbi = new DBInterface();
	$data = $dbi->displayScore();


?>

<html>
<head>
	<title>Sublime Quest 2</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/style.css?d=<?php echo date('dm'); ?>">
	<script type="text/javascript" src="js/citations.js"></script>

	<!-- importations de bibliothèques juste pour gérer la latence du son -->
	<script src="js/lowLag.js"></script>
	<script src="sm2/js/soundmanager2.js"></script>
	<script src="jquery-2.1.0.min.js"></script>
</head>

<body>

<section id="content"> 
	<h1> Sublime Quest 2 </h1>
	
	<aside>

		<p id="score"> </p>

		<h3>Highscore</h3>
		<table>
			<?php foreach ($data as $row){ ?>
				<tr>
					<td><?php echo $i++?></td>
					<td><?php echo $row["user"] ?></td>
					<td><?php echo $row["lastscore"] ?></td>
				</tr>
				<?php } ?>
		</table>

		<canvas id="infosJoueur" width="50px" height="50px"></canvas>
		<p id="balles"> Chargeur : 10 </p>
		<a href="controler.php"> Rejouer !</a>
		<a href="index.php?destroy=1"> Accueil </a>

		
	</aside>

		<p id="quote"> "Coucou." </p>

	<canvas id="can" width="600px" height="600px"></canvas>
	
</section>

<!-- /////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////     Le son      ///////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////-->

<script type="text/javascript">
	// le chargement de tous les sons utiles avec la library lowLag pour pas que y'ai de latence quand le son se joue
	lowLag.init();
	lowLag.load(["sounds/mp3/sons_re.mp3","sounds/ogg/sons_re.ogg"],"sons_re");
	lowLag.load(["sounds/mp3/sons_mi.mp3","sounds/ogg/sons_mi.ogg"],"sons_mi");
	lowLag.load(["sounds/mp3/sons_fa.mp3","sounds/ogg/sons_fa.ogg"],"sons_fa");
	lowLag.load(["sounds/mp3/sons_sol.mp3","sounds/ogg/sons_sol.ogg"],"sons_sol");
	lowLag.load(["sounds/mp3/sons_la.mp3","sounds/ogg/sons_la.ogg"],"sons_la");

	lowLag.load(["sounds/mp3/son_ennemy_collision.mp3","sounds/ogg/son_ennemy_collision.ogg"],"sons_ennemy_collision");
	lowLag.load(["sounds/mp3/sons_bonus.mp3","sounds/ogg/sons_bonus.ogg"],"sons_bonus");
	lowLag.load(["sounds/mp3/sons_malus.mp3","sounds/ogg/sons_malus.ogg"],"sons_malus");

	lowLag.load(["sounds/mp3/bass.mp3","sounds/ogg/bass.ogg"],"bass");
	lowLag.load(["sounds/mp3/horn.mp3","sounds/ogg/horn.ogg"],"horn");
	lowLag.load(["sounds/mp3/organ.mp3","sounds/ogg/organ.ogg"],"organ");
	lowLag.load(["sounds/mp3/drums.mp3","sounds/ogg/drums.ogg"],"drums");

	function lancementMusic(){
		var sons = document.getElementsByTagName("audio");
		// for (var i = 0; i < sons.length; i++) {
		// 	sons[i].volume = 0.1;
		// };

		sons[0].volume = 0.1;
		sons[1].volume = 0.2;
		sons[2].volume = 0.1;
		sons[3].volume = 0.1;
	}



	window.addEventListener("load", lancementMusic, false);
	
</script>

<audio autoplay="true" loop="true" muted="true" id="harp">
  <source src="sounds/ogg/sons_harp.ogg" type="audio/ogg">
  <source src="sounds/mp3/sons_harp.mp3" type="audio/mpeg">
</audio>

<audio autoplay="true" loop="true" muted="true" id="bass">
  <source src="sounds/ogg/sons_bass.ogg" type="audio/ogg">
  <source src="sounds/mp3/sons_bass.mp3" type="audio/mpeg">
</audio>

<audio autoplay="true" loop="true" muted="true" id="jingleBell">
  <source src="sounds/ogg/sons_jingleBell.ogg" type="audio/ogg">
  <source src="sounds/mp3/sons_jingleBell.mp3" type="audio/mpeg">
</audio>

<audio autoplay="true" loop="true" muted="true" id="violin">
  <source src="sounds/ogg/sons_violin.ogg" type="audio/ogg">
  <source src="sounds/mp3/sons_violin.mp3" type="audio/mpeg">
</audio>

<!-- <audio autoplay="true" loop="true" muted="true" id="bass">
  <source src="sounds/ogg/bass.ogg" type="audio/ogg">
  <source src="sounds/mp3/bass.mp3" type="audio/mpeg">
</audio>

<audio autoplay="true" loop="true" muted="true" id="horn">
  <source src="sounds/ogg/horn.ogg" type="audio/ogg">
  <source src="sounds/mp3/horn.mp3" type="audio/mpeg">
</audio>

<audio autoplay="true" loop="true" muted="true" id="organ">
  <source src="sounds/ogg/organ.ogg" type="audio/ogg">
  <source src="sounds/mp3/organ.mp3" type="audio/mpeg">
</audio>

<audio autoplay="true" loop="true" muted="true" id="drums">
  <source src="sounds/ogg/drums.ogg" type="audio/ogg">
  <source src="sounds/mp3/drums.mp3" type="audio/mpeg">
</audio> -->




<script type="text/javascript">
	var quote = document.getElementById("quote");

	window.setInterval(function(){
		quote.innerHTML = "\"" + rdmQuotes() + "\"";
	}, 10000);


	var largeurClient = document.body.clientWidth;
	var hauteurClient = document.body.clientHeight;

	var topCanvas = document.getElementById("can").offsetTop;
	var leftCanvas = document.getElementById("can").offsetLeft;

	quote.style.left = leftCanvas + 30;
	quote.style.top = topCanvas + 15;
</script>



<script type="text/javascript">
	var canvas = document.getElementById("can");
	var ctx = canvas.getContext("2d");

	var infosDuJoueurEnCanvas = document.getElementById("infosJoueur");
	var CtxInfosJoueur = infosDuJoueurEnCanvas.getContext("2d");

	var pscore = document.getElementById("score");

	var score = 100;
	var scoreState = "normal";

	var end = 0;

	var w = canvas.width;
	var h = canvas.height;

// image pour le perso
	var perso_src = new Image();
	perso_src.src = "sprites/spriteV2.png";

// image pour les balles
	var bulletImg = new Image();
	bulletImg.src = "sprites/sprite_bullet.png";

// image pour les ennemis
	var spritesMMIFinal = new Image();
	spritesMMIFinal.src = "sprites/spritesMMIFinal.png";

// image pour les bonus/malus
	var bonusImg = new Image();
	bonusImg.src = "sprites/bonus.png";

// image pour les vies restantes
	var vies = new Image();
	vies.src = "sprites/sprite_life.jpg"

// un tableau pour ranger les ennemis
	var ennemies = [];
	var NumberOfEnnemies = 10;
	var compteurEnnemies = "waiting";
	  


	

// Fonction AJAX, à la fin du jeu on envoie une requete comportant le score
	var save_score =function(){		
		ctx.clearRect(0, 0, w, h);				
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(){
			if(xhr.readyState==4 && xhr.status==200){
			(xhr.responseText);
			}
		}
		xhr.open("post", "controler.php");
		xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		var data = "score="+ score;
		xhr.send(data);
	}

// un objet pour les balles
	var bullet = {
		x : undefined,
		y : undefined,
		etat : "waiting",
		width : 29,
		height : 98,
		// vecteurs pour tester les collisions
		vx : 0,  
		vy : 0,
		note : 0,

		abscisseCrop: 1 + 30 * Math.floor(Math.random()*6), // un abscisse aléatoire multiple de 30

		updateAnimation: function(){
			
			if(this.etat == "waiting"){
				this.x = -100;
				this.y = -100;
			}

			if(this.etat == "tir"){ // quand on tire
				// on vérifie que le tir est déjà initialisé
				if(this.y === undefined){ // NON, alors on initialise de ou doit partir le tir
					this.x = 50;
					this.y = 50;
				}
				else{ // OUI, alors on se contente de mettre à jour l'animation
					if(this.y > 0){
						this.y += -10;
					}
					else{
						this.etat = "waiting";
					}
				}
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}

// un objet pour les chargeurs
	var chargeur = {
		x: -50,
		y: 0,
		speed: 5,
		height: 30,
		width: 30,
		vx: 0,
		vy: 0,

		HIDING : 0,
		OBSTACLING : 1,
		state : this.HIDING,
		waitTime: 0,

		updateAnimation: function(){

			// tant que le décompte est pas fini, l'obstacle est caché : 
			if(this.waitTime > 0 || this.waitTime === undefined){
				this.state = this.HIDING;
			}
			else{
				this.state = this.OBSTACLING;
			}


			// puis on test l'état pour savoir quelle
			switch (this.state){

				case this.HIDING:
					// si l'obstacle est caché on décompte (waitTime) le temps d'attente
					this.y = -30;
					this.waitTime--;
					break

				case this.OBSTACLING:
					// a la fin du decompte, l'obstacle fait son bonhomme de chemin
					this.y = this.y + this.speed;
					break
			}

			// gestion de la fin de vie du chargeur
			if(this.y > h ){
				this.state = this.HIDING;
				this.waitTime = 200+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop du chargeur
				this.x = (Math.random()*w);
				if(this.x > w - this.width){
					this.x = w - this.width;
				}
			}
		},

		findRandomStats: function(){ 
			this.state = this.HIDING;
			this.waitTime = 200+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop de l'obstacle
			this.y = -30;
			this.x = (Math.random()*w);
			if(this.x > w - this.width){
				this.x = w - this.width;
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}

///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////  BONUS ET MALUS  ////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////


// un objet pour le bonus Score X2
	var doubleScore = {
		x: -50,
		y: 0,
		speed: 5,
		height: 30,
		width: 30,
		vx: 0,
		vy: 0,
		timer: 0,
		clock: 0,
		hitState: "notHitted",

		HIDING : 0,
		OBSTACLING : 1,
		state : this.HIDING,
		waitTime: 0,

		updateAnimation: function(){

			// tant que le décompte est pas fini, l'obstacle est caché : 
			if(this.waitTime > 0 || this.waitTime === undefined){
				this.state = this.HIDING;
			}
			else{
				this.state = this.OBSTACLING;
			}


			// puis on test l'état pour savoir quelle
			switch (this.state){

				case this.HIDING:
					// si l'obstacle est caché on décompte (waitTime) le temps d'attente
					this.y = -this.height;
					this.waitTime--;
					break

				case this.OBSTACLING:
					// a la fin du decompte, l'obstacle fait son bonhomme de chemin
					this.y = this.y + this.speed;
					break
			}

			// gestion de la fin de vie du chargeur
			if(this.y > h ){
				this.state = this.HIDING;
				this.waitTime = 250+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop du chargeur
				this.x = (Math.random()*w);
				if(this.x > w - this.width){
					this.x = w - this.width;
				}
			}
		},

		findRandomStats: function(){ 
			this.state = this.HIDING;
			this.waitTime = 250+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop de l'obstacle
			this.y = -this.height;
			this.x = (Math.random()*w);
			if(this.x > w - this.width){
				this.x = w - this.width;
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}


// un objet pour le malus reverse
	var reverseTouches = {
		x: -50,
		y: 0,
		speed: 5,
		height: 30,
		width: 30,
		vx: 0,
		vy: 0,
		timer: 0,
		clock: 0,
		hitState: "notHitted",

		HIDING : 0,
		OBSTACLING : 1,
		state : this.HIDING,
		waitTime: 0,

		updateAnimation: function(){

			// tant que le décompte est pas fini, l'obstacle est caché : 
			if(this.waitTime > 0 || this.waitTime === undefined){
				this.state = this.HIDING;
			}
			else{
				this.state = this.OBSTACLING;
			}


			// puis on test l'état pour savoir quelle
			switch (this.state){

				case this.HIDING:
					// si l'obstacle est caché on décompte (waitTime) le temps d'attente
					this.y = -this.height;
					this.waitTime--;
					break

				case this.OBSTACLING:
					// a la fin du decompte, l'obstacle fait son bonhomme de chemin
					this.y = this.y + this.speed;
					break
			}

			// gestion de la fin de vie du chargeur
			if(this.y > h ){
				this.state = this.HIDING;
				this.waitTime = 50+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop du chargeur
				this.x = (Math.random()*w);
				if(this.x > w - this.width){
					this.x = w - this.width;
				}
			}
		},

		findRandomStats: function(){ 
			this.state = this.HIDING;
			this.waitTime = 50+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop de l'obstacle
			this.y = -this.height;
			this.x = (Math.random()*w);
			if(this.x > w - this.width){
				this.x = w - this.width;
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}


// un objet pour le bonus ralentisseur
	var ralentisseur = {
		x: -50,
		y: 0,
		speed: 5,
		height: 30,
		width: 30,
		vx: 0,
		vy: 0,
		timer: 0,
		clock: 0,
		hitState: "notHitted",

		HIDING : 0,
		OBSTACLING : 1,
		state : this.HIDING,
		waitTime: 0,

		updateAnimation: function(){

			// tant que le décompte est pas fini, l'obstacle est caché : 
			if(this.waitTime > 0 || this.waitTime === undefined){
				this.state = this.HIDING;
			}
			else{
				this.state = this.OBSTACLING;
			}


			// puis on test l'état pour savoir quelle
			switch (this.state){

				case this.HIDING:
					// si l'obstacle est caché on décompte (waitTime) le temps d'attente
					this.y = -this.height;
					this.waitTime--;
					break

				case this.OBSTACLING:
					// a la fin du decompte, l'obstacle fait son bonhomme de chemin
					this.y = this.y + this.speed;
					break
			}

			// gestion de la fin de vie du chargeur
			if(this.y > h ){
				this.state = this.HIDING;
				this.waitTime = 250+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop du chargeur
				this.x = (Math.random()*w);
				if(this.x > w - this.width){
					this.x = w - this.width;
				}
			}
		},

		findRandomStats: function(){ 
			this.state = this.HIDING;
			this.waitTime = 250+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop de l'obstacle
			this.y = -this.height;
			this.x = (Math.random()*w);
			if(this.x > w - this.width){
				this.x = w - this.width;
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}


	// un objet pour le MALUS ralentisseur
	var ralentisseurPerso = {
		x: -50,
		y: 0,
		speed: 5,
		height: 30,
		width: 30,
		vx: 0,
		vy: 0,
		timer: 0,
		clock: 0,
		hitState: "notHitted",

		HIDING : 0,
		OBSTACLING : 1,
		state : this.HIDING,
		waitTime: 0,

		updateAnimation: function(){

			// tant que le décompte est pas fini, l'obstacle est caché : 
			if(this.waitTime > 0 || this.waitTime === undefined){
				this.state = this.HIDING;
			}
			else{
				this.state = this.OBSTACLING;
			}


			// puis on test l'état pour savoir quelle
			switch (this.state){

				case this.HIDING:
					// si l'obstacle est caché on décompte (waitTime) le temps d'attente
					this.y = -this.height;
					this.waitTime--;
					break

				case this.OBSTACLING:
					// a la fin du decompte, l'obstacle fait son bonhomme de chemin
					this.y = this.y + this.speed;
					break
			}

			// gestion de la fin de vie du chargeur
			if(this.y > h ){
				this.state = this.HIDING;
				this.waitTime = 50+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop du chargeur
				this.x = (Math.random()*w);
				if(this.x > w - this.width){
					this.x = w - this.width;
				}
			}
		},

		findRandomStats: function(){ 
			this.state = this.HIDING;
			this.waitTime = 50+Math.ceil(Math.random() * 100); // génération aléatoire du temps de repop de l'obstacle
			this.y = -this.height;
			this.x = (Math.random()*w);
			if(this.x > w - this.width){
				this.x = w - this.width;
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}

///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

	// un objet pour le perso
	var perso = {
		life: 4,
		x: 0,
		y: h-90,
		direction : "haut",
		abscisseCrop : 0,
		ordonneeCrop : 0,
		width: 39,
		height: 61,
		speedx: 0,
		speedy: 0,
		barrillet: 10,
		vx: 0,
		vy: 0,

		// mouvements
		moveUp : false,
		moveDown : false,
		moveLeft : false,
		moveRight : false,
		etatMouvement : 0,

		deplacement: function(){
			this.etatMouvement+=1;
			if(this.etatMouvement % 40 == 0){
				this.abscisseCrop += this.width; 
				if(this.abscisseCrop == this.width * 3){
					this.abscisseCrop = 0;
				}
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}

// un objet pour les obstacles
	var obstacle = { 
		x : 0,
		y : 0,
		width : 38,
		height : 49,
		vx : 0,
		vy : 0,
		abscisseCrop: 0,
		etatMouvement: 0,
		spriteNumber : 0,

		trajectoire: 0,

		speed : 0,
		speedDifficulty : 10,

		HIDING : 0,
		OBSTACLING : 1,
		state : this.HIDING,
		waitTime: 0, // le temps d'attente initial


		// la fonction pour initialiser les stats de l'obstacle au début de partie
		findRandomStats: function(){ 
			this.state = this.HIDING;
			this.waitTime = Math.ceil(Math.random() * 50); // génération aléatoire du temps de repop de l'obstacle
			this.speed = 5 + Math.random()*this.speedDifficulty;
			this.y = - this.height;
			this.x = (Math.random()*w);
			if(this.x > w - this.width){
				this.x = w - this.width;
			}
			this.trajectoire = Math.round(Math.random()*10);

			this.spriteNumber = Math.round(Math.random()*3);
		},

		updateAnimation: function(){

			this.etatMouvement+=1;
			
			if(this.etatMouvement % 7 == 0){
				this.abscisseCrop += this.width; 
				if(this.abscisseCrop == this.width * 3){
					this.abscisseCrop = 0;
				}
			}
		

			// tant que le décompte est pas fini, l'obstacle est caché : 
			if(this.waitTime > 0 || this.waitTime === undefined){
				this.state = this.HIDING;
			}
			else{
				this.state = this.OBSTACLING;
			}



			

			// puis on test l'état pour savoir quelle
			switch (this.state){

				case this.HIDING:
					// si l'obstacle est caché on décompte (waitTime) le temps d'attente
					this.y = -this.height;
					this.waitTime--;
					break

				case this.OBSTACLING:
					// a la fin du decompte, l'obstacle fait son bonhomme de chemin
					this.y = this.y + this.speed;

					if(this.trajectoire == 1){
						this.x+=Math.ceil(Math.random()*3);
					}
					if(this.trajectoire == 2){
						this.x+= -Math.ceil(Math.random()*3);
					}

					break

			}

			// gestion de la fin de vie de l'obstacle
			if(this.y > h ){
				this.state = this.HIDING;
				this.waitTime = Math.ceil(Math.random() * 50); // génération aléatoire du temps de repop de l'obstacle
				this.x = (Math.random()*w);
				if(this.x > w - this.width){
					this.x = w - this.width;
				}

				this.speed = 5 + Math.random()*this.speedDifficulty;
			}
		},

		// méthodes pour récupérer les coordonnées utiles au test de collision
		centerX: function()
		{
			return this.x + (this.width / 2);
		},

		centerY: function()
		{
			return this.y + (this.height / 2);
		},

		halfWidth: function()
		{
			return this.width / 2;
		},

		halfHeight: function()
		{
			return this.height / 2;
		}
	}



//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////    fonctions    ///////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////

	function hitTestRectangle(r1, r2)
	{
	  //A variable to determine whether there's a collision
	  var hit = false;
	  
	  //Calculate the distance vector
	  var vx = r1.centerX() - r2.centerX();
	  var vy = r1.centerY() - r2.centerY();
	  
	  //Figure out the combined half-widths and half-heights
	  var combinedHalfWidths = r1.halfWidth() + r2.halfWidth();
	  var combinedHalfHeights = r1.halfHeight() + r2.halfHeight();

	  //Check for a collision on the x axis
	  if(Math.abs(vx) < combinedHalfWidths)
	  {
	    //A collision might be occuring. Check for a collision on the y axis
	    if(Math.abs(vy) < combinedHalfHeights)
	    {
	      //There's definitely a collision happening
	      hit = true;
	    }
	    else
	    {
	      //There's no collision on the y axis
	      hit = false;
	    }   
	  }
	  else
	  {
	    //There's no collision on the x axis
	    hit = false;
	  }
	  
	  return hit;
	}


	// fonction pour dessiner le carré
	function rendu () {


		// initialisation du "tableau de bord" avec les infos du joueur

		// dessin de la vie 

		if(perso.life==4){
			drawVie4(CtxInfosJoueur);
		}
		if(perso.life==3){
			drawVie3(CtxInfosJoueur);
		}
		if(perso.life==2){
			drawVie2(CtxInfosJoueur);
		}
		if(perso.life==1){
			drawVie1(CtxInfosJoueur);
		}		

		ctx.clearRect(30,0,w-30,h);

		// couleur de background
		ctx.fillStyle = "#272822";
		ctx.fillRect(0, 0, w, h);

		//ctx.drawImage(spriteMMI, 50, 50);	


		if(end != 1){
			// dessiner les obstacles
			for(var i = 0 ; i < NumberOfEnnemies ; i++){	
				if(score < 300000){
					ctx.drawImage(spritesMMIFinal, ennemies[i].abscisseCrop, ennemies[i].spriteNumber*ennemies[i].height, ennemies[i].width, ennemies[i].height, ennemies[i].x, ennemies[i].y, ennemies[i].width, ennemies[i].height);	
				}
				else{
					ctx.drawImage(spritesMMIFinal, ennemies[i].abscisseCrop, 203, ennemies[i].width, ennemies[i].height, ennemies[i].x, ennemies[i].y, ennemies[i].width, ennemies[i].height);	
				}
			}
		}

			// dessiner le perso
			ctx.drawImage(perso_src, perso.abscisseCrop, perso.ordonneeCrop, perso.width, perso.height, perso.x, perso.y, perso.width, perso.height);
			
			// dessiner la balle
			ctx.drawImage(bulletImg, bullet.abscisseCrop, 1, bullet.width, bullet.height, bullet.x, bullet.y, bullet.width, bullet.height);

			// dessiner les chargeurs
			// ctx.fillStyle = "green";
			// ctx.beginPath();
			// ctx.fillRect(chargeur.x, chargeur.y, 30, 30);
			ctx.drawImage(bonusImg, 0, 120, chargeur.width, chargeur.height, chargeur.x, chargeur.y, chargeur.width, chargeur.height);

			// dessiner le bonus ralentisseur
			// ctx.fillStyle = "yellow";
			// ctx.beginPath();
			// ctx.fillRect(ralentisseur.x, ralentisseur.y, 30, 30);
			ctx.drawImage(bonusImg, 0, 30, ralentisseur.width, ralentisseur.height, ralentisseur.x, ralentisseur.y, ralentisseur.width, ralentisseur.height);

			// dessiner le bonus ralentisseurPerso
			// ctx.fillStyle = "red";
			// ctx.beginPath();
			// ctx.fillRect(ralentisseurPerso.x, ralentisseurPerso.y, 30, 30);
			ctx.drawImage(bonusImg, 0, 60, ralentisseurPerso.width, ralentisseurPerso.height, ralentisseurPerso.x, ralentisseurPerso.y, ralentisseurPerso.width, ralentisseurPerso.height);

			// dessiner le bonus doubleScore
			// ctx.fillStyle = "blue";
			// ctx.beginPath();
			// ctx.fillRect(doubleScore.x, doubleScore.y, 30, 30);
			ctx.drawImage(bonusImg, 0, 0, doubleScore.width, doubleScore.height, doubleScore.x, doubleScore.y, doubleScore.width, doubleScore.height);

			// dessiner le malus reverseTouches
			// ctx.fillStyle = "grey";
			// ctx.beginPath();
			// ctx.fillRect(reverseTouches.x, reverseTouches.y, 30, 30);
			ctx.drawImage(bonusImg, 0, 90, reverseTouches.width, reverseTouches.height, reverseTouches.x, reverseTouches.y, reverseTouches.width, reverseTouches.height);
	}

	function drawVie4(ctx){

	  ctx.clearRect(0,0, 600, 100);
	  ctx.save();

      // calque1/Ecrter le groupe

      // calque1/Ecrter le groupe/Masque
      ctx.save();
      ctx.beginPath();
      ctx.moveTo(0.0, 30.6);
      ctx.lineTo(41.1, 30.6);
      ctx.lineTo(41.1, 0.0);
      ctx.lineTo(0.0, 0.0);
      ctx.lineTo(0.0, 30.6);
      ctx.closePath();
      ctx.clip();

      // calque1/Ecrter le groupe/Trac transparent
      ctx.beginPath();

      // calque1/Ecrter le groupe/Trac transparent/Trac
      ctx.moveTo(39.6, 9.4);
      ctx.bezierCurveTo(29.1, -1.1, 12.1, -1.1, 1.6, 9.4);

      // calque1/Ecrter le groupe/Trac transparent/Trac
      ctx.moveTo(35.5, 14.7);
      ctx.bezierCurveTo(27.3, 6.5, 13.9, 6.5, 5.6, 14.7);

      // calque1/Ecrter le groupe/Trac transparent/Trac
      ctx.moveTo(30.4, 20.0);
      ctx.bezierCurveTo(25.0, 14.6, 16.2, 14.6, 10.8, 20.0);
      ctx.lineWidth = 3.1;
      ctx.strokeStyle = "rgb(121, 200, 65)";
      ctx.lineCap = "round";
      ctx.stroke();

      // calque1/Ecrter le groupe/Trac
      ctx.beginPath();
      ctx.moveTo(25.7, 25.3);
      ctx.bezierCurveTo(25.7, 28.2, 23.3, 30.6, 20.4, 30.6);
      ctx.bezierCurveTo(17.5, 30.6, 15.1, 28.2, 15.1, 25.3);
      ctx.bezierCurveTo(15.1, 22.4, 17.5, 20.0, 20.4, 20.0);
      ctx.bezierCurveTo(23.3, 20.0, 25.7, 22.4, 25.7, 25.3);
      ctx.fillStyle = "rgb(121, 200, 65)";
      ctx.fill();
      ctx.restore();
      ctx.restore();
	}

	function drawVie3(ctx){
      ctx.clearRect(0,0, 600, 100);
	  ctx.save();

      // calque1/Ecrter le groupe

      // calque1/Ecrter le groupe/Masque
      ctx.save();
      ctx.beginPath();
      ctx.moveTo(4.1, 30.6);
      ctx.lineTo(37.1, 30.6);
      ctx.lineTo(37.1, 7.0);
      ctx.lineTo(4.1, 7.0);
      ctx.lineTo(4.1, 30.6);
      ctx.closePath();
      ctx.clip();

      // calque1/Ecrter le groupe/Trac transparent
      ctx.beginPath();

      // calque1/Ecrter le groupe/Trac transparent/Trac
      ctx.moveTo(35.5, 14.7);
      ctx.bezierCurveTo(27.3, 6.5, 13.9, 6.5, 5.6, 14.7);

      // calque1/Ecrter le groupe/Trac transparent/Trac
      ctx.moveTo(30.4, 20.0);
      ctx.bezierCurveTo(25.0, 14.6, 16.2, 14.6, 10.8, 20.0);
      ctx.lineWidth = 3.1;
      ctx.strokeStyle = "rgb(255, 211, 0)";
      ctx.lineCap = "round";
      ctx.stroke();

      // calque1/Ecrter le groupe/Trac
      ctx.beginPath();
      ctx.moveTo(25.7, 25.3);
      ctx.bezierCurveTo(25.7, 28.2, 23.3, 30.6, 20.4, 30.6);
      ctx.bezierCurveTo(17.5, 30.6, 15.1, 28.2, 15.1, 25.3);
      ctx.bezierCurveTo(15.1, 22.4, 17.5, 20.0, 20.4, 20.0);
      ctx.bezierCurveTo(23.3, 20.0, 25.7, 22.4, 25.7, 25.3);
      ctx.fillStyle = "rgb(255, 211, 0)";
      ctx.fill();
      ctx.restore();
      ctx.restore();
	}

	function drawVie2(ctx){
	  ctx.clearRect(0,0, 600, 100);
	  ctx.save();

      // calque1/Ecrter le groupe

      // calque1/Ecrter le groupe/Masque
      ctx.save();
      ctx.beginPath();
      ctx.moveTo(9.2, 30.6);
      ctx.lineTo(31.9, 30.6);
      ctx.lineTo(31.9, 14.4);
      ctx.lineTo(9.2, 14.4);
      ctx.lineTo(9.2, 30.6);
      ctx.closePath();
      ctx.clip();

      // calque1/Ecrter le groupe/Trac
      ctx.beginPath();
      ctx.moveTo(30.4, 20.0);
      ctx.bezierCurveTo(25.0, 14.6, 16.2, 14.6, 10.7, 20.0);
      ctx.lineWidth = 3.1;
      ctx.strokeStyle = "rgb(246, 147, 30)";
      ctx.lineCap = "round";
      ctx.stroke();

      // calque1/Ecrter le groupe/Trac
      ctx.beginPath();
      ctx.moveTo(25.7, 25.3);
      ctx.bezierCurveTo(25.7, 28.2, 23.3, 30.6, 20.4, 30.6);
      ctx.bezierCurveTo(17.5, 30.6, 15.1, 28.2, 15.1, 25.3);
      ctx.bezierCurveTo(15.1, 22.4, 17.5, 20.0, 20.4, 20.0);
      ctx.bezierCurveTo(23.3, 20.0, 25.7, 22.4, 25.7, 25.3);
      ctx.fillStyle = "rgb(246, 147, 30)";
      ctx.fill();
      ctx.restore();
      ctx.restore();
	}

	function drawVie1(ctx){
	  ctx.clearRect(0,0, 600, 100);
	  ctx.save();

      // calque1/Ecrter le groupe

      // calque1/Ecrter le groupe/Masque
      ctx.save();
      ctx.beginPath();
      ctx.moveTo(15.0, 30.6);
      ctx.lineTo(25.6, 30.6);
      ctx.lineTo(25.6, 20.0);
      ctx.lineTo(15.0, 20.0);
      ctx.lineTo(15.0, 30.6);
      ctx.closePath();
      ctx.clip();

      // calque1/Ecrter le groupe/Trac
      ctx.beginPath();
      ctx.moveTo(25.7, 25.3);
      ctx.bezierCurveTo(25.7, 28.2, 23.3, 30.6, 20.4, 30.6);
      ctx.bezierCurveTo(17.5, 30.6, 15.1, 28.2, 15.1, 25.3);
      ctx.bezierCurveTo(15.1, 22.4, 17.5, 20.0, 20.4, 20.0);
      ctx.bezierCurveTo(23.3, 20.0, 25.7, 22.4, 25.7, 25.3);
      ctx.fillStyle = "rgb(255, 27, 37)";
      ctx.fill();
      ctx.restore();
      ctx.restore();
	}





//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
/////   fonction pour mettre à jour le dessin de l'obstacle toutes les 50ms   ////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////


	function updateAnimation () {


		var rappel = setTimeout(updateAnimation, 1000/24); // la fonction se rappelle elle même

		if(score > 50000){
			document.getElementById("harp").muted = false;
		}
		if(score > 75000){
			document.getElementById("bass").muted = false;
		}
		if(score > 25000){
			document.getElementById("jingleBell").muted = false;
		}
		if(score > 100000){
			document.getElementById("violin").muted = false;
		}
		// if(score > 15000){
		// 	document.getElementById("bass").muted = false;
		// }
		// if(score > 25000){
		// 	document.getElementById("organ").muted = false;
		// }
		// if(score > 50000){
		// 	document.getElementById("horn").muted = false;
		// }

		bullet.updateAnimation();

		chargeur.updateAnimation();

		ralentisseur.updateAnimation();

		ralentisseurPerso.updateAnimation();

		doubleScore.updateAnimation();

		reverseTouches.updateAnimation();


		perso.x += perso.speedx;
		perso.y += perso.speedy;
		
		
		if(perso.x<0){
			perso.x = 0;
		}

		if(perso.x + perso.width > w){
			perso.x = w - perso.width;
		}

		if(perso.y + perso.height > h){
			perso.y = h - perso.height;
		}

		if(perso.y < 0 ){
			perso.y = 0;
		}

		// gestion de la récupération de vie
		// on récupère une barre de vie toutes les 1000 unités de temps (rappel), ce qui équivaut à environ 41 secondes.
		if(rappel%1000 == 0 ){
			perso.life += 1;
		}


/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////   tests de collisons   ////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

/////////////// test de la collision du perso avec le chargeur

		if(hitTestRectangle(perso, chargeur))
		  {
		  	lowLag.play("sons_bonus");
		    perso.barrillet = 10;
			document.getElementById("balles").textContent = "Chargeur : " + perso.barrillet;
			chargeur.findRandomStats();
		  }


		 
		  // test de collision avec le ralentisseur
			if(hitTestRectangle(perso, ralentisseur))
			  { 
			  	lowLag.play("sons_bonus");	
			  	ralentisseur.hitState = "hitted";  	
			  	ralentisseur.findRandomStats();
			  }

				if(ralentisseur.timer < 10 && ralentisseur.hitState == "hitted"){
					ralentisseur.timer+=0.1;
					for (var j = 0; j < NumberOfEnnemies; j++) {
					  	ennemies[j].speed = 1;
				  	}
				}
				if(ralentisseur.timer > 10){
				 	for (var j = 0; j < NumberOfEnnemies; j++) {
					  	ennemies[j].speed = 5 + Math.random()*ennemies[j].speedDifficulty;
					  	ralentisseur.hitState = "notHitted";
					  	ralentisseur.timer = 0;
				   	}
				}

/////////////// test de collision avec le ralentisseurPerso
			if(hitTestRectangle(perso, ralentisseurPerso))
			  { 
			  	lowLag.play("sons_malus");	
			  	ralentisseurPerso.hitState = "hitted";  	
			  	ralentisseurPerso.findRandomStats();
			  }

				if(ralentisseurPerso.timer < 10 && ralentisseurPerso.hitState == "hitted"){
					ralentisseurPerso.timer+=0.1;

					if(perso.moveUp && !perso.moveDown)
					{
					  	perso.speedy = -4;
						perso.ordonneeCrop = perso.height;
						perso.deplacement();
						perso.direction="haut";
					}
					 
					  //Down
					  if(perso.moveDown && !perso.moveUp)
					  {
					   	perso.speedy = 4;
						perso.ordonneeCrop = 0;
						perso.direction="bas";
						perso.deplacement();
					  }
					
					  //Left
					  if(perso.moveLeft && !perso.moveRight)
					  {
					    perso.speedx = -4;
						perso.ordonneeCrop = perso.height * 2;
						perso.deplacement();
						perso.direction="gauche";
					  }
					
					  //Right
					  if(perso.moveRight && !perso.moveLeft)
					  {
					    perso.speedx = 4;	
						perso.ordonneeCrop = perso.height * 3 - 3; // les images en png c'est pas cool
						perso.deplacement();
						perso.direction="droite";
					  }

					  if(!perso.moveUp && !perso.moveDown)
					  {
					    perso.speedy = 0;
					  }
					  if(!perso.moveLeft && !perso.moveRight)
					  {
					    perso.speedx = 0;
					  }

					  if(perso.speedx == 0 && perso.speedy == 0){
					  	perso.abscisseCrop = 0;
					  }
				}

				if(ralentisseurPerso.timer > 10){
				 	ralentisseurPerso.hitState = "notHitted";
					ralentisseurPerso.timer = 0;
				}



/////////////// test de collision avec le reverseTouches
			if(hitTestRectangle(perso, reverseTouches))
			  { 
			  	lowLag.play("sons_malus");	
			  	reverseTouches.hitState = "hitted";  	
			  	reverseTouches.findRandomStats();
			  }

				if(reverseTouches.timer < 20 && reverseTouches.hitState == "hitted"){
					reverseTouches.timer+=0.1;

					if(perso.moveUp && !perso.moveDown)
					{
					  	perso.speedy = 8;
						perso.ordonneeCrop = perso.height;
						perso.deplacement();
						perso.direction="bas";
					}
					 
					  //Down
					  if(perso.moveDown && !perso.moveUp)
					  {
					   	perso.speedy = -8;
						perso.ordonneeCrop = 0;
						perso.direction="haut";
						perso.deplacement();
					  }
					
					  //Left
					  if(perso.moveLeft && !perso.moveRight)
					  {
					    perso.speedx = 8;
						perso.ordonneeCrop = perso.height * 2;
						perso.deplacement();
						perso.direction="droite";
					  }
					
					  //Right
					  if(perso.moveRight && !perso.moveLeft)
					  {
					    perso.speedx = -8;	
						perso.ordonneeCrop = perso.height * 3 - 3; // les images en png c'est pas cool
						perso.deplacement();
						perso.direction="gauche";
					  }

					  if(!perso.moveUp && !perso.moveDown)
					  {
					    perso.speedy = 0;
					  }
					  if(!perso.moveLeft && !perso.moveRight)
					  {
					    perso.speedx = 0;
					  }

					  if(perso.speedx == 0 && perso.speedy == 0){
					  	perso.abscisseCrop = 0;
					  }
				}

				if(reverseTouches.timer > 20){
				 	reverseTouches.hitState = "notHitted";
					reverseTouches.timer = 0;
				}



///////////////// test de collision avec le doubleScore
				if(hitTestRectangle(perso, doubleScore)){ 
					lowLag.play("sons_bonus");	
				  	doubleScore.hitState = "hitted";  	
				  	doubleScore.findRandomStats();
				}

				if(doubleScore.timer < 10 && doubleScore.hitState == "hitted"){
					doubleScore.timer+=0.1;
					scoreState = "multiple";	
				}

				if(doubleScore.timer > 10){
			 		scoreState = "normal";
				  	doubleScore.hitState = "notHitted";
				  	doubleScore.timer = 0;
				}

			



		for(var i = 0 ; i < NumberOfEnnemies ; i++){

			ennemies[i].updateAnimation();	// on met à jour l'état de l'obstacle et tout le bazar


			// test de collision avec les ennemis
			if(hitTestRectangle(perso, ennemies[i]))
			  {

			    ennemies[i].findRandomStats();
	
				perso.life+= -1; 

				lowLag.play("sons_ennemy_collision");

				if (perso.life == 0){
					clearTimeout(rappel);
					var lesSons = document.getElementsByTagName("audio");
					for (var i = 0; i < lesSons.length; i++) {
						lesSons[i].muted = true;
					};
					save_score();
				}
			  }

			// test de collision de la balle avec les ennemis
			if(hitTestRectangle(bullet, ennemies[i]))
			  {
			    bullet.etat = "waiting";
				ennemies[i].findRandomStats();
				score = score +1000;
				bullet.y = -100;
				bullet.abscisseCrop = 1 + 30 * Math.floor(Math.random()*6);
			  }


/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////     gestion du score     //////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

			if(scoreState == "normal"){
				score = score + 1;
			}
			else{
				if(scoreState == "multiple"){
					score = score + 10;
				}
			}
				

			pscore.textContent = score + " points";


/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////     gestion de la difficulté     //////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

				if (NumberOfEnnemies<50 && compteurEnnemies=="waiting") {
					compteurEnnemies = "enclanched";
					var incrementEnnemy = window.setInterval(function(){
						NumberOfEnnemies += 1;
						
						if(obstacle.speedDifficulty < 20){
							obstacle.speedDifficulty+=0.3;
						}
					}, 10000);
				}

				if(NumberOfEnnemies > 48){
					clearInterval(incrementEnnemy);
					NumberOfEnnemies = 50;
				}



/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////     gestion de quoi font les touches du clavier     ///////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

				if(ralentisseurPerso.hitState == "notHitted" && reverseTouches.hitState == "notHitted"){			
					if(perso.moveUp && !perso.moveDown)
					{
					  	perso.speedy = -8;
						perso.ordonneeCrop = perso.height;
						perso.deplacement();
						perso.direction="haut";
					}
					 
					  //Down
					  if(perso.moveDown && !perso.moveUp)
					  {
					   	perso.speedy = 8;
						perso.ordonneeCrop = 0;
						perso.direction="bas";
						perso.deplacement();
					  }
					
					  //Left
					  if(perso.moveLeft && !perso.moveRight)
					  {
					    perso.speedx = -8;
						perso.ordonneeCrop = perso.height * 2;
						perso.deplacement();
						perso.direction="gauche";
					  }
					
					  //Right
					  if(perso.moveRight && !perso.moveLeft)
					  {
					    perso.speedx = 8;	
						perso.ordonneeCrop = perso.height * 3 - 3; // les images en png c'est pas cool
						perso.deplacement();
						perso.direction="droite";
					  }
					  

					  if(!perso.moveUp && !perso.moveDown)
					  {
					    perso.speedy = 0;
					  }
					  if(!perso.moveLeft && !perso.moveRight)
					  {
					    perso.speedx = 0;
					  }

					  if(perso.speedx == 0 && perso.speedy == 0){
					  	perso.abscisseCrop = 0;
					  }
				}

			rendu();
		}			
	}



//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
/////////////////  gestion des appuis de touches ou non   ////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////



	window.addEventListener("keydown", function(event)
			{
			  switch(event.keyCode)
			  {
			    case 38: // aller en haut
			    	perso.moveUp = true;
				    break;
				  

				case 40: // aller en bas
					perso.moveDown = true;
				    break;
				    

				case 37: // aller a gauche
					perso.moveLeft = true;
				    break;  
				    

				case 39: // aller a droite
					perso.moveRight = true;
				    break; 


				case 32: // tir
					if(bullet.etat == "waiting" && perso.barrillet > 0 ){ // on ne peut pas tirer tant qu'une balle est déjà tirée
						
						
						if(bullet.note > 8){
							bullet.note = 0;
						}

						if(bullet.note == 0){
							lowLag.play("sons_re");
						}
						if(bullet.note == 1){
							lowLag.play("sons_fa");
						}
						if(bullet.note == 2){
							lowLag.play("sons_mi");
						}
						if(bullet.note == 3){
							lowLag.play("sons_fa");
						}
						if(bullet.note == 4){
							lowLag.play("sons_sol");
						}
						if(bullet.note == 5){
							lowLag.play("sons_la");
						}
						if(bullet.note == 6){
							lowLag.play("sons_fa");
						}
						if(bullet.note == 7){
							lowLag.play("sons_mi");
						}
						if(bullet.note == 8){
							lowLag.play("sons_re");
						}

						bullet.note += 1;

						//lowLag.play("son_re");
						bullet.etat = "tir";
						bullet.x = perso.x+5;
						bullet.y = perso.y - bullet.halfHeight();
						perso.barrillet += -1;
						document.getElementById("balles").textContent = "Chargeur : " + perso.barrillet;
					}
					break;		
			  
			  }  
			}, false);


	window.addEventListener("keyup", function(event)
			{

			  switch(event.keyCode)
			  {
			    case 38:
				    perso.moveUp = false;
				    break;
				  
				  case 40:
				    perso.moveDown = false;
				    break;
				    
				  case 37:
				    perso.moveLeft = false;
				    break;  
				    
				  case 39:
				    perso.moveRight = false;
				    break; 

				  case 32:
				  	//document.getElementById("son_do").pause();
				  	break;
			  }
			}, false);




// création du tableau contenant les obstacles
	for (var i = 0 ; i < 100 ; i++){
		var ObstacleObject = Object.create(obstacle);	
		ObstacleObject.findRandomStats();	
		ennemies.push(ObstacleObject);

	}

	window.addEventListener("load", updateAnimation, false);

</script>

</body>
</html>
