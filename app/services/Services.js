app.service('Services', ['$location',function ($location) {

    var scope = angular.element(document.getElementById("root_scope")).scope();

    getChannelUsersService =  function(cid) {
       $.ajax({
                type: "post",
                url: '/gt',
                dataType:"json",
                data:{
                  op:"getChannelUsers",
                  cid:cid
                },
                success: function(data, textStatus, jqXHR) {
                    var scope = angular.element(document.getElementById("root_scope")).scope();
                    if(scope.$$phase || scope.$root.$$phase) {
                            scope.channel_users=data.users;
                    }
                    else {
                        scope.$apply(function () {
                            scope.channel_users=data.users;
                        });
                    }
                }
        });

    };

    getChannelService = function(cid) {
       $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"getChannel",
                  cid:cid
                },
                success: function(data, textStatus, jqXHR) {
                    var scope = angular.element(document.getElementById("root_scope")).scope();
                    if(scope.$$phase || scope.$root.$$phase) {
                            scope.channel_data=data.channel;
                            scope.your_channel=data.your_channel;

                    }
                    else {
                        scope.$apply(function () {
                            scope.channel_data=data.channel;
                            scope.your_channel=data.your_channel;
                        });
                    }
                }
        });

    }


    saveUserService = function(data) {
        $.ajax({
                type: "post",
                url: '/gt',
                dataType:"json",
                data:{
                  op:"saveUser",
                  data:data
                },
                success: function(data, textStatus, jqXHR) {

                }
        });
    }

    loadGameDesignService = function(game_id) {
        $.ajax({
                type: "post",
                url: '/gt',
                dataType:"json",
                data:{
                  op:"loadGameDesign",
                  game_id:game_id
                },
                success: function(data, textStatus, jqXHR) {
                    var scope = angular.element(document.getElementById("root_scope")).scope();
                    if(scope.$$phase || scope.$root.$$phase) {
                            scope.new_game.game_design=data;
                            scope.new_game.game_default_design =
                            scope.new_game.game_default_design =
                              {
                                 color_1:data.color_1,
                                 color_2:data.color_3,
                                 color_3:data.color_3,
                                 color_4:data.color_4,
                                 banner:data.banner,
                                 background:data.background
                             };
                    }
                    else {
                        scope.$apply(function () {
                            scope.new_game.game_design=data;
                            scope.new_game.game_default_design =
                              {
                                 color_1:data.color_1,
                                 color_2:data.color_3,
                                 color_3:data.color_3,
                                 color_4:data.color_4,
                                 banner:data.banner,
                                 background:data.background
                             };
                        });
                    }
                }
        });
    };

    getImageUploadURLService = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST', '/gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getImageUploadURL");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };


    getFileDataService = function(res_mark) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST', '/gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getFileData&res_mark="+res_mark);
            if (request.status === 200) {
				try {
                	res=jQuery.parseJSON(request.responseText);
              } catch(ex) {
              	res = {"image":""};
              }
            }
        return res;
    };

    getUploadUrlService = function(to_url) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST', '/gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getUploadUrl&to_url="+to_url);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };

    getHash = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST', '/gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getHash");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };


    signOutService = function() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST', '/op', false);  // `false` makes the request synchronous
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send("op=signOut");
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    };

    getUserSrvice = function() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST', '/op', false);  // `false` makes the request synchronous
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send("op=getUser&u="+u);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    };

    activateAccountService = function(link) {
        $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"activateAccount",
                  link:link
                },
                success: function(data, textStatus, jqXHR) {
                    if(data.status=="ok")
                        stopProcessing();
                    else
                        stopProcessing();
                        //coundDown(10);
//                        setTimeout(function(){
//                            $location.path("/signIn");
//                        },10000);
                }
        });
    };

    signInUserService = function(object,notes) {

      setProcessing();
      $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"signInUser",
                  email:object.email,
                  password:object.password
                },
                success: function(data, textStatus, jqXHR) {
                    //stopProcessing();
                    scope = angular.element(document.getElementById("root_scope")).scope();

                     if(data.status=="ok") {
                     	// if the user is currently viewing the signIn page, redirect to the home page
                     	if(window.location.href.indexOf('signIn') > -1 || window.location.href.indexOf('login') > -1) {
                        	window.location = '/';
                            }
                        else {
                        	window.location.reload();
                            }
                    }
                    else {
                        stopProcessingFaild();

                        if(scope.$$phase || scope.$root.$$phase) {
                                notes.error_03="Username or password is incorrect!";
                        }
                        else {
                            scope.$apply(function () {
                                notes.error_03="Username or password is incorrect!";
                            });
                        }
                    }
                }
        });
    };

    regNewUserService = function(object) {

      setProcessing();
      $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"regNewUser",
                  fname:object.fname,
                  lname:object.lname,
                  email:object.email,
                  password:object.password
                },
                success: function(data, textStatus, jqXHR) {
                    stopProcessing();
                }
        });
    };

    checkEmailService = function(email,notes) {
      $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"checkEmail",
                  email:email
                },
                success: function(data, textStatus, jqXHR) {
                    scope = angular.element(document.getElementById("root_scope")).scope();
                    if(data.status=="ok") {
                        $("#email").addClass("validf");
                        $(".email").addClass("validf");
                        if(scope.$$phase || scope.$root.$$phase) {
                                delete notes['error_02'];
                        }
                        else {
                            scope.$apply(function () {
                                delete notes['error_02'];
                            });
                        }

                    }
                    else {
                        $("#email").addClass("not_validf");
                        $(".email").addClass("not_validf");

                        if(scope.$$phase || scope.$root.$$phase) {
                                notes.error_02="Email already in use!";
                        }
                        else {
                            scope.$apply(function () {
                                notes.error_02="Email already in use!";
                            });
                        }
                    }
                }
        });
    };
}]);

app.factory('Facebook', function ($rootScope) {
    return {
        getLoginStatus:function () {
            FB.getLoginStatus(function (response) {
                $rootScope.$broadcast("fb_statusChange", {'status':response.status});
            }, true);
        },
        login:function () {
            FB.getLoginStatus(function (response) {
                switch (response.status) {
                    case 'connected':
                        $rootScope.$broadcast('fb_connected', {facebook_id:response.authResponse.userID});
                        break;
                    case 'not_authorized':
                    case 'unknown':
                        /*FB.login(function (response) {
                            if (response.authResponse) {
                                $rootScope.$broadcast('fb_connected', {
                                    facebook_id:response.authResponse.userID,
                                    userNotAuthorized:true
                                });
                            } else {
                                $rootScope.$broadcast('fb_login_failed');
                            }
                        }, {scope:'read_stream, publish_stream, email'});*/
                       top.location = '/fb_auth';
                        break;
                    default:
                        FB.login(function (response) {
                            if (response.authResponse) {
                                $rootScope.$broadcast('fb_connected', {facebook_id:response.authResponse.userID});
                                $rootScope.$broadcast('fb_get_login_status');
                            } else {
                                $rootScope.$broadcast('fb_login_failed');
                            }
                        });
                        break;
                }
            }, true);
        },
        logout:function () {
            FB.logout(function (response) {
                if (response) {
                    $rootScope.$broadcast('fb_logout_succeded');
                } else {
                    $rootScope.$broadcast('fb_logout_failed');
                }
            });
        },
        unsubscribe:function () {
            FB.api("/me/permissions", "DELETE", function (response) {
                $rootScope.$broadcast('fb_get_login_status');
            });
        }
    };
});