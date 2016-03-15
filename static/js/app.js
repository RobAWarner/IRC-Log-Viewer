/* Initialize AngularJS App */
var app = angular.module('IRCLogViewer', ['ngRoute', 'ngSanitize']);

/* App Variables */
var templateBase = 'templates/';
var ajaxURL = 'ajax.php';

/* Route Config */
app.config(["$routeProvider", "$locationProvider", function($routeProvider, $locationProvider) {
    $routeProvider
        .when('/', {
            templateUrl: 'templates/home.html',
            controller: 'HomeCtrl'
        })
        .when('/:network', {
            templateUrl: 'templates/channels-list.html',
            controller: 'ChannelsCtrl'
        })
        .when('/:network/:channel', {
            templateUrl: 'templates/logs-list.html',
            controller: 'LogsCtrl'
        })
        .when('/:network/:channel/:logdate', {
            templateUrl: 'templates/view-log.html',
            controller: 'LogCtrl'
        })
        .otherwise({
            redirectTo: '/'
        });

    $locationProvider.html5Mode(false);
    $locationProvider.hashPrefix('!');
}]);


/* Controllers */

// Main page controller
app.controller('PageCtrl', ['$scope',
    function($scope) {
        // Create a data object
        $scope.data = new Object;

        // Set page title data
        $scope.data.pageTitle = '';
    }
]);

// Home view controller
app.controller('HomeCtrl', ['$scope',
    function($scope) {
        // Reset page title data
        $scope.$parent.data.pageTitle = '';
    }
]);

// Menu/Network list controller
app.controller('NetworksCtrl', ['$scope', '$http',
    function($scope, $http) {
        // Create a data object
        $scope.data = new Object;

        // Create an array to store network list
        $scope.data.networks = new Array;

        // Fetch network list
        $http.get(ajaxURL+"?fetch=network-list")
            .then(function(response) {
                // Had an error occured?
                if(typeof response.data.success !== 'undefined' && response.data.success === true) {
                    if(typeof response.data.return.networks !== 'undefined') {
                        // Set the networks array
                        $scope.data.networks = response.data.return.networks;
                    }
                }
            });
    }
]);

// Channel list controller
app.controller('ChannelsCtrl', ['$scope', '$routeParams', '$http',
    function($scope, $routeParams, $http) {
        // Create a data object
        $scope.data = new Object;

        // Create an array to store channel list in
        $scope.data.channels = new Array;

        // Show the preloader
        $scope.data.loading = true;

        // Set current network name
        $scope.data.network = $routeParams.network;

        $scope.$parent.data.pageTitle = $routeParams.network;

        // Fetch channel list
        $http.get(ajaxURL+"?fetch=channel-list&for_network="+encodeURIComponent($scope.data.network))
            .then(function(response) {
                // Had an error occured?
                if(typeof response.data.success !== 'undefined' && response.data.success === true) {
                    if(typeof response.data.return.channels !== 'undefined') {
                        // Loop channels
                        for(var i=response.data.return.channels.length-1; i>=0; i--) {
                            // Set prefix color
                            response.data.return.channels[i].colorClassNum = getColorFromString(response.data.return.channels[i].display_name);

                            // Set URL
                            response.data.return.channels[i].url = encodeURIComponent($scope.data.network)+'/'+encodeURIComponent(response.data.return.channels[i].name);
                        }

                        // Set channels array
                        $scope.data.channels = response.data.return.channels;
                    }
                }
            })
            .finally(function() {
                // Hide the preloader
                $scope.data.loading = false;
            });
    }
]);
