/* wc_3d_product_version: 2.6.0 */
var events = (function() {

	var events = {};

	function on(eventName, fn) {
		events[eventName] = events[eventName] || [];
		events[eventName].push(fn);
	}

	function off(eventName, fn) {
		if (events[eventName]) {
			for (var i = 0; i < events[eventName].length; i++) {
				if( events[eventName][i] === fn ) {
					events[eventName].splice(i, 1);
					break;
				}
			}
		}
	}

	function emit(eventName, data) {
		if (events[eventName]) {
			events[eventName].forEach(function(fn) {
				fn(data);
			});
		}
	}

	return {
		on: on,
		off: off,
		emit: emit
	};

})();

window.$ = window.jQuery = jQuery;

/* 3D Player buttons like Play / Full Screen */
var rcPlayer = (function(){

	var $rcPlayerWrapper = $('#wc-3d-wrapper');

	var $fullScreenToggle = $('.fullscreen_toggle');
	var $playToggle = $('.play_pause_toggle');
	var $saveButton = $('.save_image');
	
	var strDownloadMime = "image/octet-stream";

	var product_title = $('h1.product_title').html();

	$fullScreenToggle.addClass('nofullscreen');
	$playToggle.addClass('play');

	var originalW = $rcPlayerWrapper.width();
	var originalH = $rcPlayerWrapper.height();
	var $wc_3d_materials = $('.wc_3d_materials');
	var canvas = $("#wc_3d");

	var controls, renderer;

	events.on('getControls', setControls);
	events.on('renderedLoaded', setRenderer);

	$fullScreenToggle.on('click', rcScreenToggle);
	$playToggle.on('click', playPauseToggle);
	$saveButton.on('click', rcSaveCanvas);

	function setControls(data){
		controls = data;
	}

	function setRenderer(data){
		renderer = data;
	}


	function rcScreenToggle(){

		if($fullScreenToggle.hasClass('nofullscreen')){

			$fullScreenToggle.removeClass( 'nofullscreen' );
			$fullScreenToggle.removeClass( 'fa-compress');
			$fullScreenToggle.addClass( 'fa-compress-arrows-alt');
			$fullScreenToggle.addClass( 'fullscreen' );

			$rcPlayerWrapper.addClass('fullscreenmode');
			$rcPlayerWrapper.width($(window).width() - 20);
			$rcPlayerWrapper.height($(window).height());

			$wc_3d_materials.width( $wc_3d_materials.width());
			$wc_3d_materials.height( $wc_3d_materials.height());
			$wc_3d_materials.addClass('fullscreenmode');

		} else {

			$fullScreenToggle.removeClass( 'fullscreen' );
			$fullScreenToggle.addClass( 'nofullscreen' );
			$fullScreenToggle.addClass( 'fa-compress');
			$fullScreenToggle.removeClass( 'fa-compress-arrows-alt');

			$rcPlayerWrapper.removeClass('fullscreenmode');
			$rcPlayerWrapper.width(originalW);
			$rcPlayerWrapper.height(originalH);

			$wc_3d_materials.width( $wc_3d_materials.width());
			$wc_3d_materials.height( $wc_3d_materials.height());
			$wc_3d_materials.removeClass('fullscreenmode');
		}

		rcWindowResize.onWindowResize($rcPlayerWrapper);

	}

	function playPauseToggle(){

		if($playToggle.hasClass('play')){
			$playToggle.removeClass( 'play' );
			$playToggle.addClass( 'pause' );
			controls.autoRotate = true;
		} else {
			$playToggle.removeClass( 'pause' );
			$playToggle.addClass( 'play' );
			controls.autoRotate = false;
		}
	}

	function rcSaveCanvas() {
		var imgData, imgNode;
		try {
			var strMime = "image/jpeg";
			imgData = renderer.domElement.toDataURL(strMime);
			saveFile(imgData.replace(strMime, strDownloadMime), product_title+".jpg");
		} catch (e) {
			return;
		}
	}

})();


/* Get Product 3D Data with Ajax */
var wc_3d_product_Get_data = (function(){

	var productID = $("#productId").val();

	if(productID == null){
		var id, matches = document.body.className.match(/(^|\s)postid-(\d+)(\s|$)/);
		if (matches) {
				productID = matches[2];
		}
	}

	var action = 'wc_rc_getproductdata';

	return jQuery.ajax({
		type: 'POST',
		url: wc3dProductJs.ajaxUrl,
		data: {
			'action': action,
			'productID': productID,
		},
		success: function (data) {

		}
	})

})();

/* After receiving product data, pub event so other function receive it */
wc_3d_product_Get_data.success(function (data) {
	
	events.emit('ajaxDataReceived', data);

});

