<?php
/*  Plugin Name: InnoLeads
  Plugin URI: http://customer.strateqleads.com
  Description: Helps to implement campaigns from InnoLeads.
  Version: 0.2.3
	Author: Emil Isaksson
	Author URI: http://ka50.se
	License: Free
*/
require plugin_dir_path( __FILE__ ) . 'config.inc.php';

$website_token = get_option('knox_website_token');
$authentication_token = get_option('knox_authentication_token');
$device_id = get_option('knox_device_id');
$device_info = array();
$device_info['Version'] = $wp_version;
if (empty($device_id)) {
    $device_id = uniqid();
    update_option('knox_device_id', $device_id);
}

function get_init_values()
{
    global $website_token;
    global $authentication_token;
    global $device_id;
    global $device_info;
    ?>
    <script>
        var initValues = {
            authenticationToken: '<?php echo $authentication_token; ?>',
            websiteToken: '<?php echo $website_token; ?>',
            deviceId: '<?php echo $device_id; ?>',
            friendlyName: 'Wordpress - <?php echo get_bloginfo('name'); ?>',
            deviceInfo: '<?php echo json_encode($device_info); ?>'
        };


        function reportEvent($scope, $http, message, _severity, onFinish)
        {
            var data = $scope.getBaseParams();
            data.message = message;
            data.source = 'Wordpress Frontend';
            switch(_severity)
            {
                case severity.info:
                    data.severity = 'Info';
                    break;
                case severity.warning:
                    data.severity = 'Warning';
                    break;
                case severity.Error:
                    data.severity = 'Error';
                    break;
            }
            $http.post(knox_api_url + "/EventLog/Log", data).then(onFinish, onFinish);
        }

        function initScope($scope)
        {
            $scope.getBaseParams = function(){
                return {authenticationToken: $scope.values.authenticationToken, deviceId: $scope.values.deviceId, language:'sv-SE' };
            };
        }
    </script>
<?php
}



include(plugin_dir_path(__FILE__) . 'dashboard.php');
include(plugin_dir_path(__FILE__) . 'admin_page.php');

if (function_exists('add_action')) {
    add_action('admin_menu', 'knox_plugin_menu');

    add_filter('the_content', 'knox_script', 20);

}


function knox_enqueue_admin_script($hook)
{
 /*   if ('toplevel_page_knox_admin' != $hook) {
        return;
    }
*/
    wp_register_style('knox_admin_css', plugin_dir_url(__FILE__) . '/style/style.css', false, '1.0.0');
    wp_register_style('knox_dashboard_css', plugin_dir_url(__FILE__) . '/style/dashboard.css', false, '1.0.0');

    wp_register_script('knox_dashboard_script', plugin_dir_url(__FILE__) . '/script/dashboard.js', false, '1.0.0');
    wp_register_script('knox_admin_script', plugin_dir_url(__FILE__) . '/script/script.js', false, '1.0.0');
    wp_register_script('knox_angular', plugin_dir_url(__FILE__) . '/script/angular.min.js', false, '1.0.0');
    wp_register_script('knox_chart', plugin_dir_url(__FILE__) . '/script/Chart.min.js', false, '1.0.0');

}

function knox_plugin_menu()
{
    add_menu_page('knox', 'InnoLeads', 'manage_options', 'knox_admin', knox_plugin, plugin_dir_url(__FILE__) . 'images/icon.png');
}


function knox_script($content)
{
    global $api_url;
    $token = get_option('knox_website_token');

    if (!empty($token)) {
        wp_enqueue_script('knox-script', $api_url.'/js/' . $token);
    }

    return $content;
}

?>