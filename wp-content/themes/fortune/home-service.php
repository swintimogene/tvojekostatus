<?php
$fortune_theme_options = fortune_theme_options();
if(!$fortune_theme_options['home_service_enabled']){return;}
?>
<div class="row" id="service_sectoin">
    <?php if($fortune_theme_options['service_heading']!=""){?>
        <div class="title-centered">
        <h2 id="service_head"><?php echo esc_attr($fortune_theme_options['service_heading']);?></h2>
        </div><?php
    } ?>
    <?php
    if($fortune_theme_options['service_type']==2){
        for($i=1;$i<=3;$i++){ ?>
            <div class="col-md-4">
            <div class="icon-box centered circled boxed icon-box-animated animation fadeInUp animation-visible" data-animation="fadeInUp">
                <div class="icon">
                    <i id="<?php echo 'service_icon_'.$i; ?>" class="<?php echo esc_attr($fortune_theme_options['service_icon_'.$i]);?>"></i>
                </div>
                <div class="icon-box-body">
                    <h3 id="<?php echo 'service_title_'.$i; ?>"><?php echo esc_attr($fortune_theme_options['service_title_'.$i]);?></h3>
                    <div id="<?php echo 'service_text_'.$i; ?>"><p><?php echo esc_attr($fortune_theme_options['service_text_'.$i]);?></p></div>
                    <a id="<?php echo 'service_link_'.$i; ?>" href="<?php  echo esc_url($fortune_theme_options['service_link_'.$i]);?>" class="btn btn-default"><?php _e('Read More','fortune'); ?></a>
                </div>
            </div>
            </div><?php
        } }else{
        $j=0;
        for($i=1;$i<=3;$i++){ ?>
            <div class="col-md-4">
            <div class="icon-box centered squared icon-box-animated" data-animation="flipInY" data-animation-delay="<?php echo (int)$j*200; ?>">
                <div class="icon"> <i id="<?php echo 'service_icon_'.$i; ?>" class="<?php echo esc_attr($fortune_theme_options['service_icon_'.$i]);?>"></i> </div>
                <div class="icon-box-body"><?php
                    if($fortune_theme_options['service_link_'.$i]!="" && $fortune_theme_options['service_link_'.$i]!='#'){ ?>
                    <a id="<?php echo 'service_link_'.$i; ?>" class="service_link" href="<?php  echo esc_url($fortune_theme_options['service_link_'.$i]);?>"><h3 id="<?php echo 'service_title_'.$i; ?>"><?php echo esc_attr($fortune_theme_options['service_title_'.$i]);?></h3> </a><?php
                    }else{ ?>
                        <h3 id="<?php echo 'service_title_'.$i; ?>"><?php echo esc_attr($fortune_theme_options['service_title_'.$i]);?></h3>
                    <?php } ?>
                    <div id="<?php echo 'service_text_'.$i; ?>"><?php echo esc_attr($fortune_theme_options['service_text_'.$i]);?></div>
                </div>
            </div>
            </div><?php
            $j++;
        }} ?>
</div>
<hr class="lg">