/* 3D Canvas / Light / Camera / Render and Complete Scene builder */
var wcRcSceneBuilder = (function(data){

	if ( ! Detector.webgl ) Detector.addGetWebGLMessage();

	var scene, renderer, camera, mainObj, controls, stats, wc_3d_debug_activate, wc_3d_product_data, obj_position, obj_pos_array;

	var canvas = document.getElementById("wc_3d");
	var parent = canvas.parentNode;

	events.on('ajaxDataReceived', startInit);
	events.on('modelLoaded', setModelData);


	/* Initi Function runs when ajax data receive */
	function startInit(data){

		wc_3d_product_data = jQuery.parseJSON(data);

		wc_3d_debug_activate = wc_3d_product_data['wc_3d_debug_activate'];

		if(wc_3d_debug_activate == 'on') {
			stats = new Stats();
			parent.appendChild( stats.dom );
		}

		obj_position = wc_3d_product_data['obj_position'];
		obj_pos_array = obj_position.split(',');

		renderer = new THREE.WebGLRenderer({ canvas: canvas,  alpha: true, antialias: true, preserveDrawingBuffer: true });
		renderer.setPixelRatio( window.devicePixelRatio );
		renderer.setSize( parent.offsetWidth, parent.offsetHeight );

		//Casting Shadow
		renderer.shadowMap.enabled = true;
		renderer.shadowMapSoft = true;
		renderer.shadowMap.type = THREE.PCFShadowMap;

		//Canvas Background
		var canvas_bg = wc_3d_product_data['canvas_bg'];
		var canvas_transparant = wc_3d_product_data['canvas_transparant'];

		scene = new THREE.Scene();
		if(canvas_transparant == 'on'){
				
		} else {
			if(canvas_bg != ''){
				scene.background = new THREE.Color( canvas_bg );	
			} else {
				scene.background = new THREE.Color( 0xcccccc );
			}
		}

		rcCamera();
		rcLights();
		rcCustomLights();
		rcEnvMap();
		rcLoader();
		rcShadowPlan();
		rcControls();
		animate();

		events.emit('renderedLoaded', renderer);
		events.emit('getCamera', camera);
		events.emit('getControls', controls);
		
	}

	/* Environment HDR Map Function */
	function rcEnvMap(){

		var env_map = wc_3d_product_data['env_map'];
		var env_bg = wc_3d_product_data['env_bg'];

		if(env_map != ''){

			new THREE.RGBELoader().setDataType( THREE.UnsignedByteType ).load( env_map, function ( texture ) {
				const pmremGenerator = new THREE.PMREMGenerator(renderer);
				var envMap = pmremGenerator.fromEquirectangular( texture ).texture;
				
				if(env_bg == 'on')
					scene.background = envMap;

				scene.environment = envMap;
				texture.dispose();
				pmremGenerator.dispose();
			});
		}

	}

	/* Camera Function */
	function rcCamera(){

		camera = new THREE.PerspectiveCamera( 45, parent.offsetWidth / parent.offsetHeight, 0.1, 2000 );

		//Camera Position
		var camera_position = wc_3d_product_data['camera_position'];
		if(camera_position) {
			var camera_pos_array = camera_position.split(',');
			camera.position.set(camera_pos_array[0], camera_pos_array[1], camera_pos_array[2]);
		} else {
			camera.position.set( 50, 200, 180 );
		}

		//Camera Rotation
		var camera_rotation = wc_3d_product_data['camera_rotation'];
		if(camera_rotation) {
			var camera_rot_array = camera_rotation.split(',');
			camera.rotation.set(camera_rot_array[0], camera_rot_array[1], camera_rot_array[2]);
		} else {
			camera.rotation.set(0, 0, 45);
		}

	}

	/* Default Light Funtion */
	function rcLights(){

		var cast_shadow_option = wc3dProductJs.cast_shadow;

		var disable_lights = wc_3d_product_data['disable_lights'];
		if(disable_lights == 'on'){
			//No default Light
		} else {
			hemiLight = new THREE.HemisphereLight( 0xffffff, 0xeeeeee, 0.6 );
			hemiLight.position.set( 0, 200, 0 );
			scene.add( hemiLight );
			
			dirLight = new THREE.DirectionalLight( 0xffffff, 0.4 );
			dirLight.position.set( -35, 175, 190 );
			if(cast_shadow_option == 'yes'){
				dirLight.castShadow = true;
				const d = 100;
				dirLight.shadow.camera.left = - d;
				dirLight.shadow.camera.right = d;
				dirLight.shadow.camera.top = d;
				dirLight.shadow.camera.bottom = - d;
				dirLight.shadow.mapSize.width = 4096;
				dirLight.shadow.mapSize.height = 4096;
			}
			camera.add( dirLight );
			scene.add( camera );
		}

	}

	//Creating Additional Lights based added in backend settings
	function rcCustomLights(){
		var lights = wc_3d_product_data['lights'];
		if(jQuery.isArray(lights) != '-1' && lights != '' ) {
			lights.forEach(function(element , i) {
				var light_json = element;
				var light_type = light_json['light_type'];
				var light_intensity = light_json['light_intensity'];
				if(!light_intensity) light_intensity = 1;
				var light_color = light_json['light_color'];
				if(!light_color) light_color = '#ffffff';
				var light_location = light_json['light_location'];
				if(!light_location) light_location = '0, 0, 0';
				var light_location_array = light_location.split(',');
				var light_shadow = light_json['light_shadow'];
				if(!light_shadow) light_shadow = 'no';

				if(light_type == 'point'){
					var light_new = new THREE.PointLight( light_color, light_intensity );
					light_new.position.set(light_location_array[0], light_location_array[1], light_location_array[2]);
					if(light_shadow == 'yes') {
						light_new.castShadow = true;
					}
					scene.add(light_new);
				} else if(light_type == 'directional') {
					var light_new = new THREE.DirectionalLight( light_color, light_intensity );
					light_new.position.set(light_location_array[0], light_location_array[1], light_location_array[2]);
					if(light_shadow == 'yes') {
						light_new.castShadow = true;
					}
					scene.add(light_new);
				} else if(light_type == 'ambient') {
					var light_new = new THREE.AmbientLight( light_color, light_intensity );
					scene.add(light_new);
				}
			});
		}
	}

	/* Product 3D Model Loader - Actual Loader is in wc-3d-loader.js */
	function rcLoader(){
		var obj_type = wc_3d_product_data['wc_3d_object_type'];
		wc_3d_loader(wc_3d_product_data['obj_file'], obj_type);
	}

	/* after loading model, set as scene main object */
	function setModelData(data){
		scene.remove( mainObj );
		mainObj = data;
		addModeltoScene(mainObj);
	}

	/* After making main object, place it in scene at position set in backend */
	function addModeltoScene(mainObj){

		mainObj.traverse( function ( child ) {
			if ( child.isMesh ) {
				child.castShadow = true;
			}
		} );
		if(obj_position){
			mainObj.position.set(obj_pos_array[0], obj_pos_array[1], obj_pos_array[2]);
		} else {
			mainObj.position.set(0,-40,0);
		}
		scene.add( mainObj );
		camera.lookAt( mainObj.position );
	}
		
	//Creating Shadow Mat Plan to display shadow of object - Not Working Currently - Waiting for setting to be added in backend
	function rcShadowPlan() {

		var shadowMat = new THREE.ShadowMaterial();
		shadowMat.opacity = 0.2;
		var plan = new THREE.PlaneBufferGeometry( 500, 500, 100, 100 );
		var shadowplan = new THREE.Mesh( plan, shadowMat );
		if(obj_position) {
			shadowplan.position.y = obj_pos_array[1];
		} else {
			shadowplan.position.y = -40;
		}
		shadowplan.rotation.x = - Math.PI / 2;
		shadowplan.receiveShadow = true;
		//scene.add( shadowplan );
	}


	/* Camera Orbit Control Setting */
	function rcControls(){

		var allow_downside = wc3dProductJs.allow_downside;
		//Camera Orbit Control
		controls = new THREE.OrbitControls(camera, canvas);
		controls.enablePan = false;
		controls.enableZoom = true;
		if(wc_3d_product_data['camera_zoom_min'] != ''){
			controls.minDistance = wc_3d_product_data['camera_zoom_min'];
		}
		if(wc_3d_product_data['camera_zoom_max'] != ''){
			controls.maxDistance = wc_3d_product_data['camera_zoom_max'];
		}

		//Allowing to see below object or not
		if(allow_downside == 'no'){
			controls.maxPolarAngle = Math.PI / 2;
		}

	}

	/* Animate Function */
	function animate() {
		requestAnimationFrame( animate );
		controls.update();
		render();

		if(wc_3d_debug_activate == 'on') {
			stats.update();
		}

	}

	//Render Function
	function render() {
		renderer.render( scene, camera );
	}

})();

/* Window Resize - Canvas & Model Resize Function */
var rcWindowResize = (function(){

	var camera, renderer;

	events.on('getCamera', setCamera);
	events.on('renderedLoaded', setRenderer);

	function setCamera(data){
		camera = data;
	}

	function setRenderer(data){
		renderer = data;
	}

	function onWindowResize(parent){

		renderer.setPixelRatio( window.devicePixelRatio );
		renderer.setSize( parent.width(), parent.height() );
		camera.fov = 45;
		camera.aspect = parent.width() / parent.height();
		camera.updateProjectionMatrix();
	}

	return { onWindowResize };

})();

/* Initial Objects Show / Hide for multi object Model */
var initialObjectShowHide = (function(){

	events.on('modelLoaded', initialObjectShow);

	var object_initial_data = $('.object_initial_data');

	function initialObjectShow(mainObj){

		mainObj.traverse( function ( child ) {

			$(object_initial_data).each(function() {
				var currentElement = $(this);
				var hide_objs = currentElement.attr('data-hide');
				hideobjsArray = hide_objs.split(", ");

				jQuery.each( hideobjsArray, function( i, val ) {
					if(child.name == val){
						child.visible = false;
					}	
				});

				var show_obj = currentElement.attr('data-show');
				if(child.name == show_obj) { child.visible = true; }
			});

		});

	}
	
})();

