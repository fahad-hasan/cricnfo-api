var interval;

app.controller("matchListController", function($scope, $http){
	$http.get("http://www.mylocalhost.local/cricinfo-api/public/api/matches").success(function(response){
		$scope.matches = response;
	});
});

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider.
	when('/details/:guid', {
		templateUrl: 'matchDetails.htm',
		controller: 'matchDetailsController'
	}).
	otherwise({
		redirectTo: '/'
	});
}]);

app.factory('ScoreService', function($http) {     
   var factory = {};  
   factory.updateScore = function(guid, scope) {
      $http.get("http://www.mylocalhost.local/cricinfo-api/public/api/matches/"+guid).success(function(response){
			scope.score = response;
		});
   }
   return factory;
}); 

app.controller('matchDetailsController', function($scope, $routeParams, $interval, ScoreService) {
	var guid = $routeParams.guid;
	ScoreService.updateScore(guid, $scope);
	$interval.cancel(interval);
	//interval = $interval(function(){
	//	ScoreService.updateScore(guid, $scope);
	//}, 5000)
});
