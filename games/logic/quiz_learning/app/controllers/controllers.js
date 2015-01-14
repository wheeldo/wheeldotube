var loadded_parts={
    dictionary:false,
    question:false
};

var BASE="/games/logic/quiz_learning";
var or_lang="en";

var shareGameController = function ($scope, $http,  $modalInstance, f_data) {
    
    $scope.frame_src="/share_game/"+f_data.gid+"/"+f_data.result_text;
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
                    
                    
                    if(!f_data.callbackE) {
                        window.parent.location.reload();
                    }

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


app.controller('QuizController', function ($scope,$http,$cookies,$routeParams,$location,$modal,$templateCache,wheeldo_app,Report) {
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

	$scope.callAction = function() {
            window.open($scope.cta_link, "_blank");
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
    };

    

    $scope.show_voucher=false;
    $scope.voucher_name=voucher_name;

    $scope.skipReg = function() {
        // skip report:
        Report.__skipRegClicked();
        ///////////////
        $cookies.skip_reg = gid;
        $location.path("game-end");
    };
    
    var favoriteCookie = $cookies.myFavorite;
    
   
   
   
   
   
   

   $scope.quiz_game_data=data.quiz_game_data;
   
   
   $scope.curr_question_c=1;
   $scope.curr_question={};
   
   
   $scope.inst_init = function() {
       
   };
   
   
   $scope.__go = function() {
        $location.path("/game");
   };

   var q_array;
   $scope.gameInit = function() {
       q_array=[];
       var c=0;
       for(i in data.quiz) {
           
           q_array[c]=data.quiz[i];
           q_array[c].q_id=i;
           c++;
       }
       startQ(0);
   };
   
   resetAns = function() {
       $(".answer").removeClass("right");
       $(".answer").removeClass("wrong");
   };
   
   $scope.nextQ = nextQ = function() {
        if($scope.curr_question_c>=q_array.length) {
            Report.__endGame();
            //console.log($cookies.skip_reg);
            var skip_reg=$cookies.skip_reg;
            //if(false) { //if(ug) {
            if(ug && results_signup==1 && (!skip_reg || (skip_reg && skip_reg!=gid))) {
                $location.path("reg");
            }
            else {
                $location.path("/game-end");
            }
            
            return;
        }
        $scope.curr_question_c++;
        startQ($scope.curr_question_c-1); 
   };
   
   startQ = function(index) {
        var pr=Math.ceil((index+1)/q_array.length*100);
        if(pr>100)
            pr=100;
        $(".progress_top").css("width",pr+"%");
        
       
        $(".blur").hide();
        $(".extra_data").hide();
        resetAns();
        $scope.curr_question=q_array[index];
        
        // question type:
        $scope.question_type="";
        var media_ans=false;
        for(i in $scope.curr_question.answers) {
            if($scope.curr_question.answers[i].media) {
                media_ans=true;
            }
            break;
        }
        if(media_ans) {
            $scope.question_type="ans_image";
        }
        else if($scope.curr_question.media!=="") {
            $scope.question_type="q_image";
        }
        else {
            $scope.question_type="textual";
        }
        
        
        
        if($scope.$$phase || $scope.$root.$$phase) {/*1*/} else {$scope.$apply(function (){/*2*/});}
   };
   
   showMoreText = function() {
       $(".blur").fadeIn();
       $(".extra_data").fadeIn();
   };
   
   $scope.setAns = function(ans_id) {
       var right_Ans=parseInt($scope.curr_question.right);
       var correct;
       $(".answer_"+right_Ans).addClass("right");
       if(right_Ans===parseInt(ans_id)) {
           correct=1;
       }
       else {
           correct=0;
           $(".answer_"+ans_id).addClass("wrong");
       }
       
       
       $http.post(BASE+'/gt',{
           op:"saveAns",
           q_id:$scope.curr_question.q_id,
           ans_id:ans_id,
           correct:correct
       });
       
       var wait=2000;
       if($scope.curr_question.more_text) {
           wait=1000;
       }
       
       setTimeout(function(){
          if($scope.curr_question.more_text) {
              showMoreText();
          }
          else {
              nextQ();
          }   
       },wait);
   };
   
   
   $scope.gameEndInit = function() {
       
       $scope.share_button=share_button;
        $scope.CTA_button=CTA_button;
       
        $scope.cta_text=call_action_text;
        $scope.cta_link=call_action_link;
        var skip_reg=$cookies.skip_reg;
     //if(false) { //if(ug) {
     if(ug && results_signup==1 && (!skip_reg || (skip_reg && skip_reg!=gid))) {
         $location.path("reg");
         //$scope.register();
     }
        
       $http.post('/gt',{op:"loadGameCallToAction"}).success(function(data, status, headers, config){
                $scope.gameDetails = data;
      });

      if(voucher) {
          $scope.show_voucher=true;
      }
       
        $http.post(BASE+'/gt',{op:"gameEndData"}).success(function(response, status, headers, config) {
            
            $scope.end_data=response;
            
            $scope.result_text='I answered '+response.right+' out of '+response.total+' questions correctly at ';
            
            $scope.questions_data=data.quiz;
            
            $scope.read_more={};
            
            
            setTimeout(function(){$(".calculating").css({"width":"30%","background-color":"#E02A7B"});},50);
            setTimeout(function(){$(".calculating").css({"width":"80%","background-color":"#399CDB"});},650);
            setTimeout(function(){$(".calculating").css({"width":"100%","background-color":"#A9C82F"});},1800);
            
            setTimeout(function(){
                $scope.ready=true;
                if($scope.$$phase || $scope.$root.$$phase) {/*1*/} else {$scope.$apply(function (){/*2*/});}
            },2600);
            
        });
   };
   
   $scope.skipReg = function() {
        // skip report:
        Report.__skipRegClicked();
        ///////////////
        
        
        $cookies.skip_reg = gid;
        $location.path("game-end");
    };
    
    var favoriteCookie = $cookies.myFavorite;
   
   $scope.regInit = function() {
        if(signup_headline) {
            $scope.signup_headline=signup_headline;
        }
        
        if(!ug)
            $location.path("game-end");
    };
   
   $scope.shareGame = function(){
      var t=new Date().getTime();
        var modalInstance = $modal.open({
            templateUrl: BASE+'/app/partials/includes/share_game.html?t='+t,
            keyboard:true,
            //backdrop:'static',
            windowClass: 'sharePopUp',
            controller: shareGameController,
            resolve: {
                f_data:function () {
                  return {
                      gid:gid,
                      result_text:$scope.result_text
                  };
                }
            }
        }); 
    };
   
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