<?php

add_action('admin_enqueue_scripts', 'knox_enqueue_admin_script');

add_action('wp_ajax_add_website_token', 'knox_save_website_token');
add_action('wp_ajax_add_authentication_token', 'knox_save_authentication_token');
add_action('wp_ajax_log_out', 'knox_log_out');


function knox_plugin()
{
    global $authentication_token;
    global $website_token;
    global $api_url;

    wp_enqueue_style('knox_admin_css');
    wp_enqueue_script('knox_angular');
    wp_enqueue_script('knox_admin_script');

    wp_enqueue_script('jquery');
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }





    $current_step = 'login';
    if (!empty($authentication_token)) {
        $current_step = 'choose_website';
    }

    if (!empty($website_token)) {
        $current_step = 'admin_panel';
    }

    get_init_values();
    ?>
<script>
    var knox_api_url = '<?php echo $api_url; ?>/External';
    initValues.currentStep = '<?php echo $current_step; ?>';
</script>

    <div class="wrap" id="knox" ng-app="knox_admin" ng-controller="admin_controller">
        <div ng-show="values.currentStep == 'admin_panel'" class="box">
            <div class="inner">
                <div class="top_row">
                    <img class="logo" src="<?php echo plugin_dir_url(__FILE__) . 'images/logo.png'; ?>" class="logo"/>

                    <div class="tabs">
                        <div class="tab">
                            <a href="http://customer.strateqleads.com" target="_blank">
                                <img src="<?php echo plugin_dir_url(__FILE__) . 'images/white_icon.png'; ?>"
                                     class="icon"/>

                                <div class="title">InnoLeads<br/>webbpanel</div>
                            </a>
                        </div>
                    </div>
                </div>
                <br/>

                <h3>Din webbplats kommer att visa aktiva kampanjer från webbsidan {{currentWebsite.name}} i
                    InnoLeads.</h3>
                <br/>
                <br/>
                <input type="button" ng-click="logout();" value="Koppla från"/>
            </div>
        </div>
        <div class="box" ng-show="values.currentStep == 'login'">
            <div class="inner">
                <div class="top_row">
                    <img class="logo" src="<?php echo plugin_dir_url(__FILE__) . 'images/logo.png'; ?>"/>
                </div>
                <table id="knox_login" style="width:100%;">
                    <tr>
                        <th>
                            Användarnamn:
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="knox_username" ng-model="loginUsername"/>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Lösenord:
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <input type="password" id="knox_password" ng-model="loginPassword"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" value="Logga in" ng-click="login();"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{loginMessage}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="knox_websites" class="box" ng-show="values.currentStep == 'choose_website'">
            <div class="inner">
                <h3>Vilken webbsida är detta?</h3>
                <br/>

                <div class="websites_list">
                    <div class="website_option" ng-repeat="website in websites"
                         ng-click="chooseWebsite(website.token);">{{website.name}}
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}

function knox_log_out()
{
    update_option('knox_website_token', '');
    update_option('knox_authentication_token', '');
}

function knox_save_website_token()
{
    update_option('knox_website_token', $_REQUEST['websiteToken']);
}

function knox_save_authentication_token()
{
    update_option('knox_authentication_token', $_REQUEST['authenticationToken']);
}

?>