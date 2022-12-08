<?php
    $brands = get_terms([
        'taxonomy' => 'color_brand',
        'hide_empty' => false,
    ]);

    $colors = get_terms([
        'taxonomy' => 'color',
        'hide_empty' => false,
    ]);

    $patterns = get_terms([
        'taxonomy' => 'pattern',
        'hide_empty' => false,
    ]);
?>
<!-- Fabric Picker -->
<section id="qr-fabric-picker" class="qr-designer-popup">
    <div class="modal">
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="flex-container space-between">
                <div class="heading">
                    <h2>Fabric Gallery</h2>
                </div>
                <div class="times">
                    <button type="button" class="btn btn-times">
                        X
                    </button>
                </div>
            </div>
        </div>
        <!-- End Modal Header -->

        <!-- Modal Body -->
        <div class="modal-body">
            <div id="fabric-gallery" class="fabric-gallery">
                <!-- Tabs -->
                <div class="tabs">
                    <ul class="ul-tabs">
                        <li class="results active">
                            Results
                        </li>
                        <li class="favourite">
                            Favourite
                        </li>
                    </ul>
                </div>
                <!-- End Tabs -->

                <!-- Colors Filter -->
                <div class="colors-filter flex-container space-between align-center">

                    <!-- Colors Strap -->
                    <div class="colors-strap">
                        <ul class="colors">
                            <li>
                                <img src="./assets/images/fabrics/all-colors.png" alt="">
                            </li>
                            <?php 
                                $count=1;
                                foreach($colors as $color): ?>
                                <?php $colorCode = get_term_meta($color->term_id, '_color_code'); 
                                    if($count>=6){
                                        break;
                                    }
                                    if($colorCode && is_array($colorCode)){
                                        $colorCode = "#".$colorCode[0];
                                    }
                                    $count++;
                                ?>
                            <li>
                                <div class="color" style="background-color:<?php echo $colorCode; ?>;"></div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- End Colors Strap -->

                    <!-- Form Inputs -->
                    <div class="form-inputs">
                        <form action="" class="flex-container">

                            <!-- Input Patterns -->
                            <div class="input-control mr-1">
                                <select name="input-patterns" id="input-patterns" class="form-control">
                                    <option value="">All Patterns</option>
                                    <?php foreach($patterns as $pattern): ?>
                                        <option value="<?php echo $pattern->term_id; ?>"><?php echo $pattern->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- End Input Patterns -->

                            <!-- Input All Brands -->
                            <div class="input-control mr-1">
                                <select name="input-brands" id="input-brands" class="form-control">
                                    <option value="">All Brands</option>
                                    <?php foreach($brands as $brand): ?>
                                        <option value="<?php echo $brand->term_id; ?>"><?php echo $brand->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- End Input All Brands -->

                            <!-- Input Sort By -->
                            <div class="input-control">
                                <select name="input-sort-by" id="input-sort-by" class="form-control">
                                    <option value="">Sort by</option>
                                </select>
                            </div>
                            <!-- End Input Sort By -->

                        </form>
                    </div>
                    <!-- End Form Inputs -->

                </div>
                <!-- End Colors Filter -->
                
                <input type="hidden" name="fabric-selected" id="fabric-selected" />
                <!-- Fabrics -->
                <div id="fabrics-container" class="fabrics flex-container">


                </div>
                <!-- End Fabrics -->

                <!-- Loader -->
                <div id="popup-data-loader">
                    <div class="text-center m-1">
                        <div class="dots-bars-7"></div>
                    </div>
                </div>
                <!-- End Loader-->
            </div>
            <div id="fabric-container" class="fabric-gallery-item" style="display: none;">
                <div class="cta-box">
                    <button type="button" id="btn-back-to-fabric-gallery" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i> Go Back</button>
                </div>
                <div class="info-box">
                    <div id="fabric-info-container">

                    </div>
                </div>
            </div>

        </div>
        <!-- End Modal Body -->

    </div>
</section>
<!-- End Fabric Picker -->