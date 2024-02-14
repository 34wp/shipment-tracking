<?php
/**
 * Plugin Name: Shipment Tracking
 * Description: Shipment Tracking allows you to easily transmit the tracking codes of your shipments to your customers.
 * Version: 1.0.0
 * Author: 34WP
 * Author URI: https://34wp.com
 * Text Domain: shipment-tracking
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SHIPMENT_TRACKING_VERSION', '1.0.0' );

//Add Menu to WPadmin
include 'netgsm-helper.php';
include 'shipment-tracking-helper.php';
include 'shipment-tracking-order-list.php';
include 'shipment-tracking-email-settings.php';
include 'shipment-tracking-sms-settings.php';
// include 'shipment-tracking-content-edit-helper.php';
include 'shipment-tracking-wc-api-helper.php';

add_action( 'admin_menu', 'shipment_tracking_register_admin_menu' );
function shipment_tracking_register_admin_menu() {
    $menu_slug = 'shipment-tracking';
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page( 'Shipment Tracking', 'Shipment Tracking', 'read', $menu_slug, false, 'dashicons-car', 20 );
    add_submenu_page( $menu_slug, 'Shipment Tracking Settings', 'General Settings', 'read', $menu_slug, 'shipment_tracking_setting_page' );
    add_submenu_page( $menu_slug, 'Shipment Tracking Settings', 'Email Settings', 'read', 'shipment-tracking-email-settings', 'shipment_tracking_email_setting_page' );
    add_submenu_page( $menu_slug, 'Shipment Tracking Settings', 'SMS Settings', 'read', 'shipment-tracking-sms-settings', 'shipment_tracking_sms_setting_page' );
    add_action( 'admin_init', 'shipment_tracking_register_settings' );
}

function shipment_tracking_register_settings() {
    $args = array(
        'default' => 'yes',
    );

    $argsSelect = array(
        'default' => 'no',
    );

    $argsFild = array(
        'default' => '',
    );

    $argsSmsTemplate = array(
        'default' => 'Dear {customer_name}, your order with {order_id} has been delivered to {company_name}. Your cargo tracking number: {tracking_number}. Your cargo tracking link: {tracking_url}. Good day.',
    );


    register_setting( 'shipment-tracking-settings-group', 'kargo_hazirlaniyor_text',$argsSelect  );

    register_setting( 'shipment-tracking-settings-group', 'mail_send_general',$argsSelect  );
    register_setting( 'shipment-tracking-settings-group', 'sms_provider',$argsSelect  );

    register_setting( 'shipment-tracking-settings-group', 'sms_send_general',$argsSelect  );

    register_setting( 'shipment-tracking-settings-group', 'NetGsm_UserName',$argsFild  );
    register_setting( 'shipment-tracking-settings-group', 'NetGsm_Password',$argsFild  );
    register_setting( 'shipment-tracking-settings-group', 'NetGsm_Header',$argsSelect  );
    register_setting( 'shipment-tracking-settings-group', 'NetGsm_sms_url_send',$argsSelect  );

    // General SMS Template
    register_setting( 'shipment-tracking-settings-group', 'shipment_tracking_sms_template',$argsSmsTemplate  );

    // Kobikom
    register_setting( 'shipment-tracking-settings-group', 'Kobikom_ApiKey',$argsFild  );
    register_setting( 'shipment-tracking-settings-group', 'Kobikom_Header',$argsFild  );
}


function shipment_tracking_setting_page() {
    $kargo_hazirlaniyor_text = get_option('kargo_hazirlaniyor_text');
    $mail_send_general_option = get_option('mail_send_general');
    $sms_provider = get_option('sms_provider');

    $NetGsm_UserName = get_option('NetGsm_UserName');
    $NetGsm_Password = get_option('NetGsm_Password');
    $NetGsm_Header = get_option('NetGsm_Header');
    $NetGsm_sms_url_send = get_option('NetGsm_sms_url_send');

    ?>
    <div class="wrap">
        <h1>Shipment Tracking</h1>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'shipment-tracking-settings-group' ); ?>
            <?php do_settings_sections( 'shipment-tracking-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" style="width:50%">
                        <?php _e( 'Before entering the cargo information, the message "Cargo is being prepared" should be displayed in the orders.', 'shipment-tracking' ) ?>
                    </th>
                    <td>
                        <input type="radio" id="evet" <?php if( $kargo_hazirlaniyor_text == 'yes' ) echo 'checked'?>
                            name="kargo_hazirlaniyor_text" value="yes">
                        <label for="evet">Yes</label><br>
                    </td>
                    <td>
                        <input type="radio" id="hayir" <?php if( $kargo_hazirlaniyor_text == 'no' ) echo 'checked'?>
                            name="kargo_hazirlaniyor_text" value="no">
                        <label for="hayir">No</label><br>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" style="width:50%">
                        <?php _e( 'Automatically send an e-mail when the cargo tracking number is entered in the order.', 'shipment-tracking' ) ?>
                    </th>
                    <td>
                        <input type="radio" id="evetmail" <?php if( $mail_send_general_option == 'yes' ) echo 'checked'?>
                            name="mail_send_general" value="yes">
                        <label for="evetmail">Yes</label><br>
                    </td>
                    <td>
                        <input type="radio" id="hayirmail" <?php if( $mail_send_general_option == 'no' ) echo 'checked'?>
                            name="mail_send_general" value="no">
                        <label for="hayirmail">No</label><br>
                    </td>
                </tr>
                <tr>
                    <th scope="row" style="width:50%">
                        <hr>
                    </th>
                    <td>
                        <hr>
                    </td>
                    <td>
                        <hr>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" style="width:50%">
                        <?php _e( 'Send in Shipment Tracking URL? <br> If you turn on this feature, your SMS size will probably be larger and extra credit will be charged from your package.', 'shipment-tracking' ) ?>
                    </th>
                    <td>
                        <input type="radio" id="yes_url_send" <?php if( $NetGsm_sms_url_send == 'yes' ) echo 'checked'?>
                            name="NetGsm_sms_url_send" value="yes">
                        <label for="yes_url_send">Yes</label><br>
                    </td>
                    <td>
                        <input type="radio" id="noUrlSend" <?php if( $NetGsm_sms_url_send == 'no' ) echo 'checked'?>
                            name="NetGsm_sms_url_send" value="no">
                        <label for="noUrlSend">No</label><br>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>

            <script>
                jQuery(document).ready(function ($) {
                    $('input[type=radio][name=sms_provider]').change(function () {
                        if (this.value == 'none') {
                            $('.netgsm').hide();

                        } else if (this.value == 'NetGSM') {
                            $('.netgsm').show(2000);
                        }
                    });
                })
            </script>

            <style>
                .label-bold {
                    text-align: center;
                    font-weight: bold;
                }
            </style>
        </form>
    </div>
<?php
}

// Register new status
function shipment_tracking_register_shipment_shipped_order_status() {
    register_post_status('wc-kargo-verildi', array(
        'label' => 'Shipped',
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Shipped(%s)', 'Shipped (%s)'),
    ));
}

add_action('init', 'shipment_tracking_register_shipment_shipped_order_status');
function shipment_tracking_add_shipment_to_order_statuses($order_statuses) {
    $order_statuses['wc-kargo-verildi'] = _x('Shipped', 'WooCommerce Order status', 'woocommerce');
    return $order_statuses;
}

add_filter('wc_order_statuses', 'shipment_tracking_add_shipment_to_order_statuses');
add_action('woocommerce_admin_order_data_after_order_details', 'shipment_tracking_general_shipment_details_for_admin');
function shipment_tracking_general_shipment_details_for_admin($order) {
    $tracking_company = get_post_meta($order->get_id(), 'tracking_company', true);
    $tracking_code = get_post_meta($order->get_id(), 'tracking_code', true);
    ?>
<br class="clear" />
<?php


    woocommerce_wp_select(array(
        'id' => 'tracking_company',
        'label' => 'Shipping Company:',
        'description' => 'Please choose a shipping company!',
        'desc_tip' => true,
        'value' => $tracking_company,
        'placeholder' => 'Not Selected',
        'options' => shipment_tracking_cargo_company_list(),
        'wrapper_class' => 'form-field-wide shipment-set-tip-style',
    ));

    ?>
<script>
    jQuery(document).ready(function ($) {
        $('#tracking_company').select2();
    });
</script>
<?php

    woocommerce_wp_text_input(array(
        'id' => 'tracking_code',
        'label' => 'Tracking number:',
        'description' => 'Please enter the shipment tracking number.',
        'desc_tip' => true,
        'value' => $tracking_code,
        'wrapper_class' => 'form-field-wide shipment-set-tip-style',
    ));

}

add_action('woocommerce_process_shop_order_meta', 'shipment_tracking_tracking_save_general_details');
function shipment_tracking_tracking_save_general_details($ord_id) {
    $tracking_company = get_post_meta($ord_id, 'tracking_company', true);
    $tracking_code = get_post_meta($ord_id, 'tracking_code', true);
    $order_note = wc_get_order($ord_id);
    $mail_send_general_option = get_option('mail_send_general');
    $sms_provider = get_option('sms_provider');

    if (($tracking_company != $_POST['tracking_company']) && ($tracking_code == $_POST['tracking_code'])) {
        update_post_meta($ord_id, 'tracking_company', wc_clean($_POST['tracking_company']));

        $note = __("Shipping company has been updated.");

        $order_note->add_order_note($note);
    } elseif (($tracking_company == $_POST['tracking_company']) && ($tracking_code != $_POST['tracking_code'])) {
        update_post_meta($ord_id, 'tracking_code', wc_sanitize_textarea($_POST['tracking_code']));

        $note = __("Shipment tracking code updated.");

        $order_note->add_order_note($note);
    } elseif (($tracking_company == $_POST['tracking_company']) && ($tracking_code == $_POST['tracking_code'])) {

    } elseif (!empty($_POST['tracking_company']) && !empty($_POST['tracking_code'])) {
        update_post_meta($ord_id, 'tracking_company', wc_clean($_POST['tracking_company']));
        update_post_meta($ord_id, 'tracking_code', wc_sanitize_textarea($_POST['tracking_code']));
        $order = new WC_Order($ord_id);
        $order->update_status('kargo-verildi', 'Sipariş takip kodu eklendi');
        if ($mail_send_general_option == 'yes') do_action('order_ship_mail', $ord_id);
        if ($sms_provider == 'NetGSM') do_action('order_send_sms', $ord_id);
        if ($sms_provider == 'Kobikom') do_action('order_send_sms_kobikom', $ord_id);


    }
}

add_action('admin_head', 'shipment_tracking_shipment_fix_wc_tooltips');
function shipment_tracking_shipment_fix_wc_tooltips() {
    echo '<style>
	    #order_data .order_data_column .form-field.shipment-set-tip-style label{
		    display:inline-block;
	    }
	    .form-field.shipment-set-tip-style .woocommerce-help-tip{
		    margin-bottom:5px;
	    }
	    </style>';
}

function shipment_tracking_shipment_details($order) {
    $tracking_company = get_post_meta($order->get_id(), 'tracking_company', true);
    $tracking_code = get_post_meta($order->get_id(), 'tracking_code', true);
    $kargo_hazirlaniyor_text_option = get_option('kargo_hazirlaniyor_text');
    if ( $order->get_status() != 'cancelled') {
        if ($tracking_company == '') {
            if ($kargo_hazirlaniyor_text_option =='yes') {
                echo "Shipping is being prepared";
            } else {
            ?>

<?php
            }
        }
        else {
            ?>
<div class="shipment-order-page">
    <h2 id="kargoTakipSection">Shipment Tracking</h2>
    <h4>Shipping company : </h4> <?php echo shipment_tracking_get_company_name($tracking_company); ?>
    <h4><?php _e( 'Shipment tracking number:','shipment-tracking');?></h4> <?php echo esc_attr($tracking_code) ?>
    <br>
    <?php echo '<a href="' . shipment_tracking_getCargoTrack($tracking_company, $tracking_code) . '"target="_blank" rel="noopener noreferrer">'; _e( 'Click here for shipment tracking.','shipment-tracking' );  echo '</a>'; ?>
</div>
<?php
        }
    }
}

add_action('woocommerce_after_order_details', 'shipment_tracking_shipment_details');
add_filter('woocommerce_my_account_my_orders_actions', 'shipment_tracking_add_kargo_button_in_order', 10, 2);
function shipment_tracking_add_kargo_button_in_order($actions, $order) {
    $tracking_company = get_post_meta($order->get_id(), 'tracking_company', true);
    $tracking_code = get_post_meta($order->get_id(), 'tracking_code', true);
    $action_slug = 'kargoButonu';

    if (!empty($tracking_code)) {
        $cargoTrackingUrl = shipment_tracking_getCargoTrack($tracking_company, $tracking_code);
        $actions[$action_slug] = array(
            'url' => $cargoTrackingUrl,
            'name' => 'Shipping Tracking',
        );
        return $actions;
    } else {
        return $actions;
    }
}

function shipment_tracking_kargo_bildirim_icerik($order, $mailer, $mail_title = false) {
    $template = 'email-shipment-template.php';
    $mailTemplatePath = untrailingslashit(plugin_dir_path(__FILE__)) . '/mail-template/';

    $tracking_company = get_post_meta($order->get_id(), 'tracking_company', true);
    $tracking_code = get_post_meta($order->get_id(), 'tracking_code', true);

    return wc_get_template_html($template, array(
        'order' => $order,
        'email_heading' => $mail_title,
        'sent_to_admin' => false,
        'plain_text' => false,
        'email' => $mailer,
        'tracking_company' => $tracking_company,
        'tracking_code' => $tracking_code,
    ), '', $mailTemplatePath);
}




function shipment_tracking_kargo_eposta_details($order_id) {
    $order = wc_get_order($order_id);
    $phone = $order->get_billing_phone();
    $alici = $order->get_shipping_first_name() . " " . $order->get_shipping_last_name();
    $mailer = WC()->mailer();

    $mailTo = $order->get_billing_email();
    $subject = "Siparişiniz Kargoya Verildi";
    $details = shipment_tracking_kargo_bildirim_icerik($order, $mailer, $subject);
    $mailHeaders[] = "Content-Type: text/html\r\n";

    $mailer->send($mailTo, $subject, $details, $mailHeaders);

    $note = __("Shipment Tracking information has been sent to the customer's " . $order->get_billing_email() . " email.");
    $order->add_order_note($note);

    // Siparişi güncelle
    $order->save();
}

add_action('order_ship_mail', 'shipment_tracking_kargo_eposta_details');