/* Add Fonts to SVG file when page created. So it stays in svg once and fonts work in svg */
var addFontsToSvg = (function(){

	events.on('ajaxDataReceived', checkSvgData);

	function checkSvgData(data){

		var wc_3d_product_data = jQuery.parseJSON(data);
		var svg_active = wc_3d_product_data['svg_active'];

		if(svg_active == 'on') {

			var svg = document.getElementById("svg_data").querySelector("svg");
			var html = '<defs><style id="fontimport" type="text/css"></style></defs>';
			$( svg ).prepend( html );

			var allFontsFiles = $('.fontFamily');

			var svgfont = document.getElementById("fontimport");

			jQuery.each( allFontsFiles, function( i, val ) {
				var fontCss = $(val).attr('data-font-url');
				$.get( fontCss, function( data ) {
					$(svgfont).append(data);
				});
			});

		}
	}

})();

/* Materail Color / SVG color / Texrure Image Changing Function */
var materialConfig = (function(){

	var svg_active, mainModel;

	var use_price_option = wc3dProductJs.use_price;

	events.on('modelLoaded', setModel);
	events.on('ajaxDataReceived', SvgActive);
	
	$( ".mat_option" ).on('click', materialChanged );

	function SvgActive(data){
		var wc_3d_product_data = jQuery.parseJSON(data);
		svg_active = wc_3d_product_data['svg_active'];
	}

	function setModel(mainObj){
		mainModel = mainObj;
	}

	function materialChanged(){

		var element = $(this);
		var material_options = element.closest('.material_options');
		material_options.find('.mat_option').removeClass('active');
		element.addClass('active');

		var mat_id = element.attr('data-mat');
		var matArray = mat_id.split(", ");

		var mat_color = element.attr('data-mat-color');
		var mat_map = element.attr('data-mat-map');
		var mat_title = element.attr('data-mat-title');

		if(use_price_option == 'yes') {
			var mat_price = element.attr('data-mat-price');
			if(!mat_price) {
				mat_price = 0;
			}
			material_options.siblings('.wc_mat_price').val(mat_price);
			material_heading = material_options.siblings('h3');
			material_heading.children('.option_price').html('<b>+ ' + mat_price + '</b>');
			rcCalculateTotal.wc3dcalulatePrice();
		}

		material_options.siblings('.mat_value').val(mat_title);

		var $special_material = document.getElementById("svg_data");

		if($special_material){
			var special_material_name = $special_material.getAttribute('data-svgM');
			if(special_material_name != ''){
				if(jQuery.inArray( special_material_name, matArray ) != '-1'){
					svg_active = 'on';
				} else {
					jQuery.each( matArray, function( i, val ) {
						var svg_path = $special_material.querySelector('#'.val);
						if(svg_path != ''){
							svg_active = 'on';						
						} else {
							svg_active = 'off';
						}
					});
				}
			}
		}

		if(svg_active == 'on') {

			jQuery.each( matArray, function( i, val ) {

				if(val.indexOf('*') != -1){
					var wild_ids = val.replace('*', '');
					$( "[id^='"+wild_ids+"']" ).attr( 'class', '' );
					$( "[id^='"+wild_ids+"']" ).each(function () {
						var parentTag = $( this ).parent().get( 0 ).tagName;
						if(parentTag == 'linearGradient') {
							$(this).css('stop-color', mat_color);
						} else {
							$(this).attr( 'fill', mat_color );	
						}
					});
				} else {
					var svg_path = document.getElementById(val);

					svg_path.setAttribute("fill", mat_color);
				}
			});

			addSvgToModel.applySvgGraphics();

		} else {

			mainModel.traverse( function ( child ) {
				if ( child.isMesh ) {
					if(jQuery.isArray( child.material )) {
						for( var mat of child.material) {
							if(	jQuery.inArray( mat.name , matArray ) != '-1' ){
								if(mat_map) {
									texture = new THREE.TextureLoader().load( mat_map );
									texture.wrapS = THREE.RepeatWrapping;
									texture.wrapT = THREE.RepeatWrapping;
									mat.color.set('#ffffff');
									mat.map = texture;
									mat.needsUpdate = true;
								} else {
									mat.color.set(mat_color);	
								}
							}
						}	
					} else {
						var mat = child.material;
						if(jQuery.inArray( mat.name , matArray ) != '-1' ){
							if(mat_map) {
								texture = new THREE.TextureLoader().load( mat_map );
								texture.wrapS = THREE.RepeatWrapping;
								texture.wrapT = THREE.RepeatWrapping;
								mat.color.set('#ffffff');
								mat.map = texture;
								mat.needsUpdate = true;
							} else {
								mat.color.set(mat_color);	
							}
						}
					}
				}
			} );
		}
	}

})();

/* Fabric Gallery Changing Function */
var fabricgalleryConfig = (function(){

	var svg_active, mainModel;

	var use_price_option = wc3dProductJs.use_price;

	events.on('modelLoaded', setModel);
	events.on('ajaxDataReceived', SvgActive);
	
	$( document ).on('click', ".mat_option_fabric_gallery", materialChanged );

	function SvgActive(data){
		var wc_3d_product_data = jQuery.parseJSON(data);
		svg_active = wc_3d_product_data['svg_active'];
	}

	function setModel(mainObj){
		mainModel = mainObj;
	}

	function materialChanged(){
		var element = $(this);

		var mat_id = element.attr('data-mat');
		var matArray = mat_id.split(", ");

		var mat_color = element.attr('data-mat-color');
		var mat_map = element.attr('data-mat-map');
		var mat_title = element.attr('data-mat-title');

		mainModel.traverse( function ( child ) {
			if ( child.isMesh ) {
				if(jQuery.isArray( child.material )) {
					for( var mat of child.material) {
						if(	jQuery.inArray( mat.name , matArray ) != '-1' ){
							if(mat_map) {
								texture = new THREE.TextureLoader().load( mat_map );
								texture.wrapS = THREE.RepeatWrapping;
								texture.wrapT = THREE.RepeatWrapping;
								mat.color.set('#ffffff');
								mat.map = texture;
								mat.needsUpdate = true;
							} else {
								mat.color.set(mat_color);	
							}
						}
					}	
				} else {
					var mat = child.material;
					if(jQuery.inArray( mat.name , matArray ) != '-1' ){
						if(mat_map) {
							texture = new THREE.TextureLoader().load( mat_map );
							texture.wrapS = THREE.RepeatWrapping;
							texture.wrapT = THREE.RepeatWrapping;
							mat.color.set('#ffffff');
							mat.map = texture;
							mat.needsUpdate = true;
						} else {
							mat.color.set(mat_color);	
						}
					}
				}
			}
		} );
	}

})();

/* Image Change function in SVG */
var imageConfig = (function(){

	$( ".img_option" ).bind('click', adminImageChanged );
	$( document ).on('click', '.user_image', userImageChanged );

	function adminImageChanged(){

		var element = $(this);
		element.addClass('active').siblings().removeClass('active');
		var img_id = element.attr('data-mat');
				
		var mat_map = element.attr('data-mat-map');
		var mat_title = element.attr('data-mat-title');

		var material_options = element.parent('.material_options');
		material_options.siblings('.mat_value').val(mat_title);

		var img_block = document.getElementById(img_id);

		toDataUrl(mat_map, function(myBase64) {
			$(img_block).attr('xlink:href', myBase64);
			addSvgToModel.applySvgGraphics();
		});

	}

	function userImageChanged(){

		var element = $(this);
		element.addClass('active').parent().siblings().removeClass('active');

		var img_id = element.parent().attr('data-mat');

		var mat_map = element.children('.dz-image').children('img').attr('src');
		var mat_title = element.attr('data-attachment');

		var material_options = element.parent('.material_options');
		material_options.siblings('.mat_value').val(mat_title);

		var img_block = document.getElementById(img_id);
		$(img_block).attr('xlink:href', mat_map);

		addSvgToModel.applySvgGraphics();

	}

})();

/* Object Show / Hide Function in 3d Model */
var objectConfig = (function(){

	$( ".obj_option" ).bind('click', objectChanged );

	events.on('modelLoaded', setModel);

	var mainModel;

	function setModel(mainObj){
		mainModel = mainObj;
	}

	function objectChanged(){

		var element = $(this);

		element.addClass('active').siblings().removeClass('active');

		var mat_title = element.attr('data-mat-title');

		var hide_objs = element.siblings('.object_initial_data').attr('data-hide');
		var hideobjsArray = hide_objs.split(", ");

		// var show_obj = element.attr('data-obj-name');
		var show_obj = element.attr('data-obj-name').split(", ");

		mainModel.traverse( function ( child ) {

			jQuery.each( hideobjsArray, function( i, val ) {
				if(child.name == val){
					child.visible = false;
				}
			});
			console.log(show_obj);
			if(show_obj.includes(child.name)){ 
				child.visible = true;
			}
		});

		var material_options = element.parent('.material_options');
		material_options.siblings('.mat_value').val(mat_title);

	}

})();

/* Gradient Changing function for SVG */
var gradientConfig = (function(){

	$( ".gradient_option" ).bind('click', gradientChanged );

	function gradientChanged(){

		var element = $(this);
		element.addClass('active').siblings().removeClass('active');
		var mat_id = element.attr('data-mat');
		var matArray = mat_id.split(", ");

		var mat_color = element.attr('data-mat-color');
		var mat_title = element.attr('data-mat-title');

		var material_options = element.parents('.material_options');
		var gcurvalue = material_options.siblings('.mat_value').val();

		gcurvalue = gcurvalue.split("-");

		jQuery.each( matArray, function( i, val ) {
			var svg_path = document.getElementById(val);
			if($(element).hasClass("gcolor1")) {
				$(svg_path).children(':first-child').css('stop-color', mat_color);
				if(gcurvalue[1]==''){gcurvalue[1] = "Default";}
				material_options.siblings('.mat_value').val(mat_title + '-' + gcurvalue[1]);
			} else if($(element).hasClass("gcolor2")) {
				$(svg_path).children(':last-child').css('stop-color', mat_color);
				if(gcurvalue[0]==''){gcurvalue[0] = "Default";}
				material_options.siblings('.mat_value').val(gcurvalue[0] + '-' + mat_title);
			}
		});

		addSvgToModel.applySvgGraphics();

	}

})();

/* Pattern Changing Function for SVG */
var patternConfig = (function(){

	$( ".pattern_option" ).bind('click', patternChanged );

	function patternChanged(){

		var element = $(this);
		element.addClass('active').siblings().removeClass('active');
		var mat_id = element.attr('data-mat');
		var matArray = mat_id.split(", ");
		var pattern_id = element.attr('data-pattern-id');
				
		var mat_map = element.attr('data-pattern-code');
		var mat_title = element.attr('data-mat-title');

		var material_options = element.parent('.material_options');
		material_options.siblings('.mat_value').val(mat_title);

		jQuery.each( matArray, function( i, val ) {
			var svg_path = document.getElementById(val);
			svg_path.setAttribute("fill", 'url(#'+pattern_id+')');
		});

		addSvgToModel.applySvgGraphics();

	}

})();

/* Text, Font, Text Color, Text Size Changing Functions for SVG */
var textConfig = (function(){

	$( ".field_text" ).bind('change paste keyup', textInput );
	$( ".font_size" ).bind('change', textSize );
	$( ".font_color" ).bind('click', textColor );
	$( ".fontFamily" ).bind('click', textFamily );

	function textInput(){

		var element = $(this);
		var text_id = element.attr('data-text-id');
		var update_text = element.val();

		var textArray = text_id.split(", ");
		jQuery.each( textArray, function( i, val ) {
			var text_block = document.getElementById(val);
			$(text_block).find( "tspan" ).html(update_text);
		});

		var material_options = element.parent('.material_options');
		material_options.siblings('.mat_value').val(update_text);

		addSvgToModel.applySvgGraphics();
	};

	function textSize(){

		var element = $(this);
		var text_id = element.attr('data-text-id');
		var update_font_size = element.val();

		var textArray = text_id.split(", ");

		jQuery.each( textArray, function( i, val ) {
			var text_block = document.getElementById(val);
			$(text_block).find( "tspan" ).removeAttr('font-size');
			$(text_block).find( "tspan" ).css('font-size', update_font_size);
		});

		element.siblings('.hidden_font_size').val(update_font_size);

		addSvgToModel.applySvgGraphics();
	}

	function textColor(){

		var element = $(this);
		var text_id = element.attr('data-text-id');
		var update_font_color = element.attr('data-text-color');
		var color_title = element.attr('data-mat-title')

		var textArray = text_id.split(", ");

		element.addClass('active').siblings().removeClass('active');

		jQuery.each( textArray, function( i, val ) {
			var text_block = document.getElementById(val);
			$(text_block).find( "tspan" ).attr('fill', update_font_color);
			$(text_block).find( "tspan" ).css('color', update_font_color);
		});

		element.siblings('.hidden_font_color').val(color_title);

		addSvgToModel.applySvgGraphics();
	}

	function textFamily(){

		var element = $(this);
		var text_id = element.attr('data-text-id');
		var update_font_family = element.attr('data-font');
		var textArray = text_id.split(", ");

		jQuery.each( textArray, function( i, val ) {
			var text_block = document.getElementById(val);
			$(text_block).find( "tspan" ).removeAttr('font-family');
			$(text_block).find( "tspan" ).css('font-family', update_font_family);
		});
		element.siblings('.hidden_font_family').val(update_font_family);

		addSvgToModel.applySvgGraphics();
	}

})();

/* Apply SVG file to 3D Model */
var addSvgToModel = (function(){

	events.on('ajaxDataReceived', SvgActive);
	events.on('modelLoaded', setModel);
	events.on('modelLoaded', applySvgGraphics);

	var svg_active, mainModel;

	var img  = new Image();

	function SvgActive(data){
		var wc_3d_product_data = jQuery.parseJSON(data);
		svg_active = wc_3d_product_data['svg_active'];
	}

	function setModel(mainObj){
		mainModel = mainObj;
	}

	function applySvgGraphics(mainObj){

		if(svg_active == 'on') {

			var svg = document.getElementById("svg_data").querySelector("svg");
			var material = document.getElementById("svg_data").getAttribute('data-svgM');
			var svgData = new XMLSerializer().serializeToString(svg);
			
			var src = "data:image/svg+xml;base64," + btoa(svgData);

			img.onload = function() {

				mainModel.traverse( function ( child ) {
					if ( child.isMesh ) {

						var texture = new THREE.Texture(img);
						var mat = child.material;
						if(material != ''){
							if(material == mat.name){
								mat.color.set('#ffffff');
								mat.map = texture;
								mat.map.needsUpdate = true;
								mat.needsUpdate = true;
								mat.side = THREE.DoubleSide;
							}
						} else {
							mat.color.set('#ffffff');
							mat.map = texture;
							mat.map.needsUpdate = true;
							mat.needsUpdate = true;
							mat.side = THREE.DoubleSide;
						}
					}
				});
			};

			img.setAttribute("src", src );

		}
	}

	return { applySvgGraphics };

})();

/* Activate Default Color in right Color selection based on SVG */
var rcActiveDefaultSelection = (function(){

	events.on('ajaxDataReceived', ActivateDefault);

	function ActivateDefault(data){
		var wc_3d_product_data = jQuery.parseJSON(data);
		var svg_active = wc_3d_product_data['svg_active'];

		if(svg_active == 'on') {

			var svg = document.getElementById("svg_data").querySelector("svg");

			$('.mat_color').each(function() {

				var element = $(this);

				var selector_id = element.children('.mat_option').first().attr('data-mat');

				if(!selector_id) { return; }

				var matArray = selector_id.split(", ");

				jQuery.each( matArray, function( i, val ) {
					
					if(val.indexOf('*') != -1){
						var wild_ids = val.replace('*', '');
						var fill_color = $( "[id^='"+wild_ids+"']" ).attr( 'fill' );

						element.children('.mat_option').each(function () {
							var color_value = $(this).attr('data-mat-color');
							var mat_title = $(this).attr('data-mat-title');
							var mat_price = $(this).attr('data-mat-price');
							if(!mat_price) {
								mat_price = 0;
							}

							if(color_value.toUpperCase() == fill_color){
								$(this).addClass('active');
								element.siblings('.mat_value').val(mat_title);
								element.siblings('.wc_mat_price').val(mat_price);
							} else {
								$(this).removeClass('active');
							}
						});

					} else {
						var fill_color = $( '#' + val ).attr( 'fill' );
						
						element.children('.mat_option').each(function () {
							var color_value = $(this).attr('data-mat-color');
							var mat_title = $(this).attr('data-mat-title');
							var mat_price = $(this).attr('data-mat-price');
							if(!mat_price) {
								mat_price = 0;
							}

							if(color_value.toUpperCase() == fill_color){
								$(this).addClass('active');
								element.siblings('.mat_value').val(mat_title);
								element.siblings('.wc_mat_price').val(mat_price);
							} else {
								$(this).removeClass('active');
							}
						});
					}
				});

			});

			$('.text_color').each(function() {

				var element = $(this);

				var text_option = element.children('.text_options_block');

				var selector_id = text_option.children('.font_color').first().attr('data-text-id');

				if(!selector_id) { return; }

				var matArray = selector_id.split(", ");

				jQuery.each( matArray, function( i, val ) {

					var fill_color = $( '#' + val + ' tspan' ).attr( 'fill' );

					if(!fill_color) { fill_color = "#000000"; }

					text_option.children('.font_color').each(function () {
						var color_value = $(this).attr('data-text-color');
						var mat_title = $(this).attr('data-mat-title');

						if(color_value.toUpperCase() == fill_color){
							$(this).addClass('active');
							element.siblings('.mat_value').val(mat_title);
						} else {
							$(this).removeClass('active');
						}
					});
				});
			});
		}
	}

})();

/* Calculate Price Total if price enable / disable */
var rcCalculateTotal = (function(){

	var use_price_option = wc3dProductJs.use_price;

	if(use_price_option == 'yes'){
		var iniPrice = $('#finalPrice').val();
		wc3dcalulatePrice();
	}

	function wc3dcalulatePrice() {
		var matOptionsPrice = 0;
		$('.wc_mat_price').each(function() {
			var indPrice = $( this ).val();
			matOptionsPrice = parseInt(matOptionsPrice) + parseInt(indPrice);
		});
		var finalPrice = parseInt(iniPrice) + parseInt(matOptionsPrice);
		$('#finalPrice').val( finalPrice );
		var cur_symbol = wc3dProductJs.cur_symbol;
		$('.sub_total').html( cur_symbol + ' ' + parseFloat(finalPrice).toFixed(2) );
	}

	return { wc3dcalulatePrice };

})();

/* Add to Cart Function Runs when user click on add to cart button - Save Final Model Image, SVG and add to cart data as well */
var rcAddtoCart = (function(){

	var isDone = false;

	var $thisbutton = $('.single_add_to_cart_button');

	var svg_active;

	events.on('ajaxDataReceived', SvgActive);
	
	$( $thisbutton ).on('click', addToCartProcess );

	function SvgActive(data){

		var wc_3d_product_data = jQuery.parseJSON(data);
		svg_active = wc_3d_product_data['svg_active'];

	}

	function addToCartProcess(e){

		if ( isDone == false ) {

			$thisbutton.addClass( 'loading' );

			e.preventDefault();
			var canvas = document.getElementById('wc_3d');
			var dataURL = canvas.toDataURL();

			var svgData;

			if(svg_active == 'on') {
				var svg = document.getElementById("svg_data").querySelector("svg");
				svgData = new XMLSerializer().serializeToString(svg);
			}

			var action = 'wc_3d_product_image';

			jQuery.ajax({
				type: 'POST',
				url: wc3dProductJs.ajaxUrl,
				data: {
					'action': action,
					'imgBase64': dataURL,
				},
				success: function (data) {
					var newdata = JSON.parse(data);
					$('#wc3dimgurl').val(decodeURIComponent(newdata.url));

					var action = 'wc_3d_product_svg';

					jQuery.ajax({
						type: 'POST',
						url: wc3dProductJs.ajaxUrl,
						data: {
							'action': action,
							'svgData': svgData,
						},
						success: function(data) {
							var newdata = JSON.parse(data);
							var svgurl = decodeURIComponent(newdata.url);
							$('#wc3dsvgurl').val(svgurl);
						}
					}).then(function() {
						isDone = true;
						$(e.currentTarget).trigger('click');
					});
				}
			})
		}

	}

})();

/* Front End Image Upload Function */
var dropzoneUpload = (function(){

	Dropzone.autoDiscover = false;

	jQuery(".dropzone").dropzone({
		url: wc3dProductJs.upload,
		acceptedFiles: '.jpg, .png',
		success: function (file, response) {
			file.previewElement.classList.add("dz-success");
			file.previewElement.classList.add("user_image");
			file['attachment_id'] = response;
			jQuery(file.previewElement).attr('data-attachment', response);
		},
		error: function (file, response) {
			file.previewElement.classList.add("dz-error");
		},
		// update the following section is for removing image from library
		addRemoveLinks: true,
		removedfile: function(file) {
			var attachment_id = file.attachment_id;        
			jQuery.ajax({
				type: 'POST',
				url: wc3dProductJs.delete,
				data: {
					media_id : attachment_id
				}
			});
			var _ref;
			return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
		},
	});

})();

/* Save File Function */
var toDataUrl = function (url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.onload = function() {
		var reader = new FileReader();
		reader.onloadend = function() {
			callback(reader.result);
		}
		reader.readAsDataURL(xhr.response);
	};
	xhr.open('GET', url);
	xhr.responseType = 'blob';
	xhr.send();
}

var saveFile = function (strData, filename) {
	var link = document.createElement('a');
	if (typeof link.download === 'string') {
		document.body.appendChild(link); //Firefox requires the link to be in the body
		link.download = filename;
		link.href = strData;
		link.click();
		document.body.removeChild(link); //remove the link when done
	} else {
		location.replace(uri);
	}
}
/* Save File Function Closed */

jQuery(document).ready(function($){

	jQuery( ".wc_3d_materials" ).accordion({
		collapsible: true,
		header: ".material_group h3",
		autoHeight: false,
		heightStyle: "content",
	});

});