
var filmothekApp = angular.module('filmothekApp', [
  'ngRoute',
  'angular-underscore',
  'filmothekControllers',
//  'phonecatFilters',
  'filmothekServices'
]);

//filmothekApp.config(['$routeProvider',
//  function($routeProvider) {
//    $routeProvider.
//      when('/phones', {
//        templateUrl: 'partials/phone-list.html',
//        controller: 'PhoneListCtrl'
//      }).
//      when('/phones/:phoneId', {
//        templateUrl: 'partials/phone-detail.html',
//        controller: 'PhoneDetailCtrl'
//      }).
//      otherwise({
//        redirectTo: '/phones'
//      });
//  }]);