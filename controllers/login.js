var app = angular.module('login',['login-module']);

app.controller('loginCtrl',function($scope,loginService) {
	
	$scope.views = {};
	$scope.account = {employee_username: '',employee_password: ''};
	
	$scope.login = loginService.login;

});