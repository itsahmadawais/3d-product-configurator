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

<?php
/**
 * Fabric Library Template
 */
?>
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
                    <div class="empty-div"></div>
                    <!-- Form Inputs -->
                    <div class="form-inputs">
                        <form action="" class="flex-container">

                            <!-- Input Keyword -->
                            <div class="input-control mr-1">
                                <input name="input-keyword" id="input-keyword" class="form-control" placeholder="Type Keyword">
                            </div>
                            <!-- End Input Keyword -->

                            <!-- Input Colors -->
                            <div class="input-control mr-1">
                                <input type="hidden" id="color-code" name="color_code" value="0">
                                <dl class="dropdown">
                                    <dt>
                                        <div class="flex-container align-items-center active-color">
                                            <div class="color" style="background-color:<?php echo $colorCode; ?>"></div>
                                            <span class="color-title">All</span>
                                        </div>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li data-colorid="0">
                                                <div class="flex-container align-items-center">
                                                    <div class="color" style="background-color:<?php echo $colorCode; ?>;"></div>
                                                    <span class="color-title">All</span>
                                                </div>
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
                                                <li  data-colorid="<?php echo $color->term_id; ?>">
                                                    <div class="flex-container">
                                                        <div class="color" style="background-color:<?php echo $colorCode; ?>;"></div>
                                                        <span class="color-title"><?php echo $color->name; ?></span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </dd>
                                </dl>
                            </div>
                            <!-- End Input Colors -->

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
                            <div class="input-control mr-1">
                                <select name="input-sort-by" id="input-sort-by" class="form-control">
                                    <option value="">Sort by</option>
                                    <option value="p-h-l">Price (High to low)</option>
                                    <option value="p-l-h">Price (Low to high)</option>
                                </select>
                            </div>
                            <!-- End Input Sort By -->

                            <!-- Input Sort By -->
                            <div class="input-control">
                                <button type="button" id="search-fabric-btn" class="search-btn">Filter</button>
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