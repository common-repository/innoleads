<?php
add_action('wp_dashboard_setup', 'knox_add_dashboard_widgets' );



function knox_add_dashboard_widgets() {
    wp_add_dashboard_widget('knox_dashboard_widget', 'InnoLeads', 'knox_dashboard_widget_function');
}


function knox_dashboard_widget_function($post, $callback_args ) {
    global $api_url;
    wp_enqueue_style('knox_dashboard_css');
    wp_enqueue_script('knox_angular');
    wp_enqueue_script('knox_chart');
    wp_enqueue_script('knox_dashboard_script');

    get_init_values();
    ?>
    <script>
        var knox_api_url = '<?php echo $api_url; ?>/External';
    </script>
<div class="box" ng-app="knox_dashboard">
    <div class="inner" ng-controller="knox_dashboard_controller">
        <h3>Idag: {{todaysCampaign}}</h3>
        <canvas id="knox_today_graph" style="width:50%;"></canvas>
        <div class="event_captions">
            <div class="caption" ng-repeat="event in eventLabels" ng-style="{color:event.Color}">{{event.Name}}: {{event.Count}} gånger</div>
        </div>
    </div>
</div>
<?php
}

?>