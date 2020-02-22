<?php

/**
 * @class       Woo_Instagram_Admin_Display
 * @version        1.0.0
 * @package        woo-instagram
 * @category    Class
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Instagram_Admin_Display
{

    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init()
    {

        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
    }

    public static function add_settings_menu()
    {
        add_menu_page('WooCommerce Product Instagram Photos Settings', 'Woo Instagram Settings', 'manage_options', 'instagrampage', array(__CLASS__, 'woo_instagram_general_setting'));
    }

    /**
     * woo_instagram_general_setting_fields helper will trigger hook and handle all the settings section
     * @since    0.1.0
     * @access   public
     */
    public static function woo_instagram_general_setting_fields()
    {
        $fields[] = array(
            'title' => __('', 'woo-instagram'),
            'type' => 'title',
            'id' => 'general_options_setting'
        );
        $fields[] = array(
            'title' => __('Access Token', 'woo-instagram'),
            'id' => 'woo_instagram_access_token',
            'type' => 'text',
            'label' => __('Enter Acess Token', 'woo-instagram'),
            'default' => '',
            'class' => 'regular-text',
            //'desc' => '<a href="https://www.instagram.com/developer/authentication/" target="_blank">Click here</a>',
        );

        /* $fields[] = array(
          'id' => 'woo_instagram_client_secret',
          'type' => 'hidden',
          'default' => '',
          'class' => 'regular-text',
          );
          $fields[] = array(
          'title' => __('Redirect URI', 'woo-instagram'),
          'id' => 'woo_instagram_redirect_uri',
          'type' => 'text',
          'label' => __('Enter Redirect URI', 'woo-instagram'),
          'default' => '',
          'class'=>'regular-text',
          'desc'=>'Enter redirect URL',
          ':after'=>'<i class="fa-fa-user">hh</i>',
          );
         */
        $fields[] = array(
            'title' => __('Images Limit', 'woo-instagram'),
            'id' => 'woo_instagram_limit_images',
            'type' => 'text',
            'min' => '1',
            'label' => __('Enter limit of images<br>', 'woo-instagram'),
            'default' => '',
            'class' => 'regular-text',
            'desc' => sprintf(__('Only numeric values allowed ( Value must be > 0 )', 'woo-instagram'), '')
        );

        $fields[] = array('type' => 'sectionend', 'id' => 'general_options_setting');
        return $fields;
    }

    /**
     * woo_instagram_general_setting function is responsible for settings.
     */
    public static function woo_instagram_general_setting()
    {
        $genral_setting_fields = self::woo_instagram_general_setting_fields();
        $Html_output = new Woo_Instagram_Html_output();
        $Html_output->save_fields($genral_setting_fields);

        if (isset($_POST['instagram_intigration'])):
            ?>
            <div id="setting-error-settings_updated" class="updated settings-error">
                <p><?php echo '<strong>' . __('Settings were saved successfully.', 'woo-instagram') . '</strong>'; ?></p>
            </div>

        <?php
        endif;

        if (isset($_POST['submit'])) {
            update_option('woo_instagram_client_secret_id', $_POST['woo_insta_id']);
        }

        $woo_instagram_client_secret_id = get_option('woo_instagram_client_secret_id');
        $woo_instagram_client_secret_id = !empty($woo_instagram_client_secret_id) ? $woo_instagram_client_secret_id : "";
        ?>
        <!--start access token generate form -->
        <div class="woo_insta_config">
            <table class="form-table">
                <tbody>
                <h3><?php _e('Woo Instagram General Settings', 'woo-instagram'); ?></h3>
                <form method="POST">
                    <tr valign="top" class="genrateacform">
                        <th scope="row" class="titledesc">
                            <label><?php _e('Client ID:', 'woo-instagram'); ?></label>
                        </th>
                        <td class="forminp forminp-text woo_insta_id">
                            <input type="text" name="woo_insta_id" id="woo_instagram_client_secret_id" required=""
                                   class="regular-text" value="<?php echo $woo_instagram_client_secret_id; ?>"></input>
                        </td>

                        <td><?php submit_button('Save Client ID'); ?></td>

                    </tr>
                </form>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label><?php _e('Redirect URL:', 'woo-instagram'); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <input type="text" id="redirect_uri" required="" class="regular-text"/>
                    </td>
                    <!--                    <td class="description"><?php // _e('Match On Instagram redirect URIs') ?></td>-->

                    </th>
                </tr>
                <!--<button type="button" id="token" class="ac_t_genrate_linkbtn"><?php _e('GET LINK') ?></button><br>-->
                <tr valign="top" class="btn_access_genrate">
                    <th scope="row" class="btn_access_genrate">
                        <a href="#" class="access_token_genrate_btn woo_insta_admin_btn"
                           id="token"><?php _e('Generate Access Token') ?></a>
                    </th>
                </tr>
                <tr>
                    <p> <?php _e('Go to <a href="https://www.instagram.com/developer/" target="_blank">https://www.instagram.com/developer/</a>
Follow this <a href="https://www.youtube.com/watch?v=dPpNr_3a9qE" target="_blank">video tutorial</a> to register client and generate Client ID. Enter Redirect URI as your website URL, and keep it same here and in your Client registered on Instagram.') ?>
                    </p>
                    <p></p>
                    <p>
                        <?php _e('Enter Client ID and Redirect URI in their respective text boxes below and click on <b>Generate Access Token</b> button to generate token.</br>
You will be asked to login to your Instagram account. Login with the same Instagram account which you used to generate Client ID and proceed.<br>
                    Token will be automatically filled in the access token text box. Enter limit of number of photos to be displayed on product page and click <b>Save Settings</b> at the end to save all information.') ?>
                    </p>
                    <p>

                    </p>
                </tr>
                <?php
                ?>
                <script type="text/javascript">
                    jQuery.noConflict();

                    jQuery(document).ready(function ($) {
                        $("#token").on("click", function () {
                            woo_instagram_client_secret_id = $("#woo_instagram_client_secret_id").val();

                            redirect_url = $("#redirect_uri").val();

                            $("a").attr("href", "https://api.instagram.com/oauth/authorize/?client_id=" + woo_instagram_client_secret_id + "&scope=basic+public_content&redirect_uri=" + redirect_url + "?woo_insta_uri=<?php echo admin_url('admin.php?page=instagrampage'); ?>&response_type=token");

                        });
                        //alert(window.location.hash);                                                                    
                        // var token_id=window.location.hash;
                        var accesstoken = window.location.hash.substring(1);
                        //alert(accesstoken);
                        var data = accesstoken;
                        // alert(arr[1]);
                        //prefill access token value
                        var arr = data.split('=');
                        var act_value = arr[1];
                        // alert(act_value);
                        var woo_pluginurl = act_value;
                        //alert(woo_pluginurl); 
                        if (act_value = woo_pluginurl) {
                            $('[id$=woo_instagram_access_token]').val(arr[1]);
                        }
                        woo_redirect_url_txt = woo_instagram_client_secret_id = $("#woo_instagram_client_secret_id").val();
                        $('#client_id').val(woo_redirect_url_txt);
                        woo_redirect_url_txt = '<?php echo site_url('/'); ?>';
                        $('#redirect_uri').val(woo_redirect_url_txt);

                    });


                </script>
                </tbody>

            </table>
        </div>
        <!--end access token generate form-->
        <div class="div_general_settings">
        <div class="div_log_settings">
        <form id="button_manager_integration_form_general" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($genral_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="instagram_intigration" class="button-primary"
                       value="<?php esc_attr_e('Save Settings', 'Option'); ?>"/>
            </p>
        </form>
        </div><?php
    }

}

Woo_Instagram_Admin_Display::init();
    