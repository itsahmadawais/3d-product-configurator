jQuery(document).ready(function ($) {
    var selectedFabric = 0;
    var allFabrics = [];
    var filteredFabrics = [];
    var imgDisplayContainer = "";
    var objs = "";
    //Function to Populate the Gallery Items
    function populateGallery(gallery){
        $("#fabrics-container").html('');
        gallery.forEach(function(item, index){
            /*
                <p class="subheading">Comfab</p>
                <div class="add-to-fav">
                    <button type="button" class="btn fav-btn"><i class="fa-solid fa-heart"></i></button>
                </div>
            */
            var html = `
                <div id="${item.ID}" class="fabric" data-index="${index}">
                    <img src="${item.image}"
                        alt="" class="fabric-img">
                    <div class="overlay">
                        <h3 class="heading">${item.title}</h3>
                    </div>
            </div>
            `;
            $("#fabrics-container").append(html);
        });
    }
    //Fabric Popup Show
    $("*[qr-fabric-picker='true']").click(function(){
        var id = $(this).attr("qr-fabric-picker-id");
        fabric_a = $(this).attr("qr-fabric-container");
        imgDisplayContainer = $(this).data("container");
        $("#"+id).show();
        objs = $(this).data('mat');
        if(allFabrics.length<=0){
            $.ajax({
                type: "POST",
                url: frontendajax.ajaxUrl,
                data: {
                    action: "get_fabric_library_data"
                },
                success: function(response){
                    console.log('🟢 Well done, data fecteched!', response);
                    $("#popup-data-loader").hide();
                    allFabrics = response.data;
                    populateGallery(allFabrics);
                },
                error: function(error){
                    console.log('🔴 Oops sorry caught some error!', error);
                }
            });
        } else {
            populateGallery(allFabrics);
        }
    });
    //Fabric Popup Hide
    $(".qr-designer-popup .btn-times").click(function(){
        console.log('🟢 Okay... Let me reset the popup screens now!');
        $(this).closest(".qr-designer-popup").hide();
        var container = $("#qr-fabric-picker");
        container.find("#fabric-container").hide();
        var fabricGallery = container.find("#fabric-gallery");
        fabricGallery.show();
    });
    $(document).on('click', '.qr-designer-popup .modal-body .fabrics .fabric', function(){
        console.log("🟢 Wait...I am opening the single fabric preview.");
        selectedFabric = $(this).data('index');
        console.log("🟢 Yep! Selected ID is:",selectedFabric);
        var img = '';
        var brand = '';
        if(filteredFabrics[selectedFabric].brands !==undefined ){
            allFabrics[selectedFabric].brands.forEach(function(item){
                brand += `
                    <li>
                        <div class='flex-container'>
                        <div class='label-box'>
                                <p class='label'>
                                    Brand
                                </p>
                        </div>
                        <div class='value-box'>
                                <p class='value'>
                                    ${item.title}
                                </p> 
                        </div>
                        </div>
                    </li>
                    `;
                img = `<img class='brand-img' src='${item.image}'>`;
            });
        }
        var color = '';
        if(filteredFabrics[selectedFabric].colors !== undefined){
            var colors = '';
            allFabrics[selectedFabric].colors.forEach(function(item){
                colors += `
                    <div class='color-container'>
                        <div class='flex-container'>
                                <div class='color-box' style='background-color:${item.color};'></div>
                                <p class='value'>
                                    ${item.title}
                                </p> 
                        </div>
                    </div>
                `
            });
            color = `
                <li>
                    <div class='flex-container'>
                        <div class='label-box'>
                                <p class='label'>
                                    Color
                                </p>
                        </div>
                        <div class='value-box'>
                            ${colors}
                        </div>
                    </div>
                </li>
            `;
        }
        var pattern = '';
        if(filteredFabrics[selectedFabric].patterns !== undefined){
            var patterns = [];
            allFabrics[selectedFabric].patterns.forEach(function(item){
                patterns.push(item.title);
            });
            pattern += `
                    <li>
                        <div class='flex-container'>
                        <div class='label-box'>
                                <p class='label'>
                                    Pattern
                                </p>
                        </div>
                        <div class='value-box'>
                                <p class='value'>
                                    ${patterns.join(',')}
                                </p> 
                        </div>
                        </div>
                    </li>
             `;
        }
        var html = `
        <div class='flex-container'>
            <div class='image-box'>
                <img src='${filteredFabrics[selectedFabric].image}'>
            </div>
            <div class='info'>
                <h2 class='title'>${filteredFabrics[selectedFabric].title}</h2>
                <div class='content'>
                    ${img}
                    ${filteredFabrics[selectedFabric].content}
                </div>
                <ul>
                    ${brand}
                    ${color}
                    ${pattern}
                </ul>
                <button id="btn-use-fabric"
                    data-mat-title="" 
                    data-mat-price="" 
                    data-mat-color="#dd3333"
                    data-mat="${objs}" 
                    data-mat-map="${filteredFabrics[selectedFabric].image}" 
                    type="button" class="mat_option_fabric_gallery btn btn-primary">Use this fabric</button>
            </div>
        </div>
        `;
        $("#fabric-info-container").html(html);
        
        var container = $("#qr-fabric-picker");
        var fabricGallery = container.find("#fabric-gallery");
        fabricGallery.hide();
        container.find("#fabric-container").show();
    });

    $("#btn-back-to-fabric-gallery").click(function(){
        var container = $("#qr-fabric-picker");
        container.find("#fabric-container").hide();
        var fabricGallery = container.find("#fabric-gallery");
        fabricGallery.show();
    });
    
    $(document).on('click', '#btn-use-fabric', function(){
        $("#"+imgDisplayContainer).html(`
            <img src='${filteredFabrics[selectedFabric].image}' style='width:150px;height:200px;object-fit:cover;'>
        `);
        $(this).closest(".qr-designer-popup").hide();
        var container = $("#qr-fabric-picker");
        container.find("#fabric-container").hide();
        var fabricGallery = container.find("#fabric-gallery");
        fabricGallery.show();
    });

    //Add to Favourite
    $(".fav-btn").click(function(e){
        e.preventDefault();
        e.stopPropagation();
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        }
        else{
            $(this).addClass('active');
        }
    });
    //Filter Button
    $("#search-fabric-btn").click(function(){
        var pattern = $("#input-patterns").val();
        var brand = $("#input-brands").val();
        var sortBy = $("#input-sort-by").val();
        var keyword = $("#input-keyword").val();
        var color = $("#color-code").val();
        var filteredData = allFabrics.filter((item) => {
            var flag = true;
            if(keyword!=''){
                if(item.title.toLowerCase().includes(keyword.toLowerCase())){
                    flag = true;
                } else {
                    flag = false;
                }
            }
            if(item.colors != undefined && color != '' && color != 0){
                var dataFound = item.colors.find((itemColor) => {
                    if(itemColor.ID == color ){
                        return true;
                    }
                });
                if(!dataFound){
                    flag = false;
                }
            }

            if(item.patterns !== undefined && pattern != ''){
                var dataFound = item.patterns.find((itemPattern) => {
                    if(itemPattern.ID == pattern ){
                        return true;
                    }
                });
                if(!dataFound){
                    flag = false;
                }
            }
            if(item.brands !== undefined && brand != ''){
                var dataFound = item.brands.find((itemBrand) => {
                    if(itemBrand.ID == brand ){
                        return true;
                    }
                });
                if(!dataFound){
                    flag = false;
                }
            }
            return flag;
        });
        //If Price is High to Low
        if(sortBy == "p-h-l"){
            filteredData = filteredData.sort((a, b) => {
                return parseFloat(b.price) - parseFloat(a.price);
            });
        } 
        //If Price is Low to High
        else if(sortBy == "p-l-h"){
            filteredData = filteredData.sort((a, b) => {
                return parseFloat(a.price) - parseFloat(b.price);
            });
        }
        filteredFabrics = filteredData;
        populateGallery(filteredData);
    });

    $(document).on('click', ".dropdown dt>div", function() {
        $(".dropdown dd ul").toggle();
    });

    $(".dropdown dd ul li").click(function() {
        var text = $(this).html();
        var id = $(this).data('colorid');
        $("#color-code").val(id);
        $(".dropdown dt").html(text);
        $(".dropdown dd ul").hide();
        $("#result").html("Selected value is: " + getSelectedValue("sample"));
    });
    

    function getSelectedValue(id) {
        return $("#" + id).find("dt a span.value").html();
    }

    $(document).bind('click', function(e) {
        var $clicked = $(e.target);
        if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
    });
});