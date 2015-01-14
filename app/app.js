var app = angular.module('TubeApp', ['ui.bootstrap','ngSanitize','ngCookies','angularFileUpload','ngTouch','ajoslin.mobile-navigate', 'directive.g+signin', 'googlechart']);
//'ngRoute',
app.config(function ($routeProvider,$locationProvider) {
    var t=new Date().getTime();

    $routeProvider
        .when('/console',
            {
                controller: 'TubeController',
                templateUrl: '/app/partials/'+skin+"/"+ (__isMobile ? 'mobile/home.html?t='+t : 'home.html?t='+t)
            })
        .when('/dashboard',
            {
                controller: 'TubeController',
                templateUrl: '/app/partials/'+skin+"/"+ (__isMobile ? 'mobile/dashboard.html?t='+t : 'dashboard.html?t='+t)
            })
        .when('/login',
            {
                controller: 'TubeController',
                templateUrl: '/app/partials/'+skin+"/"+'login.html'
            })
        .when('/alt',
            {
                controller: 'TubeController',
                templateUrl: '/app/partials/'+skin+"/"+'alt_home.html'
            })
        .when('/results/:search_query',
           {
               controller: 'TubeController',
               templateUrl: '/app/partials/'+skin+"/"+'results.html'
           })
        .when('/CreateGameRR',
           {
               controller: 'TubeController',
               templateUrl: '/app/partials/'+skin+"/"+'CreateGameRR.html'
           })
        .when('/signIn',
           {
               controller: 'TubeController',
               templateUrl: '/app/partials/'+skin+"/"+'signIn.html'
           })
        .when('/forgotPassword',
           {
               controller: 'TubeController',
               templateUrl: '/app/partials/'+skin+"/"+'forgotPassword.html'
           })

        .when('/signOut',
           {
               controller: 'TubeController',
               templateUrl: '/app/partials/'+skin+"/"+'signOut.html'
           })
        .when('/AddAccount',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'AddAccount.html'
          })
        .when('/RegFrom',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'RegFrom.html'
          })
         .when('/activation/:link',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'activation.html?t='+t
          })
         .when('/play/:gid',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+(__isMobile ? 'mobile/play.html?t='+t : 'play_new.html?t='+t)
          })
         .when('/createGame',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'createGame.html?t='+t
          })
          .when('/channel/:cid',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+(__isMobile ? 'mobile/channel.html?t='+t : 'channel.html?t='+t)
          })

          .when('/my_channels',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'my_channels.html?t='+t
          })
          .when('/my_channel/:cid',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'my_channel.html?t='+t
          })
          .when('/article/:article_name',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'article.html'
          })


          .when('/editGame/:gid',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'editGame.html'
          })
          
          .when('/share_game/:gid/:result',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+'share_game.html'
          })


          .when('/settings',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+"/"+ (__isMobile ? 'mobile/settings.html?t='+t : 'settings.html?t='+t)
          })
          
          
          .when('/report/:game_type/:gid',
          {
              controller: 'TubeController',
              templateUrl: '/app/partials/'+skin+'/reports/report.html?t='+t
          })
          
          






        .otherwise({ redirectTo: '/' });

        $locationProvider.html5Mode(true);

});


//app.run(function($route, $http, $templateCache) {
//  angular.forEach($route.routes, function(r) {
//    if (r.templateUrl) {
//      $http.get(r.templateUrl, {cache: $templateCache});
//    }
//  });
//})



app.run(function ($rootScope, $location) {
    $rootScope.$on('$routeChangeSuccess', function(){
        ga('send', 'pageview', $location.path());
    });
});

app.run(function ($rootScope) {
    window.fbAsyncInit = function () {
        FB.init({
            appId:'1511607775728782',
            status:true,
            cookie:true,
            xfbml:true
        });

        FB.Event.subscribe('auth.statusChange', function(response) {
            $rootScope.$broadcast("fb_statusChange", {'status': response.status});
        });
    };

    (function (d) {
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        ref.parentNode.insertBefore(js, ref);
    }(document));
});

app.factory('Usertrack', function ($rootScope,$http) {
    return {
        __event:function (event,event_id) {
            $http.post('/ut',{op:"event",event:event,event_id:event_id});
        }
    };
});


app.factory('Gamereport', function ($rootScope,$http) {
    return {
        __generalDataLoad:function(gid) {
            return $http.post('/rt',{op:"loadGeneralData",gid:gid});
        },
        __trafficDataLoad:function(gid) {
            return $http.post('/rt',{op:"trafficDataLoad",gid:gid});
        },
        __resultsDataLoad:function(gid) {
            return $http.post('/rt',{op:"resultsDataLoad",gid:gid});
        },
        __leadsDataLoad:function(gid) {
            return $http.post('/rt',{op:"leadsDataLoad",gid:gid});
        },
        __funnelDataLoad:function(gid) {
            return $http.post('/rt',{op:"funnelDataLoad",gid:gid});
        }
    };
});


app.factory('uploaderWLD', function ($compile,$rootScope,$http) {
    var obj={};
        obj._= function(el){ 
                return document.getElementById(el); 
        };
        obj.__init = function() {
            $(".file_uploaded").hide();
            $("#upload_status").hide();
            // check if form already exist:
//            var formHTML='<form id="WLD_upload_form" style="position:absolute;z-index:100000;left:50%;top:5%;margin-left:-200px;height:50px;border:1px solid blue;display:block;background-color:#ffffff;"  enctype="multipart/form-data" method="post"> ' +
//                    '<input type="file" name="img_upload" id="img_upload">'+
//                    '<progress id="WLD_progressBar" value="0" max="100" style="width:300px;"></progress> '+
//                    '<h3 id="status"></h3>'+
//                    '<p id="loaded_n_total"></p>'+
//                    '</form>';
//            //this._('WLD_upload_form');
//            if(!this._('WLD_upload_form')) {
//                $("body").prepend(formHTML); 
//            }
            ///////////////////////////////  
            
            obj.__loadLibrary();
        };
        obj.__loadLibrary = function() {
            $http.post('/gt',{op:"getLibrary"})
            .success(function(data, status, headers, config) {
                $rootScope.library_thumbnail=data;
            });
        };
        obj.__deleteThumb = function(id) {
            $http.post('/gt',{op:"deleteThumb",id:id})
            .success(function(data, status, headers, config) {
                obj.__loadLibrary();
            });
        };
        obj.__choose_file = function() {
            $("#file_upload").trigger("click");
        };
        obj.__upload = function(user_id){

            
            var file = this._("file_upload").files[0];
            //alert(file.name+" | "+file.size+" | "+file.type); 
            
            $(".file_uploaded").fadeIn();
            $(".file_uploaded").addClass("on_work");
            $("#upload_status").fadeIn();
            $("#file_upload_status_block").html(file.name);
            $("#WLD_file_name").val(file.name);
            $("#addFileButton").hide();
            
            var formdata = new FormData(); 
            formdata.append("img_upload", file); 
            formdata.append("file_name", file.name);
            formdata.append("user_id", user_id);
            var ajax = new XMLHttpRequest(); 
            ajax.upload.addEventListener("progress", obj.__progressHandler, false); 
            ajax.addEventListener("load", obj.__completeHandler, false); 
            ajax.addEventListener("error", obj.__errorHandler, false); 
            ajax.addEventListener("abort", obj.__abortHandler, false); 
            
            $http.post('/gt',{op:"getUploadURLG",url:"/img_uploaderG"})
            .success(function(data, status, headers, config) {
                ajax.open("POST", data.url); 
                ajax.send(formdata);
            });
        };
        obj.__progressHandler = function(event) {
            //document.getElementById("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total; 
            var percent = (event.loaded / event.total) * 100; 
            //document.getElementById("WLD_progressBar").value = Math.round(percent); 
            $(".file_progress").css({
                width:Math.round(percent)+"%"
            });
            //document.getElementById("status").innerHTML = Math.round(percent)+"% uploaded... please wait"; 
            $("#upload_status").html("Uploaded "+event.loaded+" bytes of "+event.total+ " ("+Math.round(percent)+"%)");
        };
        obj.__completeHandler = function(event) {
            //document.getElementById("status").innerHTML = event.target.responseText; 
            $("#upload_status").html(event.target.responseText);
            $(".file_progress").css({
                width:"0%"
            });
            $("#addFileButton").show();
            $(".file_uploaded").removeClass("on_work");
            obj.__loadLibrary();
        };
        obj.__errorHandler = function(event) {
            document.getElementById("status").innerHTML = "Upload Failed";
        };
        obj.__abortHandler = function(event) {
            document.getElementById("status").innerHTML = "Upload Aborted"; 
        };
    
    return obj;
});

