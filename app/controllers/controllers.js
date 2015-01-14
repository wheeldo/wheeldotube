
var files_checker=[];
app.controller('TubeController', function ($scope,$http,$routeParams,$modal,$location,$templateCache,Services,$navigate,Usertrack,Gamereport,uploaderWLD) {
    
    // user track events:
    $scope.__event = function(event,event_id) {
        Usertrack.__event(event,event_id);
    };
    /////////////////////
    
    
    $scope.WLD_uploadFile = function(user_id) {
        uploaderWLD.__upload(user_id);
    };
    
    $scope.WLD_chooseFile = function(form_name) {
        uploaderWLD.__choose_file(form_name);
    };
    
    $scope.deleteLibrary = function(index) {
      var thumb=$scope.library_thumbnail[index];  
        
      if(!confirm("Delete '"+thumb.name+"' from '"+thumb.date+"'?"))
          return;
      
      
      uploaderWLD.__deleteThumb(thumb.id);
      
    };
    
    
    // uploader functions:
    var uploader_image_obj={};
    $scope.upload_file = function(obj,mimes,image_w,image_h) {
        image_w=typeof(image_w) !== "undefined" ? image_w : 2;
        image_h=typeof(image_h) !== "undefined" ? image_h : 2;

        uploader_image_obj={
            scope_obj:obj,
            w:image_w,
            h:image_h
        };
        
        $scope.library_choose={
            w:image_w,
            h:image_h
        };
        
        uploaderWLD.__init();
        $("#file_uploader_popup_blur").fadeIn();
        $("#file_uploader_popup").fadeIn();
        
        $("#file_uploader_popup_blur").click(function(){
            $scope.close_upload_file();
        });
    };
    
    $scope.close_upload_file = function() {
        $("#file_uploader_popup_blur").fadeOut("fast");
        $("#file_uploader_popup").fadeOut("fast");
    };
    
    $scope.selectLibraryImage = function(index) {
        $(".library_thumbnail").removeClass("selected");
        $(".thumb_"+index).addClass("selected");
        //scope_image_on_grill=$scope.library_thumbnail[index].url;
        var img_url=$scope.library_thumbnail[index].url;
        if(uploader_image_obj.w) {
            var ex1=$scope.library_thumbnail[index].url.split('upload/');
            var e="c_fill,h_"+uploader_image_obj.h+",w_"+uploader_image_obj.w;
            img_url=ex1.join("upload/"+e+"/"); 
        }
        applyImageToObject(uploader_image_obj.scope_obj,img_url);
        $scope.close_upload_file();
        
        
    };
    /////////////////////////////

    $scope.t=new Date().getTime();

    $scope.$navigate = $navigate;
    $scope.sm_toggle = function() {
       if(!$scope.user_login) {
           return;
       }

      $("body").toggleClass("sm_open");
      $http.post('/op',{op:"setSMOpen",val:$("body").hasClass("sm_open")});
      $scope.fixLayout();
    };

    // slides:
    $scope.switch_time=500;
    $scope.wait_time=6000;

    // slides mobile:
    var m_curr_image=0;
    var slides=[
      "/media/img/hp_banner/0_mobile.jpg",
      "/media/img/hp_banner/1_mobile.jpg",
      "/media/img/hp_banner/2_mobile.jpg",
      "/media/img/hp_banner/3_mobile.jpg",
      "/media/img/hp_banner/4_mobile.jpg"
    ];

    $scope.slide_hp_mobile_init = function() {

        setTimeout(function(){
            replaceMobileImage();
        },$scope.wait_time);



    }

    replaceMobileImage = function() {
        m_curr_image++;
        if(slides.length<=m_curr_image)
            m_curr_image=0;
        $('.banner_1_mobile').fadeTo('slow', 0.3, function() {
            $(this).css('background-image', 'url(' + slides[m_curr_image] + ')');
        }).fadeTo('slow', 1);

        setTimeout(function(){
            replaceMobileImage();
        },$scope.wait_time);
    }




    var g1=[
        {
            obj:".game_0",
            type:"highlight",
            html:'<h2>Your first game is ready!</h2><p>Click the game image or the title to play!</p>',
            pointed_objects:[{obj:".game_0 .game_name",or:"left"},{obj:".game_0 .thumbnail_wrap",or:"bottom"}]
        },
        {
            obj:".game_0 .actions",
            type:"inhighlight",
            html:'<h2>Edit the game here</h2><p>Not happy with how your game turned out?  Edit it here</p>',
            pointed_objects:[{obj:".game_0 .icon_edit",or:"left"}]
        },
        {
            obj:".create_new_game",
            html:'<h2>Create New Games Here</h2>',
            pointed_objects:[{obj:".create_new_game",or:"right"}]
        }
        
    ];
    
    var guide_active;
    $scope.set_guidance = function(guide){
        
        $http.post('/op',{op:"checkGuide",guide:guide})
        .success(function(data, status, headers, config) {
                if(data.status=="ok") {
                    guide_active=guide;
                    setTimeout(function(){
                    startGuide(g1);
                },200);
            }
        });    
    };
    
    var guideC=0;
    var guideArr=[];
    startGuide = function(arr) {
        guideC=0;
        guideArr=arr;
        showGuide();
        
    };
    
    showGuide = function() {
        $(".guidance_text").remove();
        var selector=guideArr[guideC].obj;
        var obj=$(selector);
        highlightObject(selector);
        
        var screenH=screen.height;
        var offset=obj.offset();
        var h=obj.outerHeight();
        
        var text_top=offset.top+h+30;
        
        var next_btn='<button class="next_back next_btn">Next</button>';
        var gotIt_btn='';//<button class="gotit">Got It!</button></div>';
        
        if(guideC>=guideArr.length-1) {
            next_btn='<button class="next_back finish_btn">Finish</button>';
            gotIt_btn='';
        }
        
        
        var text_offset={left:offset.left,top:text_top};
        
        if($(window).width()-text_offset.left<350) {
            text_offset.top=offset.top;
            text_offset.left=offset.left-400;
        }
        
        var d='<div class="guidance_text" style="max-width:350px;left:'+text_offset.left+'px;top:'+text_offset.top+'px"><div>'+
                guideArr[guideC].html+
                '</div>'+
                '<div>'+
                next_btn+
                gotIt_btn+
                '</div>';
        $('body').append(d);
        
        if(true) {
            var h='<div class="guidance_text" style="font-size:14px;position:absolute;top:20px;width:100%;text-align:center;height:60px;color:#ffffff;z-index:100010;">'+
                    '<h1 style="width:100%;text-decoration:underline;text-align:center;font-size:30px;color:#ffffff;">Tour Guide <span style=font-size:14px;><a style="color:#ffffff;background-color:transparent;border:0;" class="gotit" href="javascript:void(0)">not now</a></span></h1>'+
                    '<div style="margin:0;font-family:Arial;margin-top:0px;">Step '+(guideC+1)+' out of '+guideArr.length+'</div>' +
                  '</div>';
            $('body').append(h);
        }
        
        
        for(i in guideArr[guideC].pointed_objects) {
            var obg_or=guideArr[guideC].pointed_objects[i];
            if(!isFunction(obg_or)) {
                var obj=obg_or.obj;
                var or=obg_or.or;
                
                var off=$(obj).offset();
                
                var arr_width=30;
                var arr_height=55;
                
                var h=$(obj).outerHeight();
                var w=$(obj).outerWidth();
                var l,t;
                switch(or) {
                    case "left":
                        l=off.left-arr_height;
                        t=off.top+(h/2)-15;;
                    break;
                    case "right":
                        l=off.left+w;
                        t=off.top+(h/2)-15;;
                    break;
                    case "bottom":
                        l=off.left+(w/2)-15;
                        t=off.top+h;
                    break;
                }

                

                var arr_img='<img class="guidance_text" style="z-index:100010;position:absolute;top:'+t+'px;left:'+l+'px" src="/media/img/'+or+'_arr.png" />';
                $('body').append(arr_img);
            }
        }
        
        $(".next_btn").click(function(){
            guideC++;
            showGuide();
        });
        
        $(".gotit").click(function(){
            hideGuide();
            $http.post('/op',{op:"setGuide",guide:guide_active});
        });
        
        
        
        $(".finish_btn").click(function(){
            hideGuide();
            $http.post('/op',{op:"setGuide",guide:guide_active});
        });

        $(".guidance_text").fadeIn();
    };
    
    
    hideGuide = function() {
        $(".guidance_highlighted").remove();
        $(".guidance_text").remove();
    };
    
    
    highlightObject = function(selector) {
        
        $(".guidance_highlighted").remove();
        $('body').removeClass('stop-scrolling');
        
        var obj=$(selector);
        var offset=obj.offset();
        var h=obj.outerHeight();
        var w=obj.outerWidth();
        
        var screenW=screen.width;
        var screenH=screen.height;

        
        var strips=[
            {top:0,left:0,height:offset.top-5,width:'100%'},
            {top:offset.top-5,left:0,height:h+10,width:offset.left-10+'px'},
            {top:offset.top-5,left:offset.left+w+10,height:h+10,width:screenW-(offset.left+w)+'px'},
            {top:offset.top+h+5,left:0,height:screenH-(offset.top+h),width:'100%'}
        ];
        
        
        for(i in strips) {
            var s=strips[i];
            var d='<div class="guidance_highlighted" style="top:'+s.top+'px;left:'+s.left+'px;height:'+s.height+'px;width:'+s.width+';">&nbsp;</div>';
            $('body').append(d);
        }
        
        
        var d='<div class="guidance_highlighted highlighted" style="top:'+(offset.top-5)+'px;left:'+(offset.left-10)+'px;height:'+(h+10)+'px;width:'+(w+20)+'px;">&nbsp;</div>';
        $('body').append(d);
        
        
        $(".guidance_highlighted").fadeIn();
        $('body').addClass('stop-scrolling');
        
        //console.log(strips);
        //obj.addClass("guidance_highlighted");
        
    }


    /////////////////

    //////////////// slides:
    $scope.banner_slides=[];


    $scope.addSlide = function(image) {
        $scope.banner_slides.push({
            image:image
        });
    }

    for (var i=0; i<5; i++) {
      $scope.addSlide("/media/img/hp_banner/"+i+".jpg");
    }

    var slider_timeout;
    $scope.slide_hp_init = function() {
        clearTimeout(slider_timeout);
        slider_timeout=setTimeout(function(){
            run_slides();
        },$scope.wait_time);



    }
    var curr_slide=0;
    var slider_timeout2;
    run_slides = function() {
        clearTimeout(slider_timeout2);
        var out_slide=curr_slide;
        curr_slide++;
        if(curr_slide>=$scope.banner_slides.length)
            curr_slide=0;

            $(".in_start").css({
               "background-image":'url("'+$scope.banner_slides[curr_slide].image+'")'
            });
            $(".going_out").addClass("out");
            $(".in_start").addClass("in");

        setTimeout(function(){
            $(".going_out").remove();
            $(".banner_img").removeClass("in_start");
            $(".banner_img").removeClass("in");

            $(".banner_img").addClass("going_out");

            $(".banners").prepend('<div class="banner_img in_start"></div>');

            slider_timeout2 = setTimeout(function(){
                run_slides();
            },$scope.wait_time-200);
        },$scope.switch_time)
    };





    ///////////////////////


    $scope.fixLayout = function() {
        var m_w=$(document).width();
        var m_h=$('html').height();
        fixLayout(m_w,m_h);
    };

    $scope.create_your_game_now = function() {
        if($scope.user_login)
            $location.path("/createGame");
        else {
            $scope.join_popup();
            //$scope.sign_in_popup();
        }

    };


    $scope.logOut = function() {
       $("body").removeClass("sm_open");
       $location.path("/signOut");
    };

    $scope.changePassword = function() {
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/change_password_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: changePasswordController,
          resolve: {
              f_data:function () {
                return {
                    user:$scope.user
                }
              }
          }
        });

        modalInstance.result.then(function (status) {

        }, function () {

        });
    };

    $scope.get_color_box = function(id) {
        var no=id%10;

        switch(no){
            case 0:
            case 3:
            case 6:
            case 9:
                return 1;
            break;
            case 1:
            case 4:
            case 7:
                return 2;
            break;
            case 2:
            case 5:
            case 8:
                return 3;
            break;
        }
    };



    //var path=
    $scope.path=$location.path();


    $scope.addAnswer = function(q_index) {
        $scope.new_game.quiz_data[q_index].answers.push({text:""});
    }
    
    $scope.addAnswerTestYourself = function(q_index) {
        $scope.new_game.test_yourself_data.questions[q_index].answers.push({text:"",media:"",strength:0});
    }


    $scope.removeAns = function(ans_index,q_index) {
        $scope.new_game.quiz_data[q_index].answers.splice(ans_index,1);
    }
    
    $scope.removeAnsTestYourself = function(ans_index,q_index) {
        $scope.new_game.test_yourself_data.questions[q_index].answers.splice(ans_index,1);
    }

    $scope.addNewQuestion = function() {
        var q_obj={
            question:"",
            rank:50,
            answer:1,
            answers:[
                {text:""},
                {text:""},
                {text:""}
            ]
        };
        $scope.new_game.quiz_data.push(q_obj);
        $scope.setActiveQ($scope.new_game.quiz_data.length-1);

        checkWhatArrowToDisplay();
    };
    
    $scope.addNewQuestionTestYourself = function() {
        var q_obj={
            text:"",
            media:"",
            answers:[
                {text:"",media:"",strength:0},
                {text:"",media:"",strength:0},
                {text:"",media:"",strength:0},
                {text:"",media:"",strength:0}
            ]
        };
        
        $scope.new_game.test_yourself_data.questions.push(q_obj);
        $scope.setActiveQ($scope.new_game.test_yourself_data.questions.length-1);

        checkWhatArrowToDisplay();
    };


    $scope.setChannelTab = function(tab) {
        $(".channel_menu li").removeClass("active");
        $(".channel_tab").removeClass("active");
        $("."+tab).addClass("active");
        $(".channel_menu li[tab_ac="+tab+"]").addClass("active");
    };

    $scope.edit_h_field = function(id) {
        $("#"+id).unbind("blur");
        $("#"+id).show();
        $("#"+id).focus();
        $("."+id).hide();

        $("#"+id).blur(function(){
            $("#"+id).hide();
            $("."+id).show();
            saveUserService(makeDataReadyToSend($scope.user));
        });
    };

    getMyChannel =function(cid) {
        for(i in $scope.user.my_channels) {
            if($scope.user.my_channels[i].unique_id===cid)
                return $scope.user.my_channels[i];

        }
        return 0;
    };

    $scope.channelInit = function() {
        getChannelService($routeParams.cid);


        $http.post('/op',{op:"checkSubscribe",cid:$routeParams.cid})
        .success(function(data, status, headers, config) {
            if(data.subscribe==1) {
                $scope.subscribe_text='Unsubscribe <img src="/media/css/dice/img/minus.png" />';
            }
            else {
                $scope.subscribe_text='Subscribe <img src="/media/css/dice/img/plus.png" />';
            }

        });
    }

    checkIfYourChannel = function(cid) {
        for(i in $scope.user.my_channels) {
            if(cid===$scope.user.my_channels[i].unique_id)
                return true;
        }

        return false;
    };

    $scope.myChannelInit = function() {
        var check=checkIfYourChannel($routeParams.cid);
        if(!check) {
            $location.path("/");
        }


        getChannelUsersService($routeParams.cid);


        $scope.channel_data=getMyChannel($routeParams.cid);

        $scope.uploadUrls.user_image=getImageUploadURLService();
        $scope.uploadUrls.user_image.res_mark=makeid();

        $scope.uploadUrls.channel_cover=getImageUploadURLService();
        $scope.uploadUrls.channel_cover.res_mark=makeid();

    };

    $scope.openImageUploader = function(popup_id) {
        $("."+popup_id).fadeToggle();
    };

    $('body').removeClass("white");
    $scope.user = getUserSrvice();

    // status:
    // 0 open
    // 1 premium
    // 2 comming soon

    var premium=$scope.user.premium==1?true:false;


    $scope.game_types=[
        {id:2,default_template:7,name:"Competition Trivia",status:0,selected:1},
        {id:3,default_template:8,name:"Personality Test",status:0,selected:0},
        {id:4,default_template:1,name:"Learning Quiz",status:premium?0:1,selected:0},
        {id:1,default_template:1,name:"Pro Quiz",status:premium?0:1,selected:0}
    ];

    $scope.user_login=false;
    if($scope.user.ghost!=="1") {
        $scope.user_login=true;
    };
    
    
    // redirect if not logged in:
    if(!$scope.user_login&&$location.path()=="/console")
        window.location.href="/";
    //////////////////////////////

    $scope.addUsersChannle = function() {
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/add_users_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'addUsersPopUp',
          controller: addUsersController,
          resolve: {
              f_data:function () {
                return {
                    cid:$routeParams.cid
                }
              }
          }
        });

        modalInstance.result.then(function (status) {
            if(status==1) {
                getChannelUsersService($routeParams.cid);
            }

        }, function () {

        });
    };

    $scope.absUrl=$location.absUrl();


    //$scope.toggle_user_data=true;


    $scope.search_query=$routeParams.search_query;

    $scope.page={
        title:"ttt"
    };


    init = function() {

    };
    init();



    // set page variables:
    var scope = angular.element(document.getElementById("head")).scope();
    scope.page={
        title:"Wheeldo - Home",
        description:"some description"
    };


    $scope.submitSearch = function() {
        if(!this.search_query)
            return false;

        $location.path("/results/"+this.search_query);
    };

    $scope.signIn = function() {
        $location.path("/signIn");
    };

    $scope.signInInit = function() {
      scope.page={
            title:"Wheeldo - Sign in",
            description:"some description"
      };
      $('body').addClass("white");
    };

    $scope.signInSubmit = function() {
        //signInUserService(this,$scope.notes);
        
        $http.post('/op',{op:"signInUser",email:this.email,password:this.password})
        .success(function(data, status, headers, config) {
            if(data.status=="ok") {
                $location.path("/console");
            }
            else {
                alert("Login faild!");
            }
        });
        
        
    };
    
    

    $scope.signClicked = function() {
        $(".sign_in_form").removeClass("cancel_error");
    };


    $scope.forgotPasswordInit = function() {
      scope.page={
            title:"Wheeldo - Forgot password",
            description:"some description"
      };
      $('body').addClass("white");
    };

    $scope.forgotPasswordSubmit = function() {
      if(this.email) {
            $(".submit").hide();
            $(".wait").show();


            $http.post('/op',{op:"forgotPassword",email:this.email})
            .success(function(data, status, headers, config) {
                if(data.status=="ok") {
                    $(".password_recovery").hide();
                    $(".done").show();
                }
                else {
                    $scope.notes=[];
                    $scope.notes.push(data.error);
                }


                $(".submit").show();
                $(".wait").hide();
            });


      }

    };


    $scope.addAccount = function() {
      $location.path("/AddAccount");
    };

    $scope.__goto = function(route) {
        $location.path("/"+route);
    };
    $scope.regClicked = function() {
        $(".reg_form").removeClass("cancel_error");
    };

    $scope.notes={};
    $scope.registerSubmit = function() {
        $scope.notes={};

        if(!this.password) {
            alert("Password must be at least 6 characters!");
            return;
        }

        if(this.password!==this.retype_password) {
            $scope.notes.error_01="Passwords don't match!";
            return;
        }



        regNewUserService(this);
    };

    $scope.checkIFEmailExits = function(email) {
      if(email)
        checkEmailService(email,$scope.notes);
    };


    $scope.activationInit = function() {
        $('body').addClass("white");
      setProcessing();
      var link=$routeParams.link;
      $http.post('/op',{op:"activateAccount",link:link})
        .success(function(data, status, headers, config) {
                    if(data.status=="ok")
                        stopProcessing();
                    else
                        stopProcessing();


                    setTimeout(function(){
                        window.location.href="/";
                    },1500);

        });


    };

    $scope.signOutInit = function() {
        //alert(1);
        $http.post('/op',{op:"signOut"})
        .success(function(data, status, headers, config) {
           window.location.href="/";
        });
    };


    $scope.clocks=[];
    $scope.clockTick = function(index,row) {
        if($scope[row][index].prize_time_limit>0) {
            $scope.clocks[index]={
                visual:"00:00:00",
                sec_left:parseInt($scope[row][index].prize_time_limit)
            }
            tick(index);
        }
        else {
            $scope.clocks[index]=null;
        }
    }


    tick = function(index) {
        sec_left=$scope.suggested[index].prize_time_limit;
        if(sec_left>0) {
            var h=Math.floor(sec_left/3600);
            sec_left-=h*3600;
            var m=Math.floor(sec_left/60);
            sec_left-=m*60;
            var s=sec_left;


            if(h<10)h="0"+h;
            if(m<10)m="0"+m;
            if(s<10)s="0"+s;


            if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.clocks[index].visual=h+":"+m+":"+s;
            }
            else {
                $scope.$apply(function () {
                    $scope.clocks[index].visual=h+":"+m+":"+s;
                });
            }

            $scope.suggested[index].prize_time_limit--;
            setTimeout(tick,1000,index);
        }
        else
            return;


    }





    $scope.load_suggested = function(c_to_hide) {
       $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"getSuggested"
                },
                success: function(data, textStatus, jqXHR) {
                    $("."+c_to_hide+"_loader").hide();
                    if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.suggested=data;
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.suggested=data;
                        });
                    }

                    //alert($scope.suggeted.length*180);

                    $("."+c_to_hide+"_loader_games_row_inner").css({
                        width:$scope[c_to_hide].length*260+"px"
                    })
                }
        });
    };


    $scope.load_suggested_channel = function(c_to_hide) {
       $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"getSuggestedChannel",
                  cid:$routeParams.cid
                },
                success: function(data, textStatus, jqXHR) {
                    $("."+c_to_hide+"_loader").hide();
                    if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.suggested=data;
                            $scope.loadChannelGame(0);
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.suggested=data;
                            $scope.loadChannelGame(0);
                        });
                    }

                    //alert($scope.suggeted.length*180);

                    $("."+c_to_hide+"_loader_games_row_inner").css({
                        width:$scope[c_to_hide].length*260+"px"
                    })
                }
        });
    };


    $scope.loadChannelGame = function(index) {
        var game=$scope.suggested[index];
        $scope.game_src="/games?gid="+game.unique_id+"&uid="+$scope.user.uid+"&hu=1";
        $scope.game={
            name:game.name,
            user_fname:game.user_fname,
            user_lname:game.user_lname,
            u_time:game.u_time

        }
    }

    $scope.load_popular = function(c_to_hide) {
       $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"getPopular"
                },
                success: function(data, textStatus, jqXHR) {
                    $("."+c_to_hide+"_loader").hide();
                    if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.popular=data;
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.popular=data;
                        });
                    }

                    //alert($scope.suggeted.length*180);

                    $("."+c_to_hide+"_loader_games_row_inner").css({
                        width:$scope[c_to_hide].length*260+"px"
                    })
                }
        });
    };

    $scope.load_featured  = function(c_to_hide) {
       $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"getFeatured"
                },
                success: function(data, textStatus, jqXHR) {
                    $("."+c_to_hide+"_loader").hide();
                    if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.featured=data;
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.featured=data;
                        });
                    }

                    //alert($scope.suggeted.length*180);

                    $("."+c_to_hide+"_loader_games_row_inner").css({
                        width:$scope[c_to_hide].length*260+"px"
                    })
                }
        });
    };

    $scope.load_my  = function(c_to_hide) {
       if($scope.user_login) {
        $http.post('/op',{op:"getMy"})
         .success(function(data, status, headers, config) {
         	if(!data) {
                    $scope.load_my(c_to_hide);
                    return;
                }
                    
                    
         	
             $scope.my=data;
             $("."+c_to_hide+"_loader_games_row_inner").css({
                 width:$scope[c_to_hide].length*260+"px"
             });
         });
       }
    };
    
    
    $scope.go_game = function(a) {
        window.open(a, '_blank');
    };



    $scope.__v_loader = function() {
      $(".loader_center").each(function(){
         var p_h=$(this).parent().height();
         //console.log(p_h);
      });
    };


    $scope.load_comments = function() {
        $scope.comments=[
           {name:"some name",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"1 hour ago",content:"Some content",likes:6},
           {name:"some name2",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"2 days ago",content:"Some content2",likes:2},
           {name:"some name3",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"3 hours ago",content:"Some content3",likes:0},
           {name:"some name4",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"6 years ago",content:"Some content4",likes:12},
           {name:"some name5",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"1 hour ago",content:"Some content5",likes:7},
           {name:"some name6",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"2 hour ago",content:"Some content6",likes:3},
           {name:"some name7",img:"http://gp3.googleusercontent.com/-m1_g_Op9J7g/AAAAAAAAAAI/AAAAAAAAERc/DXmKez-Qnyk/s48-c-k-no/photo.jpg",time:"2 hour ago",content:"Some content7",likes:1},
       ];
    };

    $scope.escape = escape;

    $scope.initPlay = function() {
       
      var url_tail="";
      if(Object.keys($location.search()).length !== 0) {
          
          
          for(i in $location.search()) {
              var val=$location.search()[i];
              url_tail+="&"+i+"="+val
              
          }
      }
      
      
//      if($location.search().length>0)
//          alert(1)
      
      $.ajax({
                type: "post",
                url: '/op',
                dataType:"json",
                data:{
                  op:"getGame",
                  gid:$routeParams.gid
                },
                success: function(data, textStatus, jqXHR) {
                    if($scope.$$phase || $scope.$root.$$phase) {
                            $scope.game=data;
                            $scope.game_src="/games?gid="+data.unique_id+"&uid="+$scope.user.uid+"&hu=1"+url_tail;
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.game=data;
                            $scope.game_src="/games?gid="+data.unique_id+"&uid="+$scope.user.uid+"&hu=1"+url_tail;
                        });
                    }
                    //alert(data.name);
                    var scope = angular.element(document.getElementById("head")).scope();
                    scope.page={
                            title:"Wheeldo - "+data.name,
                            description:"some description"
                    };



                    $http.post('/op',{op:"checkSubscribe",cid:data.cid})
                    .success(function(data, status, headers, config) {
                        if(data.subscribe==1) {
                            $scope.subscribe_text='Unsubscribe <img src="/media/css/dice/img/minus.png" />';
                        }
                        else {
                            $scope.subscribe_text='Subscribe <img src="/media/css/dice/img/plus.png" />';
                        }

                    });

					element = $('a[twitterajax]');
					if(element.length) {
						twttr.widgets.createShareButton(
	                        element.data('url'),
	                        element[0],
	                        function(el) { }, {
	                            count: 'none',
	                            text: element.data('text')
	                        }
	                    );
	            	}

	            	/*setTimeout(function(){
	            		console.log(window.devicePixelRatio);

	            		if(window.devicePixelRatio && window.devicePixelRatio != 1)
	                		$('.social_links').hide(0, function(){$(this).show();});
	            	}, 4000);*/

					// re-parse FBML for FB comment box - chaim
	            	FB.XFBML.parse();
                }
        });

        (function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=219145361621410";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    };


    $scope.createGame = function() {
       if($scope.user_login) {
           $location.path("/createGame");
        }
        else {
           $location.path("/signIn");
        }
    };



    $scope.edit_voucher_email = function() {
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/edit_email.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: EditEmailController,
          resolve: {
              f_data:function () {
                return {
                    subject:$scope.new_game.voucher_email.subject,
                    content:$scope.new_game.voucher_email.content,
                }
              }
          }
        });

        modalInstance.result.then(function (email) {
            if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.new_game.voucher_email.subject=email.subject;
                    $scope.new_game.voucher_email.content=email.content;
            }
            else {
                $scope.$apply(function () {
                    $scope.new_game.voucher_email.subject=email.subject;
                    $scope.new_game.voucher_email.content=email.content;
                });
            }
        }, function () {

        });
    };

    $scope.new_game={};
    $scope.uploadUrls={};
    $scope.active_Q=0;
    $scope.bu_exists=false;
    $scope.initCreateGame = function() {
        var t=new Date().getTime();
        $scope.design_url='/app/partials/dice/includes/design_content/bg_color.html?t='+t;
        
        $scope.new_game={
            "name":"",
            "full_desc":"",
            "tags":"",
            "lang":"en",
            "call_action_text":"",
            "call_action_link":"",
            "thumbnail":"",
            "channel":$scope.user.my_channels[0],
            "game_type":$scope.game_types[0],
            "private":0,
            "open_status":0,
            "results_signup":0,
            "signup_headline":"Sign up now!",
            "prize":0,
            "prize_text":"",
            "prize_time_limit":48,
            "share_button":"1",
            "CTA_button":0,
            "voucher":0,
            "voucher_name":"",
            "voucher_email":{
                subject:"",
                content:""
            },
            "quiz_game_data":{"instruction_text":"","instruction_img":""},
            "quiz_data":[{"question":"","media":"","more_text":"","rank":20,"answer":1,"answers":[{"text":"","media":""},{"text":"","media":""},{"text":"","media":""},{"text":"","media":""}]}],
            "test_yourself_data":{
                "main_question":{text:"",media:""},
                questions:[],
                results:[]
            },
            
            "game_design":{
                color_1:"",
                color_2:"",
                color_3:"",
                color_4:"",
                banner:"",
                background:""
            },
            "game_default_design":{}
        };

        

        // check if new_game backup exists:
        $http.post('/gt',{op:"checkIFCreateGameCookieExists"})
        .success(function(data, status, headers, config) {
            if(data.status=="ok") {
                $(".backup_restore").fadeIn();
                $scope.bu_exists=true;
            }
        });
        ///////////////////////////////////
        
        ///////////////////////////////////
        
        ///////////////////////////////////


        var res=getUploadUrlService("/csv_uploader");
        $scope.quiz_uploader={
           action:res.url,
           hash:res.hash,
           url_notify:res.url_notify
        };
        
        var res=getUploadUrlService("/csv_uploader");
        $scope.testyourself_uploader={
           action:res.url,
           hash:res.hash,
           url_notify:res.url_notify
        };


        loadGameDesignService($scope.new_game.game_type.default_template);



        $scope.uploadUrls.thumbnail=getImageUploadURLService();
        $scope.uploadUrls.thumbnail.res_mark=makeid();


        $scope.uploadUrls.banner=getImageUploadURLService();
        $scope.uploadUrls.banner.res_mark=makeid();

        $scope.uploadUrls.background=getImageUploadURLService();
        $scope.uploadUrls.background.res_mark=makeid();

        $scope.uploadUrls.banner_new=getImageUploadURLService();
        $scope.uploadUrls.banner_new.res_mark=makeid();
        
        $scope.uploadUrls.banner_ty=getImageUploadURLService();
        $scope.uploadUrls.banner_ty.res_mark=makeid();


        $scope.questions=[
            {
                question:"Demo question",
                answers:["Answer 1","Answer 2","Answer 3","Answer 4","Answer 5","Answer 6"]
            }
        ];

        $scope.timer=59;
        $scope.user_image="http://my.wheeldo.com/uimg_uid-"+$scope.user.ID+"___effect-c_fit,h_30,w_30.png";
        $scope.game_quiz_user={
            strikes:1,
            use_skip:1
        };

        $scope.ready=true;
        setTimeout(function(){
            $scope.create_game_top_fix_width();
        },0);
    };
    
    
    $scope.getResultRange = function(index){
       var l = $scope.new_game.test_yourself_data.results.length;
       var part=100/l; 

       var start=Math.floor(index*part)+1;
       var end=Math.floor((index+1)*part);


       return start+"-"+end;
    };

    $scope.addResult = function() {
        $scope.new_game.test_yourself_data.results.push({text:"",media:""});
    };

    $scope.removeResult = function(index) {
        $scope.new_game.test_yourself_data.results.splice(index,1);
    };

    var active_tab=1;
    $scope.active_tab=1;
    
    
    var tabs_validators={
          1: function(){
             return $scope.new_game.game_type?true:false;
          },
          2:function(){
             var ret=true;
             if(!$scope.new_game.name)
                 ret=false;
             return ret;
          },
          3:function() {
              return true;
          },
          4:function() {
              return true;
          },
          5:function() {
              return false;
          }
        };
    
    $scope.next_tab = function() {
        if(uploadInProcess)
            return;

        if(tabs_counter==active_tab)
            return;

        

        if($scope.bu_exists) {
            if(!confirm("Delete backup data?"))
                return;
        }

            if(active_tab>1) saveGameCoockie();
            $(".backup_restore").hide();
            $scope.bu_exists=false;

            if(tabs_validators[active_tab]()) {
                active_tab++;
                setTab();
            }

    };
    
    $scope.createGameStagesClick = function(to) {
        var from=parseInt($scope.active_tab);
        var to=parseInt(to);
        if(from>=to) {
            active_tab=to;
            setTab();
            return;
        }
        else {
            for(var i=from;i<to;i++) {
                if(tabs_validators[i]) {
                    $scope.next_tab();
                }
                else {
                    return;
                }
            }
            
        }
        //alert("from "+$scope.active_tab+" to "+to);
    };
    
    saveGameCoockie = function() {
        setTimeout(function(){
            $http.post('/gt',{op:"saveGameCoockie",new_game:$scope.new_game});
        },100)
        
    };
    
    $scope.restore_backup = function() {
        $http.post('/gt',{op:"getGameCoockie"}).success(function(data, status, headers, config) {
            $scope.new_game=data.new_game;
            $http.post('/gt',{op:"removeGameCoockie"});
            $(".backup_restore").hide();
            $scope.bu_exists=false;
            active_tab=2;
            setTab();
            $scope.selectGameAuto();
        });
    }

    $scope.back_tab = function() {
        if(active_tab==1)
            return;
        active_tab--;
        setTab();
    };


    setTab = function() {
        $scope.active_tab=active_tab;
        $(".progress_tab").removeClass("active");
        $(".progress_tab_content").removeClass("active");
        $(".progress_tab[tab_index="+active_tab+"]").addClass("active");
        $(".progress_tab_content[tab_index="+active_tab+"]").addClass("active");
    };

    $scope.select_game = function(index) {
        var game_selected=$scope.game_types[index].default_template;
        if($scope.game_types[index].status==0) {
            for(i in $scope.game_types){
                $scope.game_types[i].selected=0;
            }
            $scope.game_types[index].selected=1;
            $scope.new_game.game_type=$scope.game_types[index];
            loadGameDesignService(game_selected);
            $scope.next_tab();
        }

    };
    
    
    $scope.selectGameAuto = function() {
        for(i in $scope.game_types) {
            $scope.game_types[i].selected=0;
            if($scope.new_game.game_type.id==$scope.game_types[i].id) {
                $scope.game_types[i].selected=1;
            }
        }  
    }

    $scope.resetColor = function(name,value) {
        $scope.new_game.game_design[name]=value;
    };

    $scope.setActiveTab = function(index) {
        $(".con_bg").remove();
        $(".stage").removeClass("active");
        $(".stage").removeClass("pre_active");
        $(".stage[tab_index="+index+"]").addClass("active");
        $(".stage[tab_index="+index+"]").prepend('<div class="con_bg"></div>');

        if(!$(".stage[tab_index="+(index+1)+"]").hasClass("stage")) {
            $(".con_bg").addClass("end");
        }

        if(index==0) {
            $(".con_bg").addClass("first");
        }

        if(index>0) {
            $(".stage[tab_index="+(index-1)+"]").addClass("pre_active");
        }

        $(".tab").hide();
        $(".tab[tab_index="+index+"]").show();
    };

    $scope.createGame0Submit = function() {
    	$scope.setActiveTab(1);
    }

    $scope.selectFile = function(id) {
      $("#"+id).trigger("click");
    };

    $scope.clear_upload_thumb_pic = function(id ) {
        $scope.uploadUrls.thumbnail.img='';
        control = $("#"+id);
        //clones and destroys file input to solve issue where same file could not be re-uploded (like reseting the input)
        control.replaceWith( control.val('').clone( true ) );
    };

    $scope.clear_upload_header_pic = function(id,form_id ) {
        $scope.new_game.game_design.banner='';
        control = $("#"+id);
        /*
        var res_mark=$("#"+form_id).attr("res_mark");
        index = files_checker.indexOf(""+res_mark);
         if(index>-1) {
            files_checker.splice(index, 1);
         }
         */
        //clones and destroys file input to solve issue where same file could not be re-uploded (like reseting the input)
       // control.replaceWith( control.val('').clone( true ) );
    };

    $scope.selectContentFile = function() {
      $("#content_file").trigger("click");
    };

    $scope.imgUploadDone = function() {

    };

    var imgObj,ux_object;
    
    var uploadInProcess=false;
    $scope.onIMGSelect = function($files,form_id) {
        var formObj=$("#"+form_id);
        var res_mark=formObj.attr("res_mark");
        file_in_proccess=res_mark;
        imgObj=formObj.attr("imgObj");
        ux_object=formObj.attr("ux_object");

        var file=$files[0];

        var sizeInMB=file.size/1048576;

        if(sizeInMB>1) {
            alert("Image max size is 1Mb!");
            return;
        }


        var ex=file.name.split(".");
        var ext=ex[ex.length-1].toLowerCase();
         if(ext!="jpg" &&ext!="JPG" &&ext!="jpeg" &&ext!="png" &&ext!="gif" &&ext!="GIF") {
            $(".loader").hide();
            alert("Please upload jpg or png file only!");
            return;
        }

        $scope.fileData=file;
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".loadImage").show();
        $("#img_upload_frame").attr("onload","onFileUploadDone('"+file_in_proccess+"')");


        $("."+ux_object+"_loader_before").hide();
        $("."+ux_object+"_loader").show();
        uploadInProcess=true;
        formObj.submit();
        checkIFImgProcessDone();

    return;
  };



  $scope.onUserIMGSelect = function($files,form_id,object_to_update) {
        var formObj=$("#"+form_id);
        var res_mark=formObj.attr("res_mark");
        file_in_proccess=res_mark;
        imgObj=formObj.attr("imgObj");

        var file=$files[0];
        var ex=file.name.split(".");
        var ext=ex[ex.length-1].toLowerCase();
        if(ext!="jpg" &&ext!="JPG" &&ext!="jpeg" &&ext!="png" &&ext!="gif" &&ext!="GIF") {
            $(".loader").hide();
            alert("Please upload jpg or png file only!");
            return;
        }

        $scope.fileData=file;
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".loadImage").show();
        $("#img_upload_frame").attr("onload","onFileUploadDone('"+file_in_proccess+"')");


        $("."+imgObj+"_loader_before").hide();
        $("."+imgObj+"_loader").show();
        formObj.submit();
        checkIFImgProcessDoneObject(object_to_update);
    return;
  };



  checkIFImgProcessDone = function() {
      if(files_checker.indexOf(""+file_in_proccess)>-1) {
          var res=getFileDataService(file_in_proccess);
          

          $("."+ux_object+"_loader_before").show();
          $("."+ux_object+"_loader").hide();


          var urlres=getImageUploadURLService();
          $scope.uploadUrls[ux_object].url=urlres.url;
          $scope.uploadUrls[ux_object].res_mark=makeid();
          
          
          if(typeof res.img == 'undefined' || !res.img.length) {
          	alert('We\'re sorry, but your request could not be processed. Please try uploading this file again.');
          }
          else {
                if($scope.$$phase || $scope.$root.$$phase) {
                    imgUploadDone(res);
                }
                else {
                    $scope.$apply(function () {
                        imgUploadDone(res);
                    });
                }
                
          }
            uploadInProcess=false;
      }
      else {
          setTimeout(checkIFImgProcessDone,300);
      }
  };

  imgUploadDone = function(res) {
        
        
        $scope.uploadUrls[ux_object].img=res.img;
        
        if($scope.new_game[ux_object]=="") {
            $scope.new_game[ux_object]=res.img;
        }
        else {
            if(typeof $scope.new_game.game_design != 'object') {
                $scope.new_game.game_design = {
                                                color_1:"",
                                                color_2:"",
                                                color_3:"",
                                                color_4:"",
                                                banner:"",
                                                background:""
                                        };
            }
            $scope.new_game.game_design[imgObj]=res.img;
        }
  };

  $scope.setActiveQ = function(index) {
      $scope.active_Q=index;
  };


  var move_step=10;





  $scope.show_l_arr=false;
  $scope.show_r_arr=false;
  checkWhatArrowToDisplay = function() {

      if(curr_focus==0)
          $scope.show_l_arr=false;
      else
          $scope.show_l_arr=true;


      if($scope.new_game.quiz_data.length>curr_focus+14)
          $scope.show_r_arr=true;
      else
          $scope.show_r_arr=false;


  };


  $scope.page_quiz_left = function() {

        curr_focus-=move_step;

        if(curr_focus<0)
            curr_focus=0;
        $(".paging_in").css({
            left:-(curr_focus*50)+"px"
        });
        checkWhatArrowToDisplay();
  };

  var curr_focus=0;
  $scope.page_quiz_right = function() {

        var in_step=move_step;
        var l=$scope.new_game.quiz_data.length;

        if(l<14)
            return;
        var bulk_num=14+move_step;


        var left_on_right=l-curr_focus;

        if(left_on_right<bulk_num) {
            var limit_to=move_step-(bulk_num-left_on_right);
            in_step=limit_to;
        }

        curr_focus+=in_step;



        $(".paging_in").css({
            left:-(curr_focus*50)+"px"
        });

        checkWhatArrowToDisplay();
  };

 updateScopeUNK = function(objarr,value) {
      switch(objarr.length) {
            case 1:
                $scope[objarr[0]]=value;
            break;
            case 2:
                $scope[objarr[0]][objarr[1]]=value;
            break;
            case 3:
                $scope[objarr[0]][objarr[1]][objarr[2]]=value;
            break;
            case 4:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]]=value;
            break;
            case 5:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]][objarr[4]]=value;
            break;
            case 6:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]][objarr[4]][objarr[5]]=value;
            break;
            case 7:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]][objarr[4]][objarr[5]][objarr[6]]=value;
            break;
            case 8:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]][objarr[4]][objarr[5]][objarr[6]][objarr[7]]=value;
            break;
            case 9:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]][objarr[4]][objarr[5]][objarr[6]][objarr[7]][objarr[8]]=value;
            break;
            case 10:
                $scope[objarr[0]][objarr[1]][objarr[2]][objarr[3]][objarr[4]][objarr[5]][objarr[6]][objarr[7]][objarr[8]][objarr[9]]=value;
            break;
        }
  }


  checkIFImgProcessDoneObject = function(object) {
      if(files_checker.indexOf(""+file_in_proccess)>-1) {
          var res=getFileDataService(file_in_proccess);
          var urlres=getImageUploadURLService();

          var objarr=object.split("__");


          $("."+imgObj+"_loader_before").show();
          $("."+imgObj+"_loader").hide();

          $(".uploader_popup").fadeOut();


            if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.uploadUrls[imgObj].img=res.img;
                    $scope.uploadUrls[imgObj].url=urlres.url;
                    $scope.uploadUrls[imgObj].res_mark=makeid();
                    //alert(object);
                    updateScopeUNK(objarr,res.img);
            }
            else {
                $scope.$apply(function () {
                    $scope.uploadUrls[imgObj].img=res.img;
                    $scope.uploadUrls[imgObj].url=urlres.url;
                    $scope.uploadUrls[imgObj].res_mark=makeid();
                    //alert(object);
                    updateScopeUNK(objarr,res.img);
                });
            }
            saveUserService(makeDataReadyToSend($scope.user));
      }
      else {
          setTimeout(checkIFImgProcessDoneObject,300,object);
      }
  }

  //$scope.res_mark=getHash();




  var file_in_proccess=0;

  $scope.onQuizFileSelect = function($files) {
        var res_mark=$("#file_upload_form").attr("res_mark");
        file_in_proccess=res_mark;
        $(".loader").show();
        var file=$files[0];

        //console.log(file);
        var ex=file.name.split(".");
        var ext=ex[ex.length-1];
        if(ext!="csv"&&ext!="xls"&&ext!="xlsx") {
            $(".loader").hide();
            alert("Please upload CSV or Excel file!");
            return;
        }
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        var formObj=$("#file_upload_form");
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".main_image").hide();
        $(".loadImage").show();
        $("#upload_target").attr("onload","onFileUploadDone('"+file_in_proccess+"')");
        formObj.submit();
        checkIFFileProcessDone($("#upload_target"));
        return;

  }


  checkIFFileProcessDone = function() {
      if(files_checker.indexOf(file_in_proccess)>-1) {
          loadQuizData(getFileDataService(file_in_proccess));

      }
      else {
          setTimeout(checkIFFileProcessDone,300,upload_target);
      }
  }


  ifUploadDone = function() {
        var jqxhr = $.get( "/uploads/csv/"+$scope.quiz_uploader.hash+".txt", function() {
            var as_json=$.parseJSON(jqxhr.responseText);
            loadQuizData(as_json);
        })
        .fail(function() {
            setTimeout(ifUploadDone,500);
        });
    };


    loadQuizData = function(as_json) {
        $(".loader").hide();

        var quiz_data=[];
        for(r in as_json) {
            var row=as_json[r];
            if(row.length<4)
                continue;
            var row_a= {
              question: row[0],
              rank:row[1],
              answer:row[2],
              answers:[]
            };
            var i=0;
            for(c in row) {
                if(i>2) {
                   var col=row[c];
                   if(typeof col !== "function" && col!="" && col!=null)
                    row_a.answers.push({text:col});
                }
                i++;
            }


            if(typeof row !== "function")
                quiz_data.push(row_a);
        }

        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.new_game.quiz_data=quiz_data;
                checkWhatArrowToDisplay();
        }
        else {
            $scope.$apply(function () {
                $scope.new_game.quiz_data=quiz_data;
                checkWhatArrowToDisplay();
            });
        }


    };
    
    
    
    // load test yourself data:
    
    $scope.selectTestYourselfContentFile = function() {
      $("#content_file_test_yourself").trigger("click");
    };
    
      $scope.onTestYourselfFileSelect = function($files) {
        var t=new Date().getTime();

        var res_mark=$("#file_upload_form").attr("res_mark");
        file_in_proccess=res_mark;
        $(".loader").show();
        var file=$files[0];

        //console.log(file);
        var ex=file.name.split(".");
        var ext=ex[ex.length-1];
        if(ext!="csv"&&ext!="xls"&&ext!="xlsx") {
            $(".loader").hide();
            alert("Please upload CSV or Excel file!");
            return;
        }
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        var formObj=$("#file_upload_form_test_yourself");
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".main_image").hide();
        $(".loadImage").show();
        $("#upload_target").attr("onload","onFileUploadDone('"+file_in_proccess+"')");
        formObj.submit();
        
        checkIFFileProcessDoneTestYourself($("#upload_target"));
    return;
  };
  
  
  checkIFFileProcessDoneTestYourself = function() {
      if(files_checker.indexOf(file_in_proccess)>-1) {
          
          loadTestYourselfData(getFileDataService(file_in_proccess));

      }
      else {
          setTimeout(checkIFFileProcessDoneTestYourself,300,upload_target);
      }
  };
  
  loadTestYourselfData = function(as_json) {
        $(".loader").hide();
        
        /*
         * "test_yourself_data":{
                "main_question":{text:"",media:""},
                questions:[
                    {
                        text:"",
                        media:"",
                        answers:[
                            {text:"",media:"",strength:0},
                            {text:"",media:"",strength:50},
                            {text:"",media:"",strength:100},
                            {text:"",media:"",strength:75}
                        ]
                    },
                    {
                        text:"",
                        media:"",
                        answers:[
                            {text:"",media:"",strength:0},
                            {text:"",media:"",strength:50},
                            {text:"",media:"",strength:100},
                            {text:"",media:"",strength:75}
                        ]
                    }
                ],
                results:[{text:"Test results",media:""},{text:"Test results",media:""}]
            },
         */

        var questions=[];
        for(r in as_json) {
            var row=as_json[r];
            var row_a= {
              text: row[0],
              media:"",
              answers:[]
            };
            
            
            //create answerObj:
            var ans;
            
            var i=0;
            
            
            var in_c=1;
            for(c in row) {     
                if(i>0) {
                //if(false) {
                    if((i+1)%2==0) {
                        //console.log("creating answer object:");
                        ans={text:"",media:"",strength:""};
                    }

                    var val=row[c]; 
//                    if(val==0) {
//                        alert("val=0");
//                    }
                    
                    if(val==0 || (typeof val !== "function" && val!="" && val!=null)) {
                        if(in_c==1) {
                            ans.text=val;
                            in_c++;
                        }
                        else {
                            ans.strength=val;
                            row_a.answers.push(ans);
                            in_c=1;
                        }
                    }
                    else {
                        //console.log("Skip:"+val);
                        in_c=1;
                    }
                }
                
                i++;
//                if(i>1) {
//                   var text=row[c];
//                   var strength=row[c+1];
//                   if(typeof text !== "function" && text!="" && text!=null)
//                    row_a.answers.push({text:text,media:"",strength:strength});
//                }
            }

            
            if(typeof row !== "function")
                questions.push(row_a);
        }

        
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.new_game.test_yourself_data.questions=questions;
                checkWhatArrowToDisplay();
        }
        else {
            $scope.$apply(function () {
                $scope.new_game.test_yourself_data.questions=questions;
                checkWhatArrowToDisplay();
            });
        }
  }
    
    ///////////////////////////


 //$scope.new_game.quiz_data =[{"question":"question 2","rank":23,"answer":"B","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 3","rank":100,"answer":"C","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 4","rank":98,"answer":"d","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 5","rank":15,"answer":"e","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 6","rank":23,"answer":"a","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 7","rank":43,"answer":"b","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 8","rank":12,"answer":"b","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 9","rank":87,"answer":"c","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]},{"question":"question 10","rank":23,"answer":"a","answers":["Ans 1","Ans 2","Ans 3","Ans 4","Ans 5","Ans 6"]}];

 $scope.createGame1Submit = function() {
   $scope.setActiveTab(2);
 };

 $scope.createGame2Submit = function() {
   $scope.setActiveTab(3);
 };


 $scope.onUsersFIleSelect = function($files) {

        $(".loader").show();
        var file=$files[0];
        var ex=file.name.split(".");
        var ext=ex[ex.length-1];
        if(ext!="csv"&&ext!="xls"&&ext!="xlsx") {
            $(".loader").hide();
            alert("Please upload CSV or Excel file!");
            return;
        }
        //console.log(file);

        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        var formObj=$("#users_upload_form");
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".main_image").hide();
        $("#voter_preview_image").attr("src","");
        $(".loadImage").show();
        $("#users_upload_target").attr("onload","onCsvFileLoad()");
        formObj.submit();
        ifUploadDoneUsers();
    return;
  }

  ifUploadDoneUsers = function() {
      //return;
        var jqxhr = $.get( "/uploads/csv/"+$scope.res_mark.hash+".txt", function() {
            var as_json=$.parseJSON(jqxhr.responseText);
            loadUsersData(as_json);
        })
        .fail(function() {
            setTimeout(ifUploadDoneUsers,500);
        });
    };


    loadUsersData = function(as_json) {
        $(".loader").hide();
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.new_game.users_data=as_json;
        }
        else {
            $scope.$apply(function () {
                $scope.new_game.users_data=as_json;
            });
        }
    };

    $scope.createGame3Submit = function() {
        alert("UNDER CONSTRUCTION");
        //console.log($scope.new_game);
    };

    $scope.deleteQuizRow = function(index) {
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.new_game.quiz_data.splice(index,1);
                $scope.active_Q=0;
        }
        else {
            $scope.$apply(function () {
                $scope.new_game.quiz_data.splice(index,1);
                $scope.active_Q=0;
            });
        }
    };
    
    $scope.deleteQuizRowTestYourself = function(index) {
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.new_game.test_yourself_data.questions.splice(index,1);
                $scope.active_Q=0;
        }
        else {
            $scope.$apply(function () {
                $scope.new_game.test_yourself_data.questions.splice(index,1);
                $scope.active_Q=0;
            });
        }
    };
    
    
    $scope.addImage = function(imgObj) {
        
        var ur="http://www2.psd100.com/ppp/2013/11/2801/Add-a-picture-1128025442.png";
        applyImageToObject(imgObj,ur);
        
//        var scope_Arr=imgObj.split(".");
//        console.log(scope_Arr);
//        
//        
//        
//        $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]]=  ur;=  ur;    
    }
    
    var applyImageToObject = function(imgObj,src) {
        var scope_Arr=imgObj.split(".");
        switch(scope_Arr.length){
            case 1:
                $scope[scope_Arr[0]] = src;
            break;
            case 2:
                $scope[scope_Arr[0]][scope_Arr[1]] = src;
            break;
            case 3:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]] = src;
            break;
            case 4:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]] = src;
            break;
            case 5:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]] = src;
            break;
            case 6:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]] = src;
            break;
            case 7:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]] = src;
            break;
            case 8:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]][scope_Arr[7]] = src;
            break;
            case 9:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]][scope_Arr[7]][scope_Arr[8]] = src;
            break;
            case 10:
                $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]][scope_Arr[7]][scope_Arr[8]][scope_Arr[9]] = src;
            break;
        }
    }

    var publish_in_progress=false;
    var new_game_id=0;
    $scope.publish_game = function() {
        if(publish_in_progress)
            return;
        publish_in_progress=true;
        $("#publish_new_game").addClass("working");
        $("#publish_new_game").html('<img style="height:20px" src="/media/img/ajax-loader.gif" /> &nbsp; Please wait... ');
        var data=makeDataReadyToSend($scope.new_game);


        $http.post('/gt',{op:"publishGame",data:$scope.new_game})
        .success(function(data, status, headers, config) {
            var embed='<iframe width="700" height="600" border="0" scrolling="no" style="width:700px;height:600px;border:0;overflow:hidden;" src="http://www.wheeldo.co/games?gid='+data.game_id+'&uid=0"></iframe>';
            new_game_id=data.game_id;
            $scope.new_game_unique_id=data.game_id;
            $scope.new_game_link="http://www.wheeldo.co/play/"+data.game_id;
            $scope.embed_link=embed;
            $("#publish_new_game").removeClass("working");
            $("#publish_new_game").addClass("done");
            $("#publish_new_game").html("Game ready!");
            $(".back").hide();
            $(".prev_game").hide();
            $(".share_links").fadeIn();
        });



//        $.ajax({
//                type: "post",
//                url: '/gt',
//                dataType:"json",
//                data:{
//                  op:"publishGame",
//                  data:data
//                },
//                success: function(data, textStatus, jqXHR) {
//                    var embed='<iframe width="700" height="560" border="0" style="width:700px;height:560px;border:0;overflow:hidden;" src="http://www.wheeldo.co/games?gid='+data.game_id+'&uid=0"></iframe>';
//                    new_game_id=data.game_id;
//                    if($scope.$$phase || $scope.$root.$$phase) {
//                            $scope.new_game_link="http://www.wheeldo.co/play/"+data.game_id;
//                            $scope.embed_link=embed;
//
//
//
//                    }
//                    else {
//                        $scope.$apply(function () {
//                            $scope.new_game_link="http://www.wheeldo.co/play/"+data.game_id;
//                            $scope.embed_link=embed;
//                        });
//                    }
//
//                    $("#publish_new_game").removeClass("working");
//                    $("#publish_new_game").addClass("done");
//                    $("#publish_new_game").html("Game ready!");
//                    $(".back").hide();
//                    $(".prev_game").hide();
//                    $(".share_links").fadeIn();
//                }
//        });




    };


    $scope.invite_followers = function() {

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/invite_followers.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: inviteFollowersController,
          resolve: {
              f_data:function () {
                return {
                    channel:$scope.new_game.channel,
                    game_link:$scope.new_game_link,
                    lang:$scope.new_game.lang
                }
              }
          }
        });

        modalInstance.result.then(function (status) {


        }, function () {

        });
    };




    $scope.embedClick=function() {
        $(".selectAll").select();
    };


    $scope.facebookShareGameUser = function() {

        FB.ui(
        {
            method: 'feed',
            name: $scope.game.name,
            link: "http://www.wheeldo.co/play?gid="+$scope.game.unique_id,
            picture: $scope.game.thumbnail,
            caption: $scope.game.name,
            description: $scope.game.full_desc,
            message: ''
        });

        return;
    };

    $scope.facebookShareGame = function() {

        FB.ui(
        {
            method: 'feed',
            name: 'New game in wheeldo ::: '+$scope.new_game.name,
            link: $scope.new_game_link,
            picture: $scope.new_game.thumbnail,
            caption: $scope.new_game.name,
            description: $scope.new_game.full_desc,
            message: ''
        });

        return;
    };

    var small_game_block_width=260;

    $scope.load_suggested_right = function(block_name) {

       var left=parseInt($("."+block_name+"_loader_games_row_inner").css("left"));
       var game_row_width=$(".games_row").width();
       var avalible_blocks=$scope[block_name].length;
       var block_view=Math.floor(game_row_width/small_game_block_width);
       var move_right=small_game_block_width*block_view;
       var left_on_right=avalible_blocks-Math.abs(left/small_game_block_width)-block_view;



       if(left_on_right<block_view) {
           move_right=small_game_block_width*left_on_right;
       }

        $("."+block_name+"_loader_games_row_inner").css({
            left:left-move_right
        });

        setTimeout(checkArrows,800);
    };




    checkArrows = function() {
        $(".gallery_wrapper").each(function(){
            var gallery=$(this).attr("gallery");
            if(!$scope[gallery])
                return;
            var game_row_width=$(".games_row").width();

            var avalible_blocks=$scope[gallery].length;


            var left=parseInt($("."+gallery+"_loader_games_row_inner").css("left"));

            var block_view=Math.floor(game_row_width/small_game_block_width);



            var left_on_left=Math.abs(left/small_game_block_width);
            var left_on_right=avalible_blocks-Math.abs(left/small_game_block_width)-block_view;


            if(left_on_left<=0) {
                $(".load_left."+gallery).fadeOut("fast");
            }
            else {
                $(".load_left."+gallery).show();
            }

            if(left_on_right<=0) {
                $(".load_right."+gallery).fadeOut("fast");
            }
            else {
                $(".load_right."+gallery).show();
            }


            //console.log("left:"+left_on_left+"  |  right:"+left_on_right);


        })
    }

    $scope.checkArrows = checkArrows;



    $scope.load_suggested_left = function(block_name) {
       var left=parseInt($("."+block_name+"_loader_games_row_inner").css("left"));
       var game_row_width=$(".games_row").width();
       var avalible_blocks=$scope[block_name].length;
       var block_view=Math.floor(game_row_width/small_game_block_width);
       var left_on_left=Math.abs(left/small_game_block_width);

       var left_on_right=avalible_blocks-Math.abs(left/small_game_block_width)-block_view;



       if(left_on_left<=block_view) {
           $("."+block_name+"_loader_games_row_inner").css({
                left:0
            });
       }
       else {
           $("."+block_name+"_loader_games_row_inner").css({
                left:left+(block_view*small_game_block_width)
            });
       }

       setTimeout(checkArrows,800);
    };


    $scope.sign_in_popup = function() {

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/login_popup.html?t='+t,
          keyboard:false,
          backdrop:'static',
          windowClass: 'loginPopUp',
          controller: loginController,
          resolve: {
              f_data:function () {
                return {
                    user:$scope.user
                }
              }
          }
        });

        modalInstance.result.then(function (status) {
            if(status==2)
                $scope.join_popup();
            if(status==3)
                $scope.forgot_password();

        }, function () {

        });

    };

    $scope.forgot_password = function() {

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/forgot_password_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: forgotPasswordController,
          resolve: {
              f_data:function () {
                return {
                    a:1
                }
              }
          }
        });

        modalInstance.result.then(function (status) {
            if(status==2)
                $scope.sign_in_popup();


        }, function () {

        });

    };

    $scope.user_popup = function() {

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/user_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: userController,
          resolve: {
              f_data:function () {
                return {
                    a:1
                }
              }
          }
        });

        modalInstance.result.then(function (status) {

        if(status==2)
            $location.path("/signOut");

        }, function () {

        });

    };






    $scope.join_popup = function() {

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/join_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: joinController,
          resolve: {
              f_data:function () {
                return {
                    a:1
                }
              }
          }
        });

        modalInstance.result.then(function (status) {
            if(status==1)
                $("form").show();
            if(status==2)
                $scope.sign_in_popup();

        }, function () {

        });

    };


    $scope.article_init = function() {
        $http.post('/op',{op:"getArticle",article_name:$routeParams.article_name})
        .success(function(data, status, headers, config) {
            $scope.article=data;
        });


    };

    var tabs_counter=0;
    $scope.create_game_top_fix_width = function() {
        var wrapper_width=$("#create_game_content").width();
        var c=0;
        $(".progress_tab").each(function(){
            c++;
            tabs_counter++;
        });


        var tab_width=Math.floor(wrapper_width/c);
        $(".progress_tab").css({
            width:tab_width+"px"
        });
    };


    $scope.my_channels_init = function() {
        $http.post('/op',{op:"getMyChannels"})
        .success(function(data, status, headers, config) {
            $scope.channels=data.channels;
            $scope.shared_channels=data.shared_channels;
        });

    };


    $scope.upload_image = function(image_obj,note,extr) {


        var extra=typeof(extr) !== "undefined" ? extr : "";
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/upload_image.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'uploadImageopUp',
          controller: uploadImageController,
          resolve: {
              f_data:function () {
                return {
                    note:note,
                    extra:extra
                }
              }
          }
        });

        modalInstance.result.then(function (data) {
            if(data.img) {
                var objarr=image_obj.split("__");
                updateScopeUNK(objarr,data.img);
            }

        }, function () {

        });



    };


    $scope.saveChannel = function(cid) {
        $http.post('/gt',{
            op:"saveChannel",
            cid:cid,
            name:this.channel_data.name,
            description:this.channel_data.description,
            small_icon:this.channel_data.small_icon,
            cover:this.channel_data.cover
        })
        .success(function(data, status, headers, config) {
            alert("saved!");
        });
    };

    $scope.loadChannleAdmins = function(channle_id) {
        $http.post('/gt',{
            op:"loadChannleAdmins",
            channle_id:channle_id
        })
        .success(function(data, status, headers, config) {
            $scope.channle_admins=data;
        });
    }

    $scope.removeChannleAdmin = function(index) {
        if(!confirm("Sure?"))
            return;
        var r_id=$scope.channle_admins[index].id;
        $http.post('/gt',{
            op:"removeChannleAdmin",
            r_id:r_id
        })
        .success(function(data, status, headers, config) {
            $scope.channle_admins.splice(index,1);
        });
    }

    $scope.addChannelAdmin = function(channle_id) {
        if(!this.email)
            return;


        $http.post('/gt',{
            op:"addChannelAdmin",
            channle_id:channle_id,
            email:this.email

        })
        .success(function(data, status, headers, config) {
            $scope.loadChannleAdmins(channle_id);
        });

    }




    $scope.addUserTop = function() {

        //console.log(this.new_user_top);


        var users=[this.new_user_top];

        $http.post('/gt',{op:"addUsers",cid:$routeParams.cid,users:users})
        .success(function(data, status, headers, config) {
            getChannelUsersService($routeParams.cid);
        });

    };

    $scope.unsubscribe_user = function(id) {
        if(!confirm("Sure?"))
            return;
        $http.post('/gt',{op:"removeUserChannel",cid:$routeParams.cid,uid:id})
        .success(function(data, status, headers, config) {
            getChannelUsersService($routeParams.cid);
        });
    }

    $scope.loadChannelGames = function() {
        $http.post('/gt',{op:"getGames",cid:$routeParams.cid})
        .success(function(data, status, headers, config) {
            $scope.channel_games=data;
            
        });
    }


    $scope.editGame = function(id){
        $location.path("/editGame/"+id);
    }

//    $scope.chek_init_c=0;
//    $scope.chek_init = function() {
//
//        $scope.chek_init_c++;
//    }

    $scope.initEditGame = function() {
        var t=new Date().getTime();
        $scope.design_url='/app/partials/dice/includes/design_content/bg_color.html?t='+t;
        active_tab=2;
        setTab();
        $scope.new_game={
            "name":"",
            "full_desc":"",
            "tags":"",
            "lang":"en",
            "thumbnail":"",
            "channel":$scope.user.my_channels[0],
            "private":0,
            "open_status":0,
            "results_signup":0,
            "signup_headline":"Sign up now!",
            "prize":0,
            "prize_text":"",
            "prize_time_limit":0,
            "share_button":"1",
            "CTA_button":0,
            "voucher":0,
            "voucher_name":"",
            "voucher_email":{
                subject:"",
                content:""
            },
            "quiz_game_data":{"instruction_text":"","instruction_img":""},
            "quiz_data":[{"question":"","media":"","more_text":"","rank":20,"answer":1,"answers":[{"text":"","media":""},{"text":"","media":""},{"text":"","media":""},{"text":"","media":""}]}],
            "game_design":{
                color_1:"",
                color_2:"",
                color_3:"",
                color_4:"",
                banner:"",
                background:""
            }
        };
        $scope.gid=$routeParams.gid;
        $http.post('/gt',{op:"getGameForEdit",gid:$routeParams.gid})
        .success(function(data, status, headers, config) {
            
            
            
            $scope.new_game=data;


            $scope.new_game.channel=$scope.user.my_channels[data.channel_c];
        });
        
        $scope.addResult2 = function(){
            console.log(addResult);
        }



        var res=getUploadUrlService("/csv_uploader");
        $scope.quiz_uploader={
           action:res.url,
           hash:res.hash,
           url_notify:res.url_notify
        };

        var res=getUploadUrlService("/csv_uploader");
        $scope.testyourself_uploader={
           action:res.url,
           hash:res.hash,
           url_notify:res.url_notify
        };

        $scope.uploadUrls.thumbnail=getImageUploadURLService();
        $scope.uploadUrls.thumbnail.res_mark=makeid();


        $scope.uploadUrls.banner=getImageUploadURLService();
        $scope.uploadUrls.banner.res_mark=makeid();

        $scope.uploadUrls.background=getImageUploadURLService();
        $scope.uploadUrls.background.res_mark=makeid();
        
        $scope.uploadUrls.banner_new=getImageUploadURLService();
        $scope.uploadUrls.banner_new.res_mark=makeid();
        
        $scope.uploadUrls.banner_ty=getImageUploadURLService();
        $scope.uploadUrls.banner_ty.res_mark=makeid();


        $scope.questions=[
            {
                question:"Demo question",
                answers:["Answer 1","Answer 2","Answer 3","Answer 4","Answer 5","Answer 6"]
            }
        ];

        $scope.timer=59;
        $scope.user_image="http://my.wheeldo.com/uimg_uid-"+$scope.user.ID+"___effect-c_fit,h_30,w_30.png";
        $scope.game_quiz_user={
            strikes:1,
            use_skip:1
        };


        var embed='<iframe height="600" border="0" scrolling="no" style="width:100%;max-width:700px;height:600px;border:0;overflow:hidden;" src="http://www.wheeldo.co/games?gid='+$routeParams.gid+'&uid=0"></iframe>';
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.new_game_link="http://www.wheeldo.co/play/"+$routeParams.gid;
                $scope.embed_link=embed;



        }
        else {
            $scope.$apply(function () {
                $scope.new_game_link="http://www.wheeldo.co/play/"+$routeParams.gid;
                $scope.embed_link=embed;
            });
        }

        $scope.ready=true;
        setTimeout(function(){
            $scope.create_game_top_fix_width();
        },0);
    };


    $scope.save_edit_game = function() {
        if(publish_in_progress)
            return;
        publish_in_progress=true;
        $("#publish_new_game").addClass("working");
        $("#publish_new_game").html('<img style="height:20px" src="/media/img/ajax-loader.gif" /> &nbsp; Please wait... ');

        $http.post('/gt',{op:"saveEditGame",gid:$routeParams.gid,game_data:$scope.new_game})
        .success(function(data, status, headers, config) {
                    $("#publish_new_game").removeClass("working");
                    $("#publish_new_game").addClass("done");
                    $("#publish_new_game").html("Saved!");

                    setTimeout(function(){
                        $("#publish_new_game").removeClass("done");
                        $("#publish_new_game").html("Save Game");
                        publish_in_progress=false;
                    },
                    1000);

        });


    };


    $scope.setActiveTab = function(index) {
        active_tab=index;
        setTab();
    };


    $scope.addNewChannel = function() {
        var channel_name=prompt("Channel name:");
        if(!channel_name || channel_name=="")
            return;


         $http.post('/gt',{op:"addNewChannel",channel_name:channel_name})
        .success(function(data, status, headers, config) {
               $scope.user.my_channels.push(data);
               $scope.my_channels_init();
        });



    }


    $scope.subscribeChannle = function(cid, forceState) {
        if(!$scope.user_login) {
            $scope.sign_in_popup();
            return;
        }

        var post_vars = {op:"subscribeToggle",cid:cid};
        if(typeof forceState != 'undefined')
        	post_vars.forceState = forceState;

        $http.post('/op', post_vars).success(function(data, status, headers, config) {
            if(data.subscribe==1) {
                $scope.subscribe_text='Unsubscribe <img src="/media/css/dice/img/minus.png" />';
            }
            else {
                $scope.subscribe_text='Subscribe <img src="/media/css/dice/img/plus.png" />';
            }
        });
    };


    $scope.subscribeText = function(cid) {
        $scope.game.unique_link
        $http.post('/op',{op:"checkSubscribe",cid:cid})
        .success(function(data, status, headers, config) {

        });
        return 'Subscribe <img src="media/css/dice/img/plus.png" />';
    }

    $scope.contactUs = function() {
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: '/app/partials/'+skin+'/includes/contact_us_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'loginPopUp',
          controller: contactUsController,
          resolve: {
              f_data:function () {
                return {
                    user:$scope.user
                }
              }
          }
        });
    }

    $scope.settings_init = function() {
        $http.post('/gt',{op:"loadSettings"})
        .success(function(data, status, headers, config) {
            $scope.user_data=data;
        });
    }

    $scope.saveSettings = function() {
      $http.post('/gt',{op:"saveSettings",user_data:this.user_data})
        .success(function(data, status, headers, config) {
            $scope.user = getUserSrvice();
        });
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
						data: profile
					},
					success: function(data, textStatus, jqXHR) {
						stopProcessing();

						if(data.status == 'ok') {
							location = location.href;
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



        $scope.loadQuestionnaires = function() {
            var t=new Date().getTime();
            var modalInstance = $modal.open({
              templateUrl: '/app/partials/'+skin+'/includes/loadQuestionnaires.html?t='+t,
              keyboard:true,
              //backdrop:'static',
              windowClass: 'uploadImageopUp',
              controller: loadQuestionnairesController,
              resolve: {
                  f_data:function () {
                    return {
                        a:1
                    }
                  }
              }
            });

            modalInstance.result.then(function (quiz_data) {
                if(quiz_data) {
                    //console.log(quiz_data);
                    $scope.new_game.quiz_data=quiz_data;
                    checkWhatArrowToDisplay();
                }
            }, function () {

            });
        };




        $scope.hideFrameData = function() {
            if(__isMobile) {
                $("body").addClass("no_frame");
                no_frame=true;

                var sc_h=window.parent.screen.height;
                $("#player").css({height:sc_h+"px"});
                $("#game_frame").css({height:sc_h+"px"});
            }
        }

        $scope.showFrameData = function() {
            $("body").removeClass("no_frame");
            no_frame=false;
        }

        $scope.deleteGame = function(index) {
            var game=$scope.channel_games[index];
            if(!confirm("Delete `"+game.name+"` game?"))
                return;
            var game_id=game.id;

            $http.post('/gt',{op:"deleteGame",game_id:game_id})
            .success(function(data, status, headers, config) {
                $scope.channel_games.splice(index,1);
            });

        };
        
        $scope.deleteGameMy = function(index) {
            var game=$scope.my[index];
            if(!confirm("Delete `"+game.name+"` game?"))
                return;
            var game_id=game.id;

            $http.post('/gt',{op:"deleteGame",game_id:game_id})
            .success(function(data, status, headers, config) {
                $scope.my.splice(index,1);
            });

        };

        $scope.reports={};
        $scope.gameReport = function(game_id) {
            var open=false;
            var obj=$(".game_id_"+game_id);
            if(obj.hasClass("open"))
                open=true;
            $(".game").removeClass("open");
            if(!open) {
                if(!$scope.reports[game_id]) {
                    $http.post('/gt',{op:"load_report_data",game_id:game_id,cid:$routeParams.cid})
                    .success(function(data, status, headers, config) {
                        $scope.reports[game_id]=data;
                        $(".game_id_"+game_id+"_data").show();
                        $(".game_id_"+game_id+"_loader").hide();
                    });
                }

                obj.addClass("open");
            }
        };
        
        $scope.gameFullReport = function(game_type,gid) {
            $location.path("/report/"+game_type+"/"+gid);
        };
        
        $scope.funnelInit = function(data) {
            var chart = new FunnelChart({
                    data: data,
                    width: $(".report_box").width(), 
                    height: 450, 
                    bottomPct: 1/100
            });
            chart.draw('#funnelContainer', 10);
        };
        
        $scope.reportInit = function() {
            var t=new Date().getTime();
            $scope.game_type=$routeParams.game_type;
            
            $scope.report_url='/app/partials/dice/reports/'+$routeParams.game_type+'.html?t='+t;

            
            

            
            var gid=$routeParams.gid;
            
            Gamereport.__generalDataLoad(gid).success(function(response) {
                $scope.gid=gid;
                $scope.report_general_data = response;
            });
            
            Gamereport.__trafficDataLoad(gid).success(function(response) {
                $scope.report_traffic_data = response;
                $scope.skip_reg_counter=0;
                
                if(response.players.length>0) {
                    
                    $scope.funnel={
                        all:0,
                        start_game:0,
                        finish_game:0,
                        end_game_ctas:0,
                        cta:0,
                        coupon:0,
                        share:0
                    };
                    
                    for(i in response.players) {
                        
                        var player=response.players[i];
                        
                        
                        if(!isFunction(player)) {
                            if(player.skip_reg=="1") {
                                $scope.skip_reg_counter++;
                            }

                            if(player.start_game==1) {
                                $scope.funnel.start_game++;
                            }
                            if(player.finish_game==1) {
                                $scope.funnel.finish_game++;
                            }
                            if(player.cta==1||player.coupon==1||player.share==1) {
                                $scope.funnel.end_game_ctas++;
                            }
                            
                            if(player.cta==1) {
                                $scope.funnel.cta++;
                            }
                            if(player.coupon==1) {
                                $scope.funnel.coupon++;
                            }
                            if(player.share==1) {
                                $scope.funnel.share++;
                            }
                            
                            $scope.funnel.all++;
                        }
                    }
                    
                    $scope.funnel_drop ={
                        start_game:100-Math.ceil($scope.funnel.start_game/$scope.funnel.all*100),
                        finish_game:100-Math.ceil($scope.funnel.finish_game/$scope.funnel.start_game*100),
                        end_game_ctas:100-Math.ceil($scope.funnel.end_game_ctas/$scope.funnel.finish_game*100),
                    };


                    
                    var data = [['All Traffic', $scope.funnel.all], ['Start the game', $scope.funnel.start_game], ['Finished the game', $scope.funnel.finish_game], ['CTA', $scope.funnel.end_game_ctas]];
                    $scope.funnelInit(data);
                }
                
                if(Object.keys(response.referres).length !== 0) {               
                    var c=0;
                    var refData=[];
                    for(ref in response.referres) {
                        var counter=response.referres[ref];
                        if(ref=="")
                            ref="Not specified";
                        refData[c]={};
                        refData[c].c=[];
                        refData[c].c.push({v:ref});
                        refData[c].c.push({v:counter});
                        c++;
                    }
                    var data={"cols": [
                        {id: "name", label: "Name", type: "string"},
                        {id: "unique_users", label: "Unique users", type: "number"}
                    ]};

                    data.rows=refData;

                    throwChart("chart_traffic_data",data,"ColumnChart","Traffic","100%","400px",["#f59415"]);
                }
            });
            
            
            Gamereport.__resultsDataLoad(gid).success(function(response) {
                
                $scope.report_results_data = response;
                
                
                for(i in $scope.report_results_data.players_list) {
                    $scope.report_results_data.players_list[i].selected=false;
                };
                
                
                
                

                if($scope.game_type==3) {
                    var game_results_bins=[];

                    for(i=0;i<response.game_results.length;i++) {
                        game_results_bins[i]=0;
                    }

                    var data={"cols": [
                        {id: "total", label: "Total users", type: "string"},
                        {id: "participants", label: "Participants users", type: "string"}
                    ]};



                    for(i in response.players_list) {
                        var result=parseInt(response.players_list[i].result)-1;
                        game_results_bins[result]++;
                    }


                    var dataRows=[];
                    for(c in game_results_bins) {

                        var res_name=parseInt(c)+1;
                        dataRows[c]={};
                        dataRows[c].c=[];
                        dataRows[c].c.push({v:res_name});
                        dataRows[c].c.push({v:game_results_bins[c]});
                    }

                    data.rows=dataRows;


                    throwChart("chart_game_results",data,"PieChart","Game Results","100%","400px",['#33ADDF','#E14F4F','#85C7E9','#C01D4A','#C096B8','#002156'],true,"","","<div>fuck</div>");
                }
                
                
                if($scope.game_type==2) {
                    
                    var c=0;
                    var leadersData=[];
                    for(c in response.leaders) {
                        var user=response.leaders[c];
                        
                       
                        
                        if(!isFunction(user)) {
                            leadersData[c]={};
                            leadersData[c].c=[];
                            leadersData[c].c.push({v:user.name+" (ID:"+user.ID+")"});
                            leadersData[c].c.push({v:user.score});
                            c++;
                        }
                    }
                    var data={"cols": [
                        {id: "name", label: "Name", type: "string"},
                        {id: "score", label: "Score", type: "number"}
                    ]};

                    data.rows=leadersData;

                    throwChart("chart_leaders",data,"ColumnChart","Leaders","100%","400px",["#25ABE0"]);
                }
                
            });
            
            
            
            Gamereport.__leadsDataLoad(gid).success(function(response) {
                $scope.report_leads=response;    
                $scope.leads_counter=0;
                for(i in response) {
                    if(typeof(response[i])!=="function")
                        $scope.leads_counter++;
                }
            });
            
            
        };
        
        $scope.resetGame = function() {
            return;
            if(!confirm("Are you sure you want to reset all data for this game?"))
                return;
           var gid=$routeParams.gid;
          $http.post('/gt',{op:"resetGame",gid:gid})
            .success(function(data, status, headers, config) {
                $scope.reportInit(); 
            });
        };
        
        var user_checked_all=false;
        $scope.checkAll = function() {
            user_checked_all=!user_checked_all;
            for(i in $scope.report_results_data.players_list) {
                $scope.report_results_data.players_list[i].selected=user_checked_all;
            }
            $scope.checkIfMultipleOption();
            
        };
        
        
        $scope.show_multi=false;
        $scope.checkIfMultipleOption = function(list) {
            $scope.show_multi=false;
            $scope.records_C=0;
            for(i in $scope.report_results_data.players_list) {
                if($scope.report_results_data.players_list[i].selected===true) {
                    $scope.show_multi=true;
                    $scope.records_C++;
                }
            }
        };
        
        $scope.resetGameRecords = function() {
            if(!confirm("Are you sure you want to delete all "+$scope.records_C+" records?"))
                return;
            
            
            var records=[];
            for(i in $scope.report_results_data.players_list) {
                if($scope.report_results_data.players_list[i].selected===true) {
                    records.push($scope.report_results_data.players_list[i].ID);
                }
            }
            
            var gid=$routeParams.gid;
            $http.post('/gt',{op:"resetGameRecords",gid:gid,records:records})
              .success(function(data, status, headers, config) {
                  $scope.show_multi=false;
                  $scope.reportInit(); 
              });
            
        };
        
        $scope.report_leads_download = function() {
            xlsx_download(["First Name","Last Name","Email","Registration Date"],$scope.report_leads);
        };
        
        
        xlsx_download = function(headlines,data) {
            var data_arr=[];
            data_arr[0]=headlines;
            
            var col_c=headlines.length;
            var c=1;
            for(i in data) {
                var row=data[i];

                if(!isFunction(row)) {
                
                    data_arr[c]=[];
                    var k=0;
                    for(j in row) {
                        data_arr[c][k]=row[j];
                        k++;
                        if(k>=col_c)
                            break;
                    }
                }
                c++;
            }
            
            $http.post('/gt',{op:"createXslx",data_arr:data_arr})
            .success(function(data, status, headers, config) {
                window.open(data.link);
            });
        };
        
        throwChart = function(id,data,type,title,width,height,colors,is3D,x_label,y_label,tooltipIsHtml) {
            colors = typeof colors !== 'undefined' ? colors : ['#33ADDF','#E14F4F','#85C7E9','#C01D4A','#C096B8','#002156'];

            is3D = typeof is3D !== 'undefined' ? is3D : true; 
            tooltipIsHtml = typeof tooltipIsHtml !== 'undefined' ? tooltipIsHtml : false;

            x_label = typeof x_label !== 'undefined' ? x_label : ""; 
            y_label = typeof y_label !== 'undefined' ? y_label : ""; 

            var chart = {};
              chart.type = type;
              chart.displayed = false;
              chart.cssStyle = "height:"+height+"; width:"+width+";background-color:#FFFFFF;background-image:none;";  
              chart.data=data;
              chart.options = {
                  "title": title,
                  "isStacked": "true",
                  "fill": 20,
                  "colors":colors,
                  is3D: is3D,
                  "displayExactValues": true,
                  backgroundColor: 'transparent',
                  colorAxis: {colors: ['#E14F4F', '#33ADDF']},
                  "vAxis": {
                      "title": y_label
                  },
                  "hAxis": {
                      "title": x_label
                  },
                  "tooltip": {
                      "isHtml": tooltipIsHtml
                    },
                  bubble: {textStyle: {fontSize: 14}}

              };
              chart.formatters = {};
              $scope[id] = chart;
          };
          
          
        $scope.shareOptionGame = function(gid) {
            //$location.path("/share");
            var t=new Date().getTime();
            var modalInstance = $modal.open({
                templateUrl: '/app/partials/'+skin+'/includes/share_game.html?t='+t,
                keyboard:true,
                //backdrop:'static',
                windowClass: 'loginPopUp',
                controller: shareGameController,
                resolve: {
                    f_data:function () {
                      return {
                          gid:gid
                      };
                    }
                }
            });
            
        };



        $scope.loadAnotherGame = function(gid) {
          if($scope.$$phase || $scope.$root.$$phase) {
                  $location.path("/play/"+gid);
          }
          else {
              $scope.$apply(function () {
                  $location.path("/play/"+gid);
              });
          }

        };
        
        
        $scope.showUserMoreData = function(user_id,start_time) {
            var gid=$routeParams.gid;
            var t=new Date().getTime();
            var modalInstance = $modal.open({
              templateUrl: '/app/partials/'+skin+'/includes/showUserMoreData.html?t='+t,
              keyboard:true,
              //backdrop:'static',
              windowClass: 'loginPopUp',
              controller: function ($scope, $http,  $modalInstance, f_data) {
                    $http.post('/rt',{op:"showUserMoreData",user_id:f_data.user_id,gid:f_data.gid,start_time:f_data.start_time})
                    .success(function(data, status, headers, config) {
                        $scope.data=data;
                    });
              },
              resolve: {
                  f_data:function () {
                    return {
                        user_id:user_id,
                        gid:gid,
                        start_time:start_time
                    };
                  }
              }
            });

            modalInstance.result.then(function (status) {

            }, function () {

            });
        };
         // createGameRR_form :
         
         
         sendFormRR = function(stage) {
              $('.form_stage[stage='+stage+']').fadeOut(500,function(){
                  $(".proccessing_wrap").height($(window).height()*0.9);
                  $(".proccessing_wrap").fadeIn();
              });
              
            $http.post('/op',{op:"gameFormRR",data:$scope.createGameRR})
            .success(function(data, status, headers, config) {
                if(data.status=="ok") {
                    //
            //alert("ok")
                    $http.post('/gt',{op:"gameFormRR",topic:$scope.createGameRR.topic,user_id:data.user_id,c_id:data.c_id,design_id:$scope.createGameRR.colorset})
                    .success(function(data, status, headers, config) {
                        // forward to facebook conversion track...
                        window.location.href="/Convert";
                    });
                }
            });
              
              
         };

         var colorsPool=['#DD7F00','#6FE56F','#56FEAA','#FF75CD','#3044F2','#F04454','#F0C953','#00571E','#CD571E','#9D1EB5','#001131'];
         var randomSet=[shuffle(colorsPool),shuffle(colorsPool)];

         $scope.getRandomBGColor = function(set,index) {
             return randomSet[set][index];
         };

         
        $scope.domains=['Marketing','Tourism','Commerce','Fashion','Food','Tech','Health',"Other"]; 
        $scope.topics=['topic 1','topic 2','topic 3','topic 4','topic 5']; 
        
        
        
//        $scope.domain_topics={
//            'Marketing':[
//                {
//                    name:'topic 1',
//                    id:515
//                },
//                {
//                    name:'topic 2',
//                    id:515
//                },
//                {
//                    name:'topic 3',
//                    id:515
//                }
//            ],
//            'Sales':[
//                {
//                    name:'topic 1',
//                    id:515
//                },
//                {
//                    name:'topic 2',
//                    id:515
//                },
//                {
//                    name:'topic 3',
//                    id:515
//                }
//            ],
//            'Commerce':[
//                {
//                    name:'topic 1',
//                    id:515
//                },
//                {
//                    name:'topic 2',
//                    id:515
//                },
//                {
//                    name:'topic 3',
//                    id:515
//                }
//            ],
//            'Fashion':[
//                {
//                    name:'topic 1',
//                    id:515
//                },
//                {
//                    name:'topic 2',
//                    id:515
//                },
//                {
//                    name:'topic 3',
//                    id:515
//                }
//            ]
//            
//        };
         
        var form_stage=0;
        
        var form_stages_c=5;
        animateBG = function() {
            var b_stage=form_stage-1;
            $('.form_stage[stage='+b_stage+']').fadeOut(500,function(){
                $('.form_stage[stage='+form_stage+']').fadeIn(500);
            });
            

            var per=100/form_stages_c;
            
            var p_t_move=per*form_stage;
            
            $('body').css("background-position",p_t_move+"% top");
            
            
            

        };
        
        $scope.createGameRR={
          domain:"",
          topic:"",
          colorset:""

        };
        
        $scope.notes={};
        
        $scope.createGameRR_form_submit = function(form_no,data) {
            switch(form_no) {
                case 0:
                    $scope.createGameRR.domain=data;
                    if($scope.createGameRR.domain==="")
                        return;
                break;
                case 1:
                    $scope.createGameRR.topic=data;
                    if($scope.createGameRR.topic==="")
                        return;
                break;
                case 2:
                    $scope.createGameRR.colorset=data;
                    if($scope.createGameRR.colorset==="")
                        return;
                break;
                case 3:
                    if(!$scope.createGameRR.fname || !$scope.createGameRR.lname || !$scope.createGameRR.email) {
                        $scope.show_req_details3=true;
                        return;
                    }
                break;
                case 4:
                    $scope.note4="";
                    if(!$scope.createGameRR.password || !$scope.createGameRR.retype) {
                        $scope.show_req_details4=true;
                        return;
                    }
                    
                    if($scope.createGameRR.password!==$scope.createGameRR.retype) {
                        $scope.note4="Password does not match the confirm password.";
                        return;
                    }
                    
                    sendFormRR(4);
                    return;
                break;
            }
            
            form_stage++;
            animateBG();
            
            
        };
        
        $scope.createGameRR_fb_connect = function() {
            form_stage=5;
            animateBG();
            sendFormRR(3);
        };
        
        $scope.createGameRR_form_init = function() {
            $http.post('/op',{op:"loadDomainTopics",domains:$scope.domains})
            .success(function(data, status, headers, config) {
                $scope.domain_topics_keys = Object.keys(data);
                $scope.domain_topics=data;
            });
        
            
            
            form_stages_c=0;
            
            
            $("div.form_stage").each(function(){
                form_stages_c++;
            });
            
            //console.log(form_stages_c);
            
            
            form_stage=0;
            $('.form_stage[stage=0]').show();
        };
        
        

        
        
        ///createGameRR
        
        /////////////////////////////////
        
        
        $scope.selectAll=function(id) {
            $("#"+id).select();
        };
        
        
        $scope.trackable_links=[];
        $scope.share_game_init = function() {
            $scope.ready=false;

            var req=$location.search();
            
            $scope.full_share=req.f?true:false;
            
            var embed='<iframe height="600" border="0" scrolling="no" style="width:100%;max-width:700px;height:600px;border:0;overflow:hidden;" src="http://www.wheeldo.co/games?gid='+$routeParams.gid+'&uid=0&r=embed"></iframe>';
            
            $http.post('/op',{op:"getGame",gid:$routeParams.gid})
            .success(function(data, status, headers, config) {
                $scope.game=data;                
                $scope.result_share=$routeParams.result+' "'+data.name+'" game!';
                $scope.game_src="/games?gid="+data.unique_id+"&uid="+$scope.user.uid+"&hu=1";
                $scope.ready=true;
                
                
                
                
                var title = $routeParams.result+' "'+data.name+'" game!';
                var summary=data.name + " - " + data.full_desc;
                var caption=data.name;
                if($scope.full_share) {
                    title=data.name;
                    summary=data.full_desc;
                    caption="";
                }
                var im_url = data.thumbnail;
                
                // facebook share link:
                $scope.facebook_url = "https://www.facebook.com/dialog/feed?app_id=1511607775728782" + 
                            "&link=" + encodeURIComponent("http://www.wheeldo.co/play?gid="+data.unique_id+"%26r=facebook")+ 
                            "&name=" + encodeURIComponent(title) + 
                            "&caption=" + encodeURIComponent(caption) + 
                            "&description=" + encodeURIComponent(data.full_desc) + 
                            "&picture=" + encodeURIComponent(im_url) +
                            "&redirect_uri=https://www.facebook.com";
                    
                // liknedin share link:
                $scope.linkedin_url ="http://www.linkedin.com/shareArticle?mini=true" +
                        "&url=http://www.wheeldo.co/play?gid="+data.unique_id+"%26r=linkedin"+
                        "&title=" + encodeURIComponent(title)+
                        "&summary=" + encodeURIComponent(summary)  +
                        "&source=http://www.wheeldo.co";
                
                // tweeter share link:
                $scope.tweeter_url ="https://twitter.com/share?"+
                        "url=http://www.wheeldo.co/play?gid="+data.unique_id+"%26r=twitter"+
                        "&text=" + encodeURIComponent(title);
            });
            
            
            
            
            
            
                
                
            
            
            $scope.embed_link=embed;
            $scope.new_game_link="http://www.wheeldo.co/play/"+$routeParams.gid;
            
            $scope.trackable_links.push({name:"Web","link":""});
            $scope.trackable_links.push({name:"Facebook","link":"facebook"});
            $scope.trackable_links.push({name:"Linkedin","link":"linkedin"});
            $scope.trackable_links.push({name:"Twitter","link":"twitter"});
            
            $scope.gid=$routeParams.gid;
            //alert($routeParams.gid)
                        
        };
        
        
        $scope.newTrackableLink = function(){
          var r=prompt("Insert new tracking key:");
          if(r)
            $scope.trackable_links.push({name:r.capitalize(),"link":r});
        };
        

        
        $scope.side_menu =[
            //{"icon":"icon-dashboard","link":"/dashboard","text":"Dashboard"},
            {"icon":"icon-desktop","link":"/console","text":"Games library"},
            {"icon":"icon-group","link":"/my_channels","text":"My channels"},
            {"icon":"icon-cog","link":"/settings","text":"Settings"}
        ];
        
        
        $scope.dashboard_init = function() {
            if(!$scope.user_login) {
                return;
            }
            $http.post('/bl',{op:"getDashboard",gid:$routeParams.gid})
            .success(function(data, status, headers, config) {
                $scope.user_plan=data.user_plan;
                var color="#008C00";
                if($scope.user_plan.use>60) {
                    color="#DAAF00";
                }
                if($scope.user_plan.use>85) {
                    color="#FF0500";
                }

                $scope.plan_fill={'background-color':color,width:$scope.user_plan.use+"%"};

                $scope.ready=true;
                
                
                
                
                
                // use graph:
                var c=0;
                var playsData=[];
                for(i in data.Last30days) {
                    var day=data.Last30days[i];

                    playsData[c]={};
                    playsData[c].c=[];
                    playsData[c].c.push({v:day.date});                  
                    playsData[c].c.push({v:day.plays});
                    playsData[c].c.push({v:day.admins});
                    c++;
                }
                var data={"cols": [
                    {id: "name", label: "Name", type: "string"},
                    {id: "plays", label: "Plays", type: "number"},
                    {id: "no_charge", label: "No charge", type: "number"}
                    
                ]};

                data.rows=playsData;

                throwChart("chart_plays",data,"ColumnChart","Plays (30 days)","100%","400px",["#00B635","#00B6B6"]);
                
                /////////////
                
                
                
                
            });
            
            
            
            
        };

        get_obj = function(obj) {
            var scope_Arr=obj.split(".");
            switch(scope_Arr.length){
                case 1:
                    return $scope[scope_Arr[0]];
                break;
                case 2:
                    return $scope[scope_Arr[0]][scope_Arr[1]];
                break;
                case 3:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]];
                break;
                case 4:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]];
                break;
                case 5:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]];
                break;
                case 6:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]];
                break;
                case 7:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]];
                break;
                case 8:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]][scope_Arr[7]];
                break;
                case 9:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]][scope_Arr[7]][scope_Arr[8]];
                break;
                case 10:
                    return $scope[scope_Arr[0]][scope_Arr[1]][scope_Arr[2]][scope_Arr[3]][scope_Arr[4]][scope_Arr[5]][scope_Arr[6]][scope_Arr[7]][scope_Arr[8]][scope_Arr[9]];
                break;
            }
        };
        
        $scope.addEditor = function(obj) {
            var data=get_obj(obj);
            var t=new Date().getTime();
            var modalInstance = $modal.open({
              templateUrl: '/app/partials/'+skin+'/includes/editor.html?t='+t,
              keyboard:true,
              //backdrop:'static',
              windowClass: 'loginPopUp',
              controller: function($scope, $http,  $modalInstance, f_data){
                  
                    $scope.content=f_data.data;
                  
                    $scope.initCK = function(id) {
                        $( '#' +id).ckeditor();
                        var editor = CKEDITOR.instances[id];
                        editor.on('blur', function(event) {
                            updateEditor();
                        });
                        editor.on('key', function(event) {		// added by Chaim
                            updateEditor();
                        });
                    };

                    updateEditor=function() {
                        $scope.content=CKEDITOR.instances.content.getData();
                    };




                    $scope.close_popup = function() {
                        $modalInstance.close(0);
                    };
                    
                    $scope.save = function() {
                        updateEditor();
                        $modalInstance.close($scope.content);
                    };
              },
              resolve: {
                  f_data:function () {
                    return {
                        data:data
                    };
                  }
              }
            });

            modalInstance.result.then(function (content) {
                applyImageToObject(obj,content);
            }, function () {

            });
        };
        
        
        
        $scope.show_tutorial = function(youtube_id) {
            var t=new Date().getTime();
            var modalInstance = $modal.open({
              templateUrl: '/app/partials/'+skin+'/includes/youtube.html?t='+t,
              keyboard:true,
              //backdrop:'static',
              windowClass: 'videoPopUp',
              controller: function($scope, $http,  $modalInstance, f_data){
                    
                    $scope.frame_src="//www.youtube.com/embed/"+f_data.youtube_id+"?rel=0";


                    $scope.close_popup = function() {
                        $modalInstance.close(0);
                    };
              },
              resolve: {
                  f_data:function () {
                    return {
                        youtube_id:youtube_id
                    };
                  }
              }
            });
        };
        
        
        $scope.op_design = [
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412509853/design_1_zp3uoq.jpg',
                text_color:'#ffffff',
                style:{"color":"#ffffff","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412509853/design_1_zp3uoq.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412510566/design_2_nef5qu.png',
                text_color:'#000000',
                style:{"color":"#000000","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412510566/design_2_nef5qu.png')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412513302/design_3_uritku.jpg',
                text_color:'#ffffff',
                style:{"color":"#ffffff","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412513302/design_3_uritku.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412512558/design_4_ctvm6f.jpg',
                text_color:'#000000',
                style:{"color":"#000000","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412512558/design_4_ctvm6f.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412512556/design_5_r3rpeo.jpg',
                text_color:'#ffffff',
                style:{"color":"#ffffff","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412512556/design_5_r3rpeo.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412513132/design_6_e13dnm.jpg',
                text_color:'#000000',
                style:{"color":"#000000","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412513132/design_6_e13dnm.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412602833/design_7_pdaqar.jpg',
                text_color:'#ffffff',
                style:{"color":"#ffffff","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412602833/design_7_pdaqar.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412602831/design_8_ivdtp6.jpg',
                text_color:'#000000',
                style:{"color":"#000000","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412602831/design_8_ivdtp6.jpg')"}
            },
            {
                background:'http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_700,w_1980/v1412602820/design_9_vfo1xr.jpg',
                text_color:'#ffffff',
                style:{"color":"#ffffff","background-image":"url('http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_120,w_340/v1412602820/design_9_vfo1xr.jpg')"}
            }
            
            
        ];
        
        
        $scope.setDesign = function(design) {
            $scope.new_game.game_design.background=design.background;
            $scope.new_game.game_design.color_4=design.text_color;
        };
        
        $scope.fixHttp = function() {
            console.log(this)
        }
        
               
});
























































var aCtrl = function ($scope,$http,$routeParams,$modal,$location,$templateCache,Services,$navigate,Usertrack,Gamereport,uploaderWLD) {
    $scope.user_view_search="";
    $scope.users=[];
    $scope.user=0;
    
    $scope.show_admin_bar=0;
    $scope.showBar = function(){
        $scope.show_admin_bar=1;
    };
    $scope.hiedBar = function(){
        $scope.show_admin_bar=0;
    };
    
    $scope.search_user_view = function() {
        if($scope.user_view_search) {
            $http.post('/gt',{op:"searchUser",searchKey:$scope.user_view_search})
            .success(function(data, status, headers, config) {
                for(i in data) {
                    $scope.user=i;
                    break;;
                }
                $scope.users=data;
            });
        }
        
    };
    
    $scope.setUserView = function() {
        $http.post('/gt',{op:"setView",user_id:$scope.user})
        .success(function(data, status, headers, config) {
            if(data.status=="ok")
                window.location.reload();
        });  
    };
    
    $scope.resetUserView = function() {
        $http.post('/gt',{op:"resetView"})
        .success(function(data, status, headers, config) {
                window.location.reload();
        }); 
        
    }

    
};


var shareGameController = function ($scope, $http,  $modalInstance, f_data) {
    
    $scope.frame_src="/share_game/"+f_data.gid+"/bla?f=1";
    var h=$(window).height();
    if(h>600)
        h=600;
    $scope.frame_style={height:h+"px"};
    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
    
};


var loadQuestionnairesController = function ($scope, $http,  $modalInstance, f_data, Services) {


    $scope.search = function(searchKey) {
        $scope.search_active=true;
        $http.post('/gt',{op:"searchQuestionnaireFree",searchKey:searchKey})
        .success(function(data, status, headers, config) {
            $scope.questionnaires=data.questionnaires;
            $scope.search_active=false;
        });
    };

    $scope.search();

    $scope.load_questionary = function(index) {
        var questionary=$scope.questionnaires[index];

        $http.post('/gt',{op:"getQuestionnaire",id:questionary.id})
        .success(function(data, status, headers, config) {
            $modalInstance.close(data);
        });


    }



    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

};

var contactUsController = function ($scope, $http,  $modalInstance, f_data, Services) {


    $scope.contact={
        name:"",
        email:"",
        message:""
    };

    $scope.user=f_data.user;
    $scope.user_login=false;
    if($scope.user.ghost!=="1") {
        $scope.user_login=true;
        $scope.contact.name=$scope.user.name;
        $scope.contact.email=$scope.user.email;
    };


    $scope.contackUsSubmit = function() {
        if(!$scope.contact.name || !$scope.contact.email || !$scope.contact.message)
            return;


        $(".form_to_hide").hide();
        $(".processing").show();
        $http.post('/op',{op:"contactUsSend",contact:$scope.contact})
        .success(function(data, status, headers, config) {
            $(".processing").hide();
            $(".thanks").show();
        });
    }




    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

};

var EditEmailController = function ($scope, $http,  $modalInstance, f_data, Services) {


    $scope.email={
        subject:f_data.subject,
        content:f_data.content
    }


    $scope.initCK = function(id) {
        $( '#' +id).ckeditor();
        var editor = CKEDITOR.instances[id];
        editor.on('blur', function(event) {
            updateEditor();
        });
        editor.on('key', function(event) {		// added by Chaim
            updateEditor();
        });
    };

    updateEditor=function() {
        $scope.email.content=CKEDITOR.instances.edit_email_content.getData();
    }




    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
    $scope.save = function() {
        updateEditor();
        var email=this.email;
       $modalInstance.close(email);
    };
};
var uploadImageController = function ($scope, $http,  $modalInstance, f_data, Services) {

    $scope.note = f_data.note;

    $scope.selectFile = function(id) {
      $("#img_upload_input_popup").trigger("click");
    };

    $scope.img_effect=f_data.extra;




    $scope.res_mark=makeid();
    $scope.upload_url=getImageUploadURLService();

    $scope.onGeneralIMGSelect = function($files,form_id) {
        var res_mark=$("#"+form_id).attr("res_mark");
        file_in_proccess=res_mark;
        imgObj=$("#"+form_id).attr("imgObj");

        var file=$files[0];
        var ex=file.name.split(".");
        var ext=ex[ex.length-1].toLowerCase();
        if(ext!="jpg" &&ext!="JPG" &&ext!="jpeg" &&ext!="png" &&ext!="gif" &&ext!="GIF") {
            $(".loader").hide();
            alert("Please upload jpg or png file only!");
            return;
        }

        $scope.fileData=file;
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        var formObj=$("#"+form_id);
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".loadImage").show();
        $("#img_upload_frame_popup").attr("onload","onFileUploadDone('"+file_in_proccess+"')");


//        $("."+imgObj+"_loader_before").hide();
//        $("."+imgObj+"_loader").show();

        formObj.hide();
        $("#waiting_circle_loader").show();
        formObj.submit();
        checkIFImgProcessDoneObject(12);
    return;
  }


  var file_in_proccess=0;
  checkIFImgProcessDoneObject = function(object) {
      if(files_checker.indexOf(""+file_in_proccess)>-1) {
        $http.post('/gt',{op:"getFileData",res_mark:file_in_proccess})
        .success(function(data, status, headers, config) {
            $modalInstance.close(data);
        });
      }
      else {
          setTimeout(checkIFImgProcessDoneObject,300,object);
      }
  }

    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

};

var changePasswordController = function ($scope, $http,  $modalInstance, f_data, Services) {

    $scope.f={
        old_password:"",
        new_password:"",
        retype_password:""
    };

    $scope.resetPassword = function() {
        $scope.notes={};

        if(!$scope.f.old_password||!$scope.f.new_password||!$scope.f.retype_password) {
            $scope.notes.error_05="Please fill all the fields!";
            return;
        }

        if($scope.f.new_password!=$scope.f.retype_password) {
            $scope.notes.error_05="Passwords don't match";
            return;
        }

        $(".password_change").hide();
        $(".processing").show();

        $http.post('/gt',{op:"resetPassword",data:$scope.f})
        .success(function(data, status, headers, config) {
            $(".processing").hide();
            if(data.status=="ok") {
                $(".done").show();
            }
            else {
                $(".password_change").show();
                $scope.notes.error_05="Password error!";
            }


        });

    };

    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

};

var inviteFollowersController = function ($scope, $http,  $modalInstance, f_data, Services) {


    $scope.email={
        subject:"",
        content:'<p>Hello</p>'
    }

    $scope.initCK = function(id) {
        $( '#' +id).ckeditor();
        var editor = CKEDITOR.instances[id];
        editor.on('blur', function(event) {
            $scope.email.content=CKEDITOR.instances.email_content.getData();
        });
        editor.on('key', function(event) {
            $scope.email.content=CKEDITOR.instances.email_content.getData();
        });
    };

    $http.post('/gt',{op:"getChannelUsersCouter",cid:f_data.channel.id})
    .success(function(data, status, headers, config) {
        $scope.channel_users_n=data.n;
    });

    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
    $scope.sendToFollowers = function() {
        if(!$scope.email.subject)
            return;


        $(".form_to_hide").hide();
        $(".processing").show();
        $http.post('/gt',{op:"sendGameInvitations",cid:f_data.channel.id,email:$scope.email,game_link:f_data.game_link,lang:f_data.lang})
        .success(function(data, status, headers, config) {
            $(".processing").hide();
            $(".thanks").show();
        });

        //$modalInstance.close(2);
    };
};

var userController = function ($scope, $http,  $modalInstance, f_data, Services) {
    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
    $scope.logOut = function() {
        $modalInstance.close(2);
    };
};


var forgotPasswordController = function ($scope, $http,  $modalInstance, f_data, Services) {
    $scope.notes={};
     $scope.forgotPasswordSubmit = function() {
      if(this.email) {
            $(".submit").hide();
            $(".wait").show();


            $http.post('/op',{op:"forgotPassword",email:this.email})
            .success(function(data, status, headers, config) {
                if(data.status=="ok") {
                    $(".password_recovery").hide();
                    $(".done").show();
                }
                else {
                    $scope.notes=[];
                    $scope.notes.push(data.error);
                }


                $(".submit").show();
                $(".wait").hide();
            });


      }

    };

    $scope.signClicked = function() {
        $(".sign_in_form").removeClass("cancel_error");
    };

    $scope.back_login = function() {
        $modalInstance.close(2);
    };

    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

    $scope.forgotPassword = function() {
        $modalInstance.close(3);
    };

};

var joinController = function ($scope, $http,  $modalInstance, f_data, Services) {
    $scope.notes={};
    $scope.registerSubmit = function() {
        $scope.notes={};

        if(!this.password) {
            alert("Password must be at least 6 characters!");
            return;
        }

        if(this.password!==this.retype_password) {
            $scope.notes.error_01="Passwords don't match!";
            return;
        }



        regNewUserService(this);
    };

    $scope.joinClicked = function() {
        $(".sign_in_form").removeClass("cancel_error");
    };


    $scope.checkIFEmailExits = function(email) {
      if(email)
        checkEmailService(email,$scope.notes);
    };


    $scope.close = function() {
        $modalInstance.close(1);
    };

    $scope.sign_in = function() {
        $modalInstance.close(2);
    };

    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

};


var loginController = function ($scope, $http,$location,  $modalInstance, f_data, Services) {
    $scope.notes={};
    
    //alert()
    var ghost=f_data.user.ghost;
    if(ghost==="0") {
        $location.path("/console");
        setTimeout(function(){
            $modalInstance.close(0);
        },100);
    }
    
    $scope.signInSubmit = function() {

        if(this.email&&this.password) {
            delete $scope.notes['error_04'];
            //signInUserService(this,$scope.notes);
            $http.post('/op',{op:"signInUser",email:this.email,password:this.password})
            .success(function(data, status, headers, config) {
                if(data.status=="ok") {
                    $location.path("/console");
                    $modalInstance.close(0);
                }
                else {
                    alert("Login faild!");
                }
            });

        }
        else {
            $scope.notes.error_04="Please fill the fields!";
        }

    };

    $scope.signClicked = function() {
        $(".sign_in_form").removeClass("cancel_error");
    };

    $scope.reg = function() {
        $modalInstance.close(2);
    };

    $scope.close_popup = function() {
        $modalInstance.close(0);
    };

    $scope.forgotPassword = function() {
        $modalInstance.close(3);
    };

};
var addUsersController = function ($scope, $http,  $modalInstance, f_data, Services) {




    var cid=f_data.cid;

    var res=getUploadUrlService("/csv_uploader");
    $scope.users_uploader={
       action:res.url,
       hash:res.hash,
       url_notify:res.url_notify
    };



    $scope.onUsersFileSelect = function($files) {
        var t=new Date().getTime();

        var res_mark=$("#users_upload_form").attr("res_mark");
        file_in_proccess=res_mark;
        $(".loader").show();
        var file=$files[0];

        //console.log(file);
        var ex=file.name.split(".");
        var ext=ex[ex.length-1];
        if(ext!="csv"&&ext!="xls"&&ext!="xlsx") {
            $(".loader").hide();
            alert("Please upload CSV or Excel file!");
            return;
        }
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        var formObj=$("#users_upload_form");
        var action=formObj.attr("action");
        var target=formObj.attr("target");


        $(".loadImage").show();
        $("#users_upload_target").attr("onload","onFileUploadDone('"+file_in_proccess+"')");
        formObj.submit();
        checkIFFileProcessDone($("#users_upload_target"));
        return;
    return;
  }


  checkIFFileProcessDone = function() {
      if(files_checker.indexOf(file_in_proccess)>-1) {
          loadUsersData(getFileDataService(file_in_proccess));
      }
      else {
          setTimeout(checkIFFileProcessDone,300,users_upload_target);
      }
  }


  $scope.users=[];
  var users_big_data=[];
  $scope.big_data=false;
  var big_data=false;
  loadUsersData = function(json) {
      $(".loader").hide();
      var l_users=json.length;

      if(l_users>500) {
          big_data=true;

          for(i in json) {
              if(json[i][0]&&json[i][1]&&json[i][2]) {
                users_big_data.push({fname:json[i][0],lname:json[i][1],email:json[i][2]});
              }
          }

          if($scope.$$phase || $scope.$root.$$phase) {
                  $scope.big_data=true;
                  $scope.big_data_length=users_big_data.length;
          }
          else {
              $scope.$apply(function () {
                  $scope.big_data=true;
                  $scope.big_data_length=users_big_data.length;
              });
          }
          return;
      }
      else {
          big_data=false;
          $scope.big_data=false;
      }

      for(i in json) {

            if(json[i][0]&&json[i][1]&&json[i][2]) {
                addNewUser(json[i][0],json[i][1],json[i][2]);
            }
      }
  }


  addNewUser = function(fname,lname,email) {
        var user={
              fname:fname,
              lname:lname,
              email:email
          };



          if($scope.$$phase || $scope.$root.$$phase) {
                  $scope.users.push(user)
          }
          else {
              $scope.$apply(function () {
                  $scope.users.push(user)
              });
          }
    }

    $scope.addNewUser = function() {
        addNewUser("","","");
    }


    $scope.deleteUserRow = function(index) {
        $scope.users.splice(index,1);
    }


    $scope.addUsers = function() {

        $(".wait").show();
        $(".add_users").hide();

        var users=$scope.users;
        if(big_data) {
            users=users_big_data;
        }

        $http.post('/gt',{op:"addUsers",cid:cid,users:users})
        .success(function(data, status, headers, config) {
            $modalInstance.close(1);


            $(".add_users").show();
            $(".wait").hide();
        });
    };


    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
}

function shuffle(array) {
    var counter = array.length, temp, index;

    // While there are elements in the array
    while (counter > 0) {
        // Pick a random index
        index = Math.floor(Math.random() * counter);

        // Decrease counter by 1
        counter--;

        // And swap the last element with it
        temp = array[counter];
        array[counter] = array[index];
        array[index] = temp;
    }

    return array;
}

function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        // chrome/safari
        if ($.browser.webkit) {
            el = el.parentNode;
        } else {
            // firefox/IE
            el = el.offsetParent;
        }
    }
    return { top: _y, left: _x };
}

function isFunction(functionToCheck) {
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}


String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}