var loadded_parts={
    dictionary:false,
    question:false
};

var BASE="/games/logic/quiz";
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
        } catch(ex) { /* nothing */ }
        
        
        // if user on mobile device, hide all frame data:
        try {
            if(ug != 1) {
                if(window.parent && typeof window.parent.hideFrameData == 'function')
                        window.parent.hideFrameData();
            }
        } catch(ex) { /* nothing */ }

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


        var animate_part;
        $(".fill").animate({
                width: total_w+"%"
            }, {
                duration: comp*1000,
                specialEasing: {
                    width: "linear",
                    height: "easeOutBounce"
                },
                complete: function() {

                }
        });

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
        loadQuestions();
        loadQuestions();
        getDictionary("en");
    };


    loadGameProgress = function() {
        $http.post(BASE+'/gt',{op:"loadGameProgress"})
            .success(function(data, status, headers, config) {
                if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.game_progress=data.game_progress;
                    checkReg();
                }
                else {
                    $scope.$apply(function () {
                        $scope.game_progress=data.game_progress;
                        checkReg();
                    });
                }
        });
    }

    $scope.gameInit = function() {

        loadGameProgress();
        loadData();
        $scope.timer=61;
        stopClock=false;
        tick();



    };





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
       setTimeout(nextQ,1000);
    };

    setWrong = function(ans_id,right_id) {
        $(".option").removeClass("correct");
        $(".option").removeClass("wrong");
        $(".option").removeClass("correct_wrong");

        $("#ans_"+ans_id).addClass("wrong");
        $("#ans_"+right_id).addClass("correct_wrong");





        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.game_quiz_user.strikes++;
        }
        else {
            $scope.$apply(function () {
                $scope.game_quiz_user.strikes++;
            });
        }

        setTimeout(nextQ,1000);
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
    }

    nextQ = function() {

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

        }

    };

    $scope.show_voucher=false;
    $scope.voucher_name=voucher_name;
    $scope.gameEndInit = function() {
        
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


              if(voucher) {
                  $scope.show_voucher=true;
              }


        });

    };

    $scope.timer=61;

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
        }
        else {
            $scope.$apply(function () {
                $scope.timer--;
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
                if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.score=data.score;
                }
                else {
                    $scope.$apply(function () {
                        $scope.score=data.score;
                    });
                }
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