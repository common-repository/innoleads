$ = jQuery;
$(document).ready(function() {
    // $('#knox_websites').hide();
});

var colors = [
    "#aece00",
    "#00c7ce",
    "#ae00ce",
    "#ce0030",
    "#0ace00",
    "#ebc309",
    "#007f84",
    "#840000",
    "#464479",
    "#393913",
    "#546f57",
    "#6bc9ff",
    "#2ea988"
];



var knoxApp = angular.module('knox_dashboard', []);
knoxApp.controller("knox_dashboard_controller",
    function ($scope, $http, $interval) {
        $scope.values = initValues;
        initScope($scope);

        var data = $scope.getBaseParams();
        data.websiteToken = $scope.values.websiteToken;
        data.activeOnly = true;
        $http.post(knox_api_url + '/Campaigns/GetCampaignsByWebsiteToken', data).success(function(response){
            var innerData = $scope.getBaseParams();
            if(response.length > 0)
            {
                innerData.campaignToken = response[0].Token;
            }

            $http.post(knox_api_url + '/Statistics/CampaignToday', innerData).success(function(innerResponse){
                var graph_data = [];
                $scope.eventLabels = [];
                $scope.todaysCampaign = innerResponse.CampaignName;
                for(var i = 0; i < innerResponse.EventCounts.length; i++)
                {
                    innerResponse.EventCounts[i].Color = colors[i];
                    graph_data.push({
                        value: innerResponse.EventCounts[i].Count,
                        color: innerResponse.EventCounts[i].Color,
                        label: innerResponse.EventCounts[i].Name
                    });
                    $scope.eventLabels.push(innerResponse.EventCounts[i]);
                }
                setTimeout(function () {
                    var ctx = document.getElementById("knox_today_graph").getContext("2d");
                    var munk = new Chart(ctx).Doughnut(graph_data, { responsive: true });
                }, 100);
            });
        });

/*
        $http.post('/customer/statistics/geteventcounts/' + $scope.campaignToken).success(
            function (response) {
                var graph_data = [];
                for (var i = 0 ; i < response.events.length ; i++) {
                    graph_data.push({
                        value: response.events[i].count,
                        color: response.events[i].color,
                        label: response.events[i].caption
                    });
                }
                $scope.graph_data = graph_data;
                $scope.events = response.events;

                setTimeout(function () {
                    var ctx = document.getElementById("trigged_events_chart").getContext("2d");
                    var munk = new Chart(ctx).Doughnut($scope.graph_data, { responsive: true });
                }, 100);
            }
        );
        */
    });