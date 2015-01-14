var loadded_parts={
    dictionary:false,
    question:false
};

var BASE="/games/logic/test_yourself";
var or_lang="en";

var shareGameController = function ($scope, $http,  $modalInstance, f_data) {
    
    $scope.frame_src="/share_game/"+f_data.gid+"/I got "+f_data.result.headline + ". ";
    var h=$(window).height();
    if(h>600)
        h=600;
    $scope.frame_style={height:h+"px"};
    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
    
};

var RegController = function ($scope, $http,  $modalInstance, f_data) {

    $scope.reg = {
      ID:0,
      fname:"",
      lname:"",
      email:"",
      subscribe:true,
      remember:true
    };


    var emailExists=false;
    $scope.checkEmail = function() {
        $http.post('/op',{op:"checkEmail",email:$scope.reg.email})
                .success(function(data, status, headers, config) {
                    if(data.status=="faild") {
                        emailExists=true;
                        $("#reg_email").addClass("emailNotValid");
                        $("#reg_email").removeClass("emailValid");
                        if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.error="Email already exists! sign in to play";
                        }
                        else {
                            $scope.$apply(function () {
                                $scope.error="Email already exists! sign in to play";
                            });
                        }
                    }
                    else {
                        emailExists=false;
                        $("#reg_email").addClass("emailValid");
                        $("#reg_email").removeClass("emailNotValid");
                        $scope.error="Email already exists!";
                        if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.error="";
                        }
                        else {
                            $scope.$apply(function () {
                                $scope.error="";
                            });
                        }
                    }
            });
    };
   

    $scope.registerNewUser = function() {
        if(!emailExists && $scope.reg.fname && $scope.reg.lname && $scope.reg.email) {


            $http.post('/op',{op:"regNewUserGame",reg_data:$scope.reg,gid:gid})
                .success(function(data, status, headers, config) {
                    $scope.reg.ID=data.user_id;

                    // reload the entire window to sync the session data for this newly registered member
                    //window.parent.location.reload();
                    //alert(ug);
                    if(!f_data.callbackE) {
                        //window.parent.location.reload();
                    }
                    ug=0;

                    if($scope.$$phase || $scope.$root.$$phase) {
                        $modalInstance.close($scope.reg);
                    }
                    else {
                        $scope.$apply(function () {
                            $modalInstance.close($scope.reg);
                        });
                    }
            });

        }
        else {
            $scope.error="Please fill all the fileds!";
        }
    };

    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };

};


var sentToFriendController = function ($scope, $http,  $modalInstance, f_data) {

    $scope.send_to_friend = {
      fname:"",
      lname:"",
      email:""
    };


    $scope.sendToFriend = function() {
        if($scope.send_to_friend.fname && $scope.send_to_friend.lname && $scope.send_to_friend.email) {


            $http.post('/op',{op:"sendToFriend",reg_data:$scope.send_to_friend,gid:gid,lang:lang})
                .success(function(data, status, headers, config) {
                    $modalInstance.close(1);
            });

        }
        else {
            $scope.error="Please fill all the fileds!";
        }
    }



    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
};


app.controller('TestYourselfController', function ($scope,$http,$cookies,$routeParams,$location,$modal,$templateCache,wheeldo_app,Report) {
    // set height for mobile
    if(__isMobile) {
        //var sc_h=window.parent.screen.height;
        /*
		var sc_h=parent.document.body.clientHeight;
        $("body").css({
            height:sc_h+"px"
        });
        $(".main_wrapper").css({
            height:sc_h+"px"
        })
        $(".fill_bars").css({
            height:sc_h+"px"
        })
        */



    };
    
    $scope.play_token = function() {
        $http.post('/bl',{op:"playToken",gid:gid});  
    };
    
    $scope.startClicked = function() {
        Report.__startClicked();
    };
    
    $scope.ctaClicked = function() {
        Report.__setCTA();
    };
    
    $scope.couponClicked = function() {
        Report.__setCoupon();
    };
    
    $scope.shareClicked = function() {
        Report.__setshare();
    };
    

    $scope.$on('event:google-plus-signin-success', function (event,authResult) {
		// only perform Google+ login if the user explicity pressed on the button
		if(!authResult['g-oauth-window']) return;

		gapi.client.load('plus','v1', function(){
			var request = gapi.client.plus.people.get( {'userId' : 'me'} );
			request.execute(function(profile){
				$.ajax({
					type: "post",
					url: '/op',
					dataType:"json",
					data:{
						op	: "googleLogin",
						data: profile,
						gid	: gid
					},
					success: function(data, textStatus, jqXHR) {
						try {
							stopProcessing();
						} catch(ex) { }

						if(data.status == 'ok') {
							if(typeof data.uid != 'undefined' && data.uid.length)
            					location = location.href.replace(/uid=[a-f0-9]+/, 'uid='+data.uid);
            				else
            					location.reload();
						}
						else {
							// @@@ error handling
						}
					}
				});

	    	});
		});
	});

	$scope.$on('event:google-plus-signin-failure', function (event,authResult) {
		//console.log(event);
		//console.log(authResult);
	});

	$scope.callAction = function(call_action_link) {
        if($scope.gameDetails.call_action_link == "sendToFriend"){
        	$scope.sendToFriend();
        }
        else{
        	window.open($scope.gameDetails.call_action_link, "_blank");
        }

    };
    
    
    $scope.inst_init = function() {
        Report.__initReport();
        if(referrer) {
            Report.__setReferrer(referrer);
        }
    };
    
    
    
    // create buffer:
    $scope.game_data=data;
    var q_c=0;
    $scope.questions_buffer=[];

    questions_buffer=[];
    for(i in data.questions) {
        var q=data.questions[i];
        $scope.questions_buffer.push(q);
    }
    
    $scope.game_progress={
        answerd:1,
        total:$scope.questions_buffer.length
    };
    
    
    ////////////////
    
    $scope.game_start=false;

    $scope.startGame = function() {
        total_score=0;
        $scope.game_start=true;
        $location.path("game");
    };
    
    
    
    $scope.q_init = function() {
        $scope.q=$scope.questions_buffer[q_c];
    };
    
    
    var total_score=0;
    
    
    $scope.fill_style={width:"0%"};
    $scope.select_ans = function(score,q_id,ans_id) {
        total_score+=parseInt(score);
        q_c++;
        
        
        if(q_c<$scope.game_progress.total) {
            $scope.game_progress.answerd=q_c+1;
        }
        
        var per=Math.ceil(q_c/$scope.questions_buffer.length*100);
        
        $scope.fill_style={width:per+"%"};
        
        
        $http.post(BASE+'/gt',{op:"setAns",start_time:start_time,q_id:q_id,ans_id:ans_id});
        
        if(q_c>=$scope.questions_buffer.length) {
            var score=Math.floor((total_score/$scope.questions_buffer.length));
            
            //setScore
            
            $http.post(BASE+'/gt',{op:"setScore",score:score,start_time:start_time})
                .success(function(data, status, headers, config) {
                    $location.path("game-end");
            });
            
            
        }
        else {
            $scope.q=$scope.questions_buffer[q_c];
        }
 
    };
    
    
    $scope.skipReg = function() {
        // skip report:
        Report.__skipRegClicked();
        ///////////////
        $cookies.skip_reg = gid;
        $location.path("game-end");
    };
    
    $scope.show_voucher=false;
    $scope.gameEndInit = function() {
        
        Report.__endGame();
        
        $scope.share_button=share_button;
        $scope.CTA_button=CTA_button;
        
        $scope.cta_text=call_action_text;
        $scope.cta_link=call_action_link;
        
        var skip_reg=$cookies.skip_reg;

        if(ug && results_signup==1 && (!skip_reg || (skip_reg && skip_reg!=gid))) {
            $location.path("reg");
        }
        else {
        
            setTimeout(function(){
                $http.post(BASE+'/gt',{op:"getScore"})
                    .success(function(data, status, headers, config) {
                        $scope.end_data=data;
                        $scope.result=calculateResult(data.score);

                });
            },1200);

            if(voucher) {
                $scope.show_voucher=true;
            }
        
        }
        
    };
    
    $scope.regInit = function() {
        if(signup_headline) {
            $scope.signup_headline=signup_headline;
        }
        
        $scope.tfc();
        if(!ug)
            $location.path("game-end");
    };
    
    $scope.sendVaucher = function() {
        
        if(ug==1) {
            $scope.register($scope.sendVaucher);
            return;
        }
        
        
        $("#sendVoucherButton").hide();
        $(".voucher_wait").show();

        $http.post('/op',{op:"sendMeVoucher",gid:gid,lang:lang})
            .success(function(data, status, headers, config) {
                $(".voucher_wait").hide();
                if(data.status=="ok") {
                    $(".voucher_sent").show();
                }
                else {
                    $(".voucher_faild").show();
                }


        });
    };
    
    $scope.callAction = function() {
        window.open($scope.cta_link, "_blank");
    };
    
    
    $scope.register = function(callback) {

        var callbackE=typeof callback != 'undefined'?true:false;
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: BASE+'/app/partials/includes/registration.html?t='+t,
          keyboard:false,
          backdrop:'static',
          windowClass: 'registrationPopUp',
          controller: RegController,
          resolve: {
              f_data:function () {
                return {
                    callbackE:callbackE
                };
              }
          }
        });

        modalInstance.result.then(function (reg_data) {
            if(typeof callback != 'undefined'){
               if(typeof callback=="function") {
                   setTimeout(function(){
                       callback();
                   },200);
                   
               } 
            }
            $scope.user={
                ID:reg_data.ID,
                name:reg_data.fname+" "+reg_data.lname
            };

            ug=0;
        }, function () {

        });
    };
    
    $scope.sendToFriendSent=false;
    $scope.sendToFriend = function()  {
        var t=new Date().getTime();
        var modalInstance = $modal.open({
            templateUrl: BASE+'/app/partials/includes/send_to_friend.html?t='+t,
            keyboard:true,
            //backdrop:'static',
            windowClass: 'registrationPopUp',
            controller: sentToFriendController,
            resolve: {
                f_data:function () {
                  return 0;
                }
            }
          });

          modalInstance.result.then(function (status) {
              if(status==1) {
                  $scope.sendToFriendSent=true;
              }
            }, function () {

            });

      };
    

    calculateResult = function(score) {
        
        var part=100/data.results.length;
        
        //console.log(data.results);
        
        var r_part=Math.floor(score/part);
        if(r_part>=data.results.length)
            r_part=r_part-1;
        return data.results[r_part];
    };
    
    $scope.shareGame = function(){
      var t=new Date().getTime();
        var modalInstance = $modal.open({
            templateUrl: BASE+'/app/partials/includes/share_game.html?t='+t,
            keyboard:true,
            windowClass: 'sharePopUp',
            controller: shareGameController,
            resolve: {
                f_data:function () {
                  return {
                      gid:gid,
                      result:$scope.result
                  };
                }
            }
        }); 
    };
    
    
    $scope.reg = {
      ID:0,
      fname:"",
      lname:"",
      email:"",
      password:"",
      subscribe:true,
      remember:true
    };


    var emailExists=false;
    $scope.emailExists=false;
    $scope.checkEmail = function() {
        $http.post('/op',{op:"checkEmail",email:$scope.reg.email})
                .success(function(data, status, headers, config) {
                    if(data.status=="faild") {
                        emailExists=true;
                        $("#reg_email").addClass("emailNotValid");
                        $("#reg_email").removeClass("emailValid");
                        if($scope.$$phase || $scope.$root.$$phase) {
                            //$scope.error="Email already exists! <a href='javascript:void(0)'>sign in to play</a>";
                        }
                        else {
                            $scope.$apply(function () {
                                //$scope.error="Email already exists! <a href='javascript:void(0)'>sign in to play</a>";
                            });
                        }
                    }
                    else {
                        emailExists=false;
                        $("#reg_email").addClass("emailValid");
                        $("#reg_email").removeClass("emailNotValid");
                        //$scope.error="Email already exists!";
                        if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.error="";
                        }
                        else {
                            $scope.$apply(function () {
                                $scope.error="";
                            });
                        }
                    }
                    
                    if($scope.$$phase || $scope.$root.$$phase) {
                        $scope.emailExists=emailExists;
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.emailExists=emailExists;
                        });
                    }
                    
            });
    };

    $scope.registerNewUser = function() {
        $scope.error="";
        if(!emailExists && $scope.reg.fname && $scope.reg.lname && $scope.reg.email) {


            $http.post('/op',{op:"regNewUserGame",reg_data:$scope.reg,gid:gid})
                .success(function(data, status, headers, config) {
                    $scope.reg.ID=data.user_id;
//                    alert(gid);
//                    console.log(data);
                    window.location.href='/games?gid='+gid+'&uid='+data.uid+'&hu=1#/game-end';
            });

        }
        else {
            if(!emailExists)
                $scope.error="Please fill all the fileds!";
        }
    };
    
    
    $scope.signInEndGame = function() {
        var si_data={
            email:$scope.reg.email,
            pass:$scope.reg.password
        };
        
        $http.post('/op',{op:"signInGame",si_data:si_data})
            .success(function(data, status, headers, config) {
                $scope.reg.ID=data.user_id;

                    //console.log(data);
                if(data.status=="ok") {
                    window.location.href='/games?gid='+gid+'&uid='+data.uid+'&hu=1#/game-end';
                }
                else {
                    alert("Login error!");
                }
        });
    }
    
    
    

});

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
         for (var i = (start || 0), j = this.length; i < j; i++) {
             if (this[i] === obj) { return i; }
         }
         return -1;
    }
}

function count(a) {
    var c=0;
    for(i in a) {
        c++;
    }
    return c;
}

////// user image functionality///////
var uploaderElement;
$(document).ready(function() {

});

function setUserFunc() {
    return;
    $(".user_image_func").css("cursor","pointer");

    $(".user_image_func").click(function(){
            uploaderElement=$('<div></div>').addClass('userImageUploader');
            uploaderElement.css({"width":"360px","height":"200px","background-color":"#ffffff","position":"fixed","z-index":"20000","top":"50%","left":"50%","margin-top":"-100px","margin-left":"-180px","border":"2px solid #999999","-webkit-border-radius": "4px","border-radius": "4px"});
            uploaderElement.html('<iframe style="width:360px;height:150px;border:0px;" src="http://my.wheeldo.com/userUploaderImage/'+user_hid+'"></iframe><div style="width:100%;height:50px;"><button onclick="closeUploader()" style="float:right;margin-top:10px;margin-right:20px;" type="button">Close</button></div>');
            $("body").append(uploaderElement);
    });
}

function closeUploader() {
    uploaderElement.remove();
}
/////////////////////////////////////


onFrmaeLoad = function() {
    var ret = frames[$("#file_upload_form").attr("target")].document.getElementsByTagName("body")[0].innerHTML;
    //console.log(ret);
    var scope = angular.element(document.getElementById("story_scope")).scope();
    //console.log(scope);

    $("#browse_post").show();
    $("#loader").hide();
    scope.$apply(function() {
        scope.new_tag.picture = ret;

    });
//    scope.safeApply(function(){
//        scope.new_tag.picture = ret;
//    })


}


Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
}

makeDataReadyToSend = function(editData) {
    var editDataJson=JSON.stringify(editData);
    editDataJson=editDataJson.replace(/'/g,"\\\"");
    editDataJson=editDataJson.replace(/&/g,"___amp___");
    return editDataJson;
};