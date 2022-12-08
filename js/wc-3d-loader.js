var wc_3d_loader = (function(fileUrl, objType){

	var progress = document.getElementById("progress");
	var progressBar = document.getElementById("progressbar");
	var loaderhtml = document.getElementById("loaderhtml");

	//Loading Manager
	var manager = new THREE.LoadingManager();

	manager.onStart = function ( url, itemsLoaded, itemsTotal ) {
		loaderhtml.style.visibility = 'visible';
	};

	manager.onLoad = function ( ) {
		loaderhtml.style.visibility = 'hidden';
		rcEventQueue();
	};

	manager.onProgress = function ( url, itemsLoaded, itemsTotal ) {
		progress.style.width = (itemsLoaded / itemsTotal * 100) + '%';
	};

	manager.onError = function ( url ) {};

	//Main 3D model Loading Script
	if(objType == 'dae') {
		var loader = new THREE.ColladaLoader( manager );
		loader.load( fileUrl, function ( collada ) {
			mainObj = collada.scene;
		} );
	} else if(objType == 'fbx') {
		var loader = new THREE.FBXLoader( manager );
		loader.load( fileUrl, function ( object ) {
			mainObj = object;
		});
	} else if(objType == 'json') {
		var loader = new THREE.ObjectLoader( manager );
		loader.load( fileUrl, function ( geometry, materials ) {
			mainObj = geometry;
		});
	} else if(objType == 'gltf'){
		var loader = new THREE.GLTFLoader( manager );
		loader.load( fileUrl, function ( gltf ) {
			mainObj = gltf.scene;
		});
	}

	function rcEventQueue(){
		events.emit('modelLoaded', mainObj);
	}

});