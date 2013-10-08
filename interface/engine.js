function Engine(){
	// Grabs data from the server to initialize the game state
	this.updateState(undefined);
	this.keys = {
					'left':false,
					'right':false,
					'up':false,
					'down':false
				};

	this.FPS = 60; // this is the FPS it aims to run at
	this.updateCounter = 0;

	setInterval(Engine.mainLoop.bind(this), // Hack to get past this being set to the wrong
				(this.FPS / 1000))
}

// sends the user data and downloads a JSON object
// ONLY RUNS ONCE PER SECOND
// @param mixed userData An object generated by the Engine object, or undefined for the first connection
Engine.prototype.updateServersideState = function(userData){
	if(userData === undefined){
		var arguments = "";
	}else{
		var arguments = "userData="JSON.stringify(userData)
	}

	var http = new XMLHttpRequest();
	
	http.abort();
	http.open("POST", "updateState.php", true);
	http.onreadystatechange=function() {
		alert("TODO");
	}
	http.setRequestHeader("Content-length", arguments.length);
	http.setRequestHeader('Content-Type', "application/x-www-form-urlencoded; charset=utf-8");
	http.send(parametros);
}

Engine.prototype.keysPressed = function(e){
	e = e || window.event;

	action = (e.type == "keydown") ? true : false;

	switch(e.keyCode){
		case '37': // left
			this.keys.left = action;
			break;
		case '38': // up
			this.keys.up = action;
			break;
		case '39': // right
			this.keys.right = action;
			break;
		case '40': // down
			this.keys.down = action;
			break;
	}
}

// This is used with setInterval
Engine.prototype.mainLoop = function(){
	var userData = this.updateMainChar();

	if(this.updateCounter >= this.FPS){ // Only sends and gets the new data sporadically as to not DDos the server
		this.updateServersideState(userData);
		this.updateCounter = 0;
	}else{
		this.updateCounter += 1;
	}


}