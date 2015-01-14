<!DOCTYPE html>
<html ng-app="myApp">
  
  <head lang="en">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Angular Mobile Nav</title>
    <link rel="stylesheet" type="text/css" href="/media/css/dice/style.css?t=<?=time()?>">
    <link rel="stylesheet" href="/vendor/mobile-nav/content/iui.css">
    <link rel="stylesheet" href="/vendor/mobile-nav/content/iui/default-theme.css" type="text/css"/>
    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.1.4/angular.min.js"></script>
    <script src="/mobile/content/app.js"></script>
    <script src="/vendor/mobile-nav/mobile-nav.js"></script>
    <link rel="stylesheet" href="/vendor/mobile-nav/mobile-nav.css">
  </head>
  
  <body ng-controller="MainCtrl">
    <mobile-view></mobile-view>
  </body>
</html>