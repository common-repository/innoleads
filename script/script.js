$ = jQuery;
$(document).ready(function() {
   // $('#knox_websites').hide();
});

var severity = {
    info: 1,
    warning: 2,
    error: 3
}



var knoxApp = angular.module('knox_admin', []);
knoxApp.controller("admin_controller",
    function ($scope, $http, $interval) {
        $scope.values = initValues;
        initScope($scope);

        $scope.getBaseParams = function(){
            return {authenticationToken: $scope.values.authenticationToken, deviceId: $scope.values.deviceId };
        };

        if($scope.values.authenticationToken != '')
        {
            $http.post(knox_api_url + "/Account/CheckAuthenticationToken", {
                authenticationToken: $scope.values.authenticationToken,
                deviceId: $scope.values.deviceId
            }).success(function(response) {
                if(!response.Valid)
                {
                    $scope.values.currentStep = 'login';
                    $scope.loginMessage = 'Inloggningssessionen har gått ut.';
                }
            });
        }


        $scope.getWebsiteList = function()
        {
            var data = $scope.getBaseParams();
            $http.post(knox_api_url + "/Websites/GetAvailableWebsites", data).success(function(response) {
                $scope.websites = [];
                for(var i = 0 ; i < response.Websites.length ; i++)
                {
                    $scope.websites.push({name: response.Websites[i].Name, token: response.Websites[i].Token});
                }
            });
        };

        $scope.getCurrentWebsite = function() {
            var data = $scope.getBaseParams();
            data.token = $scope.values.websiteToken;
            $http.post(knox_api_url + "/Websites/GetWebsiteByToken", data).success(function(response) {
                $scope.currentWebsite = {name: response.Name, url: response.Url, token: response.Token };
            });
        }


        $scope.login = function()
        {
            $http.post(knox_api_url + "/Account/Authenticate", {
                username: $scope.loginUsername,
                password: $scope.loginPassword,
                deviceId: $scope.values.deviceId,
                friendlyName: $scope.values.friendlyName,
                deviceType: 'CMS',
                devicePlatform: 'Wordpress',
                deviceInfo: $scope.values.deviceInfo
                }).success(function(response) {
                if(response.Succeeded)
                {
                    $scope.values.authenticationToken = response.AuthenticationToken;
                    $http({
                        method: 'POST',
                        url:ajaxurl,
                        params:{action: 'add_authentication_token', authenticationToken: response.AuthenticationToken}
                    }).success(function(response) {
                        $scope.loginUsername = '';
                        $scope.loginPassword = '';
                        $scope.values.currentStep = 'choose_website';
                        $scope.getWebsiteList();
                    });
                }
                else
                {
                    $scope.loginMessage = 'Inloggning misslyckades.';
                }
            });
        }



        $scope.chooseWebsite = function(token)
        {
            reportEvent($scope, $http, 'User bound this instance to website with token ' + token, severity.info);
            $http({
                method: 'POST',
                url: ajaxurl,
                params: {action: 'add_website_token', websiteToken: token}
                }).success(function(response) {
                $scope.values.websiteToken = token;
                $scope.values.currentStep = 'admin_panel';
                $scope.getCurrentWebsite();
            });
        }

        $scope.logout = function()
        {
            reportEvent($scope, $http, 'User unbound this instance from the associated website.', severity.info);
            var data = $scope.getBaseParams();
            $http.post(knox_api_url + "/Account/ClearAuthenticationToken", data).success(function(response) {
                $http({
                    method: 'POST',
                    url: ajaxurl,
                    params: {action: 'log_out'}}).success(function(response) {
                    location.reload();
                });
            });
        };

        if($scope.values.currentStep == 'choose_website')
        {
            $scope.getWebsiteList();
        }
        else if($scope.values.currentStep == 'admin_panel')
        {
            $scope.getCurrentWebsite();
        }

    });



