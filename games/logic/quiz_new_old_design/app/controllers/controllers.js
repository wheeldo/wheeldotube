var loadded_parts={
    dictionary:false,
    question:false
};

var BASE="/games/logic/quiz_new";
var or_lang="en";

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
                    window.parent.location.reload();

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


app.controller('QuizController', function ($scope,$http,$routeParams,$location,$modal,$templateCache,wheeldo_app) {
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



    }

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

    }

    $scope.test_animate = function() {
//        alert(1)
        var t=new Date().getTime();



        var new_img='games/logic/quiz_new/media/img/strike_animate.gif?t='+t;


         $(".strike_test").css({
            'background-image':"url('"+new_img+"')"
        });

    }
    
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



    $scope.sendVaucher = function() {
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
    }



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


    $scope.register = function() {

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: BASE+'/app/partials/includes/registration.html?t='+t,
          keyboard:false,
          backdrop:'static',
          windowClass: 'registrationPopUp',
          controller: RegController,
          resolve: {
              f_data:function () {
                return 0;
              }
          }
        });

        modalInstance.result.then(function (reg_data) {

            $scope.user={
                ID:reg_data.ID,
                name:reg_data.fname+" "+reg_data.lname
            };

            ug=0;
        }, function () {

        });
    };

    //$scope.register();


    $scope.user_image=user_image;
    $scope.user=USER;


    $scope.ready=false;
    $scope.t=new Date().getTime();

    $scope.game_quiz_user=game_quiz_user;
    $scope.banner=banner;



    //console.log(loadded_parts);


    $scope.__go = function() {
    	$location.path("game");

    	// if user is logged in, try to automatically add him to this game's channel
    	try {
	        if(ug != 1) {
	                if(window.parent && typeof window.parent.gameChannelSubscribe == 'function')
	                        window.parent.gameChannelSubscribe();
	        }
        } catch(ex) { console.log(ex); }


        // if user on mobile device, hide all frame data:
        try {
            if(ug != 1) {
                if(window.parent && typeof window.parent.hideFrameData == 'function')
                        window.parent.hideFrameData();
            }
        } catch(ex) { console.log(ex); }

    };


    setLoader = function() {
        //$scope.loader_width="0%";
        var total_w=0;
        var c=0;
        var comp=0;
        for(i in loadded_parts) {
            if(loadded_parts[i])
                comp++;
            c++;
        }
        total_w=100*(comp/c);

        if(total_w===100) {
            setTimeout(function(){
                $(".loading").fadeOut();
            },3500);
        }


//        var animate_part;
//        $(".fill").animate({
//                width: total_w+"%"
//            }, {
//                duration: comp*1000,
//                specialEasing: {
//                    width: "linear",
//                    height: "easeOutBounce"
//                },
//                complete: function() {
//
//                }
//        });

    };

    init = function() {
        setLoader();
    };
    init();


    $scope.userFunc = function() {
        setUserFunc();
    };

    $scope.inst_init = function() {
      if(ug==1 && open_status==2) {
          $scope.register();
      }

      loadData();
    };

    var a_t=60;

    $scope.setAns = function(ans_id,q_id) {
        if(ansBlock)
            return;


        if($("#ans_"+ans_id).hasClass("disabled"))
            return;
        $(".option").removeClass("normal");
        ansBlock=true;
        stopClock=true;
        checkAnsService(ans_id,q_id,$scope.timer);

    };

    loadData = function() {
        $http.post(BASE+'/gt',{op:"loadQuestions"})
            .success(function(data, status, headers, config) {
                $scope.questions=data;
                if($scope.questions.length===0 || game_quiz_user.strikes>=3) {
                    goEnd();

                }
                loadded_parts.question=true;
                setLoader();
            });

        getDictionary("en");
    };



    loadGameProgress = function() {
        $http.post(BASE+'/gt',{op:"loadGameProgress"})
            .success(function(data, status, headers, config) {
                $scope.game_progress=data.game_progress;
                checkReg();


                $http.post(BASE+'/gt',{op:"getScore"})
                    .success(function(data, status, headers, config) {
                        $scope.score=data.score;
                        setScoreBar();
                });

        });
    }

    $scope.gameInit = function() {
        loadGameProgress();
        loadData();

        $scope.timer=61;
        stopClock=false;
        tick();

	if(__isMobile) {
	    //set_game_elements_display();
	    $scope.is_mobile = 1;
	}



    };

    set_game_elements_display = function () {
	//set the display of misc element EHILE PLAYING A GAME 1
	//alert('set_game_elements_display');
		if(__isMobile) {
		    alert('mobilexxx');
		    $("#mobile_top_screen").addClass("hide_me");
		    $("#mobile_footer").addClass("hide_me");
		    $(".lifes").addClass("hide_me");
		    $("#mobile_top_screen").hide();
		}
		xxx = $(".hide_me");

    }

    setScoreBar = function() {
        var max_score=$scope.game_progress.total*60;

        var h=Math.ceil(($scope.score/max_score)*100);

        $scope.score_bar_style = {
            "height":h+"%",
            width:"40px"
        };
    }



    goEnd = function() {
            if($scope.$$phase || $scope.$root.$$phase) {
                    $location.path("game-end");
            }
            else {
                $scope.$apply(function () {
                    $location.path("game-end");
                });
            }
    };

    var ansBlock=false;
    setRight = function(ans_id) {
        $(".option").removeClass("correct");
        $(".option").removeClass("wrong");
        $(".option").removeClass("correct_wrong");
        $("#ans_"+ans_id).addClass("correct");
        setScoreBar();
       setTimeout(nextQ,2000);
    };

    setWrong = function(ans_id,right_id) {

        //ainmateNewStrike();
        //$(".strike_animation").show();

        $(".option").removeClass("correct");
        $(".option").removeClass("wrong");
        $(".option").removeClass("correct_wrong");

        $("#ans_"+ans_id).addClass("wrong");
        $("#ans_"+right_id).addClass("correct_wrong");




        setTimeout(function(){
            if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.game_quiz_user.strikes++;
            }
            else {
                $scope.$apply(function () {
                    $scope.game_quiz_user.strikes++;
                });
            }
        },1000)



        setTimeout(nextQ,1500);
    };


    ainmateNewStrike = function() {
        var strike_an=$scope.game_quiz_user.strikes;
        var t=new Date().getTime();



//        var new_img='/games/logic/quiz_new/media/img/strike_animate_'+strike_an+'.gif?t='+t;
//        if(__isMobile) {
//            new_img='/games/logic/quiz_new/media/img/strike_animate_mobile_'+strike_an+'.gif?t='+t;
//        }


//        $(".strikes .strike.egg.animate").css({
//            'background-image':"url('"+new_img+"')"
//        });

        $scope.strike_animate[strike_an]=true;
    }


    checkReg = function() {
        if($scope.game_progress.answerd>=3 && ug==1 && open_status==1) {
            //alert("now!!!")


            if($scope.$$phase || $scope.$root.$$phase) {
                    $location.path("/instructions");
                    $scope.register();
            }
            else {
                $scope.$apply(function () {
                    $location.path("/instructions");
                    $scope.register();
                });
            }


            return true;
        }

        return false;
    }

    $scope.strike_animate=[0,0,0];

    nextQ = function() {
        setScoreBar();
        //$(".strike_animation").hide();

        $scope.strike_animate[0]=false;
        $scope.strike_animate[1]=false;
        $scope.strike_animate[2]=false;

        if(checkReg())
            return;


        ansBlock=false;
        $(".option").removeClass("correct");
        $(".option").removeClass("wrong");
        $(".option").removeClass("correct_wrong");
        $(".option").removeClass("disabled");


        if($scope.game_quiz_user.strikes>=3) {
            if($scope.$$phase || $scope.$root.$$phase) {
                    $location.path("game-end");
            }
            else {
                $scope.$apply(function () {
                    $location.path("game-end");
                });
            }
            return false;
        }
        $scope.timer=61;
        stopClock=false;
        tick();
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.questions.splice(0,1);
                $scope.game_progress.answerd++;
        }
        else {
            $scope.$apply(function () {
                $scope.questions.splice(0,1);
                $scope.game_progress.answerd++;
            });
        }
        if($scope.questions.length===0) {
            if($scope.$$phase || $scope.$root.$$phase) {
                    $location.path("game-end");
            }
            else {
                $scope.$apply(function () {
                    $location.path("game-end");
                });
            }
			return false;
        }
    };

    $scope.show_voucher=false;
    $scope.voucher_name=voucher_name;
    $scope.gameEndInit = function() {
        
        
        //if(false) { //if(ug) {
        if(ug) {
            $location.path("reg");
            //$scope.register();
        }
        else {
            
            // if user on mobile device, hide all frame data:
            try {
                if(ug != 1) {
                    if(window.parent && typeof window.parent.showFrameData == 'function')
                            window.parent.showFrameData();
                }
            } catch(ex) { /* nothing */ }


            $scope.show_voucher=false;
            $http.post(BASE+'/gt',{op:"loadGameEnd"})
            .success(function(data, status, headers, config) {
                  $scope.end_data=data;

                  // main getter - get the call to action link, text and button text
                  $http.post('/gt',{op:"loadGameCallToAction"}).success(function(data, status, headers, config){
                            $scope.gameDetails = data;
                  });

                  if(voucher) {
                      $scope.show_voucher=true;
                  }


            });

        }
    };
    
    
    $scope.regInit = function() {
        if(!ug)
            $location.path("game-end");
    }

    $scope.timer=61;


    $scope.time_bar_style={'height':"0%"};

    var stopClock=true;
    tick = function() {

        if($scope.timer<=0) {
            burnQ(true);
            return;
        }

        if(stopClock)
            return;
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.timer--;

                var h=100-$scope.timer*1.666666666666666666667;
                $scope.time_bar_style={'height':h+"%",'width':"40px"};
        }
        else {
            $scope.$apply(function () {
                $scope.timer--;
                var h=100-$scope.timer*1.666666666666666666667;
                $scope.time_bar_style={'height':h+"%",'width':"40px"};
            });
        }

        setTimeout(tick,1000);


    };


    $scope.setLife = function(type) {
        switch(type){
            case "use_5050":
                get5050Service($scope.questions[0].q_id);
            break;
            case "use_plus_60":
                add60Service();
            break;
            case "use_skip":
                skipService();
            break;
        }
    };


    add60Service = function() {
        $http.post(BASE+'/gt',{op:"useAdd60",q_id:$scope.questions[0].q_id})
            .success(function(data, status, headers, config) {
                if($scope.$$phase || $scope.$root.$$phase) {
                    if(data.status=="ok") {
                        $scope.game_quiz_user.use_plus_60=1;
                        $scope.timer=$scope.timer+30;
                    }
                    else {
                       $scope.game_quiz_user.use_plus_60=1;
                    }
                }
                else {
                    $scope.$apply(function () {
                        if(data.status=="ok") {
                            $scope.game_quiz_user.use_plus_60=1;
                            $scope.timer=$scope.timer+30;
                        }
                        else {
                           $scope.game_quiz_user.use_plus_60=1;
                        }
                    });
                }
        });
    }

    skipService = function() {

        $http.post(BASE+'/gt',{op:"useSkip",q_id:$scope.questions[0].q_id})
            .success(function(data, status, headers, config) {
                if($scope.$$phase || $scope.$root.$$phase) {
                    if(data.status=="ok") {
                        ansBlock=true;
                        stopClock=true;
                        $scope.game_quiz_user.use_skip=1;
                        nextQ();
                    }
                    else {

                    }
                }
                else {
                    $scope.$apply(function () {
                        ansBlock=true;
                        stopClock=true;
                        $scope.game_quiz_user.use_skip=1;
                        nextQ();
                    });
                }
        });
    }


    $scope.loadScore = function() {
        $http.post(BASE+'/gt',{op:"getScore"})
            .success(function(data, status, headers, config) {
                $scope.score=data.score;
                console.log($scope.score);
        });
    }


    burnQ = function(strike) {
        if(strike) {
            $scope.game_quiz_user.strikes++;
        }


        $http.post(BASE+'/gt',{op:"burnQ",q_id:$scope.questions[0].q_id});
        nextQ();
    };


    checkAnsService = function(ans_id,q_id,a_t) {


        $http.post(BASE+'/gt',{
                                op:"checkAns",
                                ans_id:ans_id,
                                q_id:q_id,
                                a_t:a_t
                              })
       .success(function(data, status, headers, config) {
                    if(data.status==="ok") {
                        if(data.answer==="right")
                            setRight(ans_id);
                        else
                            setWrong(ans_id,data.right_answer);
                    }

                    $scope.loadScore();
        });

    };


    $scope.getRecommendedGame = function() {
        $http.post('/op',{op:"getRecommendedGame",gid:gid})
        .success(function(data, status, headers, config) {
                $scope.recommended_game=data;
        });
    };

    $scope.loadAnotherGame = function(gid) {
        if(window.parent && typeof window.parent.loadAnotherGame == 'function')
                window.parent.loadAnotherGame(gid);
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