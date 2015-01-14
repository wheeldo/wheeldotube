angular.module('myApp', ['ajoslin.mobile-navigate'])
.config(function($routeProvider) {
  $routeProvider.when("/one", {
    templateUrl: "mobile/content/page1.html"
  }).when("/two", {
    templateUrl: "mobile/content/page2.html",
    transition: "modal" //this is overwritten by the go() in home.html
  }).when("/popup", {
    templateUrl: "mobile/content/popup.html",
    transition: "modal"
  }).when("/monkey", {
    templateUrl: "mobile/content/monkey.html"
  }).when("/backwards", {
    templateUrl: "mobile/content/backwards.html",
    reverse: true
  }).when("/", {
    templateUrl: "mobile/content/home.html"
  }).otherwise({
    redirectTo: "/"
  });
})
.run(function($route, $http, $templateCache) {
  angular.forEach($route.routes, function(r) {
    if (r.templateUrl) { 
      $http.get(r.templateUrl, {cache: $templateCache});
    }
  });
})
.controller('MainCtrl', function($scope, $navigate) {
  $scope.$navigate = $navigate;
})
.directive('ngTap', function() {
  var isTouchDevice = !!("ontouchstart" in window);
  return function(scope, elm, attrs) {
    if (isTouchDevice) {
      var tapping = false;
      elm.bind('touchstart', function() { tapping = true; });
      elm.bind('touchmove', function() { tapping = false; });
      elm.bind('touchend', function() { 
        tapping && scope.$apply(attrs.ngTap);
      });
    } else {
      elm.bind('click', function() {
        scope.$apply(attrs.ngTap);
      });
    }
  };
});

