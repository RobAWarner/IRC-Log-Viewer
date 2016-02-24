/* Init AngularJS App */
var app = angular.module('IRCLogViewer', ['ngRoute', 'ngSanitize']);

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
