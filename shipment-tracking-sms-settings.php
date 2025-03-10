<?php

include('kobikom-helper.php');

function shipment_tracking_sms_setting_page(){

    $sms_provider = get_option('sms_provider');

    $NetGsm_UserName = get_option('NetGsm_UserName');
    $NetGsm_Password = get_option('NetGsm_Password');
    $NetGsm_Header = get_option('NetGsm_Header');
    $NetGsm_sms_url_send = get_option('NetGsm_sms_url_send');

    //kobikom
    $Kobikom_ApiKey = get_option('Kobikom_ApiKey');
    $Kobikom_option_Header = get_option('Kobikom_Header');

    //get sms template shipment_tracking_sms_template
    $sms_template = get_option('shipment_tracking_sms_template');

    ?>
    <div class="wrap">
        <h1>SMS Settings</h1>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'shipment-tracking-settings-group' ); ?>
            <?php do_settings_sections( 'shipment-tracking-settings-group' ); ?>
            <table class="form-table">
 
 
                <tr valign="top">
                    <th scope="row" style="width:50%">
                        <?php _e( 'SMS Service Provider', 'shipment-tracking' ) ?><br>
                        <span style="font-size:12px;">When you add a cargo tracking number to the order, an SMS is automatically sent to the customer.</span>
                    </th>
                    <td>
                        <input type="radio" id="none" <?php if( $sms_provider == 'none' ) echo 'checked'?>
                            name="sms_provider" value="none">
                        <label for="none">None</label><br>
                    </td>
                    <td>
                        <input type="radio" id="NetGSM" <?php if( $sms_provider == 'NetGSM' ) echo 'checked'?>
                            name="sms_provider" value="NetGSM">
                        <label for="NetGSM">NetGSM</label><br>
                    </td>

                    <td>
                        <input type="radio" id="Kobikom" <?php if( $sms_provider == 'Kobikom' ) echo 'checked'?>
                            name="sms_provider" value="Kobikom">
                        <label for="Kobikom">Kobikom</label><br>
                    </td>
                </tr>

                <tr class="netgsm" <?php if( $sms_provider != 'NetGSM' ) echo 'style="display:none"'?>>
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

                <tr valign="top" class="netgsm" <?php if( $sms_provider != 'NetGSM' ) echo 'style="display:none"'?>>
                    <th scope="row" style="width:25%">
                        <?php _e( 'Your NetGSM Information <br> Enter the subscriber number without a leading 0 (E.g. 212xxxxxx) <br> After entering your password, save it if your password and subscriber number are correct <br> Your SMS titles will appear <br> Please select the title and save again.', 'shipment-tracking' ) ?>
                    </th>
                    <td>
                        <label for="NetGsm_UserName" class="label-bold">Subscriber No.</label> <br>
                        <input type="text" id="NetGsm_UserName" name="NetGsm_UserName" value="<?php echo esc_attr($NetGsm_UserName); ?>" required>
                    </td>
                    <td>
                        <label for="NetGSM" class="label-bold">Password</label> <br>
                        <input type="password" id="NetGSM" name="NetGsm_Password" value="<?php echo __($NetGsm_Password);?>" required>
                        <br>
                    </td>
                </tr>

                <tr valign="top" class="netgsm" <?php if ($sms_provider != 'NetGSM') echo 'style="display:none"'?>>
                    <th scope="row" style="width:25%"></th>
                    <td>
                        <label for="NetGsm_Header" class="label-bold">Your SMS Title</label> <br>
                        <?php
                                if ($NetGsm_Password && $NetGsm_UserName) {
                                    $netGsm_Header_get = shipment_tracking_get_netgsm_headers($NetGsm_UserName,$NetGsm_Password);
                                    if (!$netGsm_Header_get) {
                                        echo 'Your NetGSM username or password is incorrect!';
                                    } else {
                                        echo '<select name="NetGsm_Header" id="NetGsm_Header">';
                                        foreach ($netGsm_Header_get as $key => $value) {
                                            if ($NetGsm_Header == $value) {
                                                echo '<option selected value="'.$value.'">'.$value.'</option>';
                                            } else {
                                                echo '<option value="'.$value.'">'.$value.'</option>';
                                            }
                                        }
                                        echo '</select>';
                                    }
                                }
                            ?>
                    </td>
                    <td>
                        <?php
                                if ($NetGsm_Password && $NetGsm_UserName) {
                                    $NetGSM_packet_info = shipment_tracking_get_netgsm_packet_info($NetGsm_UserName,$NetGsm_Password);
                                    $NetGSM_credit_info = shipment_tracking_get_netgsm_credit_info($NetGsm_UserName,$NetGsm_Password);
                                    if ($NetGSM_packet_info) {
                                        echo '<b>Your Remaining Packages:</b> <br> '.__($NetGSM_packet_info);
                                    }
                                    if ($NetGSM_credit_info) {
                                        echo '<br><br><b>Your Remaining Credit:</b> <br> '.esc_attr($NetGSM_credit_info) .' TL';
                                    }
                                }
                            ?>
                    </td>
                </tr>



                <tr class="Kobikom" <?php if( $sms_provider != 'Kobikom' ) echo 'style="display:none"'?>>
                    <th scope="row" style="width:40%">
                        <hr>
                    </th>
                    <td>
                        <hr>
                    </td>
                    <td>
                        <hr>
                    </td>
                </tr>

                <tr valign="top" class="Kobikom" <?php if( $sms_provider != 'Kobikom' ) echo 'style="display:none"'?>>
                    <th scope="row" style="width:25%">
                        <?php _e( 'Your Kobikom Information <br> You need to enter your Kobikom API address.', 'shipment-tracking' ) ?>
                    </th>
                    <td>
                        <label for="Kobikom_ApiKey" class="label-bold">Your Kobikom API key</label> <br>
                        <textarea type="text" id="Kobikom_ApiKey" name="Kobikom_ApiKey"  rows="6" ><?php echo esc_attr($Kobikom_ApiKey); ?></textarea>
                    </td> 
                </tr>

                <tr valign="top" class="Kobikom" <?php if ($sms_provider != 'Kobikom') echo 'style="display:none"'?>>
                    <th scope="row" style="width:25%"></th>
                    <td>
                        <label for="KobiKom_Header" class="label-bold">Your SMS Title</label> <br>
                        <?php
                        
                                if ($Kobikom_ApiKey) { 
                                    $KobiKom_get_Headers = shipment_tracking_get_kobikom_headers($Kobikom_ApiKey);
                                    if (!$KobiKom_get_Headers) {
                                        echo 'Your Kobikom API key is incorrect!';
                                    } else {
                                       echo '<select name="Kobikom_Header" id="Kobikom_Header">';
                                        foreach ($KobiKom_get_Headers as $key => $value) {
                                        
                                            if ($Kobikom_option_Header == $value['title']) {
                                                echo '<option selected value="'.$value['title'].'">'.$value['title'].'</option>';
                                            } else {
                                                echo '<option value="'.$value['title'].'">'.$value['title'].'</option>';
                                            }
                                        }
                                        echo '</select>';
                                    }
                                }
                            ?>
                    </td>
                    <td>
                        
                        <?php
                                if ($Kobikom_ApiKey){
                                    $KobiKom_get_Credit = shipment_tracking_get_kobikom_balance($Kobikom_ApiKey);
                                    echo "Your Kobikom Packages : <br> <hr>";
                                    if ($KobiKom_get_Credit) {
                                        foreach ($KobiKom_get_Credit as $key => $value) {
                                            echo $value['name'] . ' : <br> Remaining Credit ' . $value['amount'] . ' SMS <br> Package Expiration Date: ' . $value['finished_at'] . '<br>';
                                        }
                                    } else {
                                        echo "Your Kobikom Api Key is incorrect";
                                    }
                                }
                            ?>
                    </td>
                </tr>

                <tr valign="top">
                    <td>
                        <h3>Variables</h3>
                        <b>{customer_name} : Customer name</b> <br>
                        <b>{order_id} : Order number</b> <br>
                        <b>{company_name} : Shipping company name</b> <br>
                        <b>{tracking_number} : Shipment number</b> <br>
                        <b>{tracking_url} : Shipment tracking link</b> <br>
                    </td>
                    <td colspan="2">

                        <textarea type="text" id="sms_template" style="width:100%" name="shipment_tracking_sms_template" rows="6" placeholder="Dear {customer_name}, your order with {order_id} has been delivered to {company_name}. Your cargo tracking number: {tracking_number}. Your cargo tracking link: {tracking_url}. Good day."><?php echo esc_attr($sms_template); ?></textarea>

                    </td>
                     
                </tr>

             
            </table>

            <?php submit_button(); ?>

            <script>
                jQuery(document).ready(function ($) {
                    $('input[type=radio][name=sms_provider]').change(function () {
                        if (this.value === 'none') {
                            $('.netgsm, .Kobikom').hide();
                        } else if (this.value === 'NetGSM') {
                            $('.netgsm').show(2000);
                            $('.Kobikom').hide();
                        } else if (this.value === 'Kobikom') {
                            $('.Kobikom').show(2000);
                            $('.netgsm').hide();
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


