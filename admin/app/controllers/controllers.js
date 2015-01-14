var files_checker=[];
onFileUploadDone = function(file_t) {
    files_checker.push(file_t);
};
app.controller('WheeldoAdminController', function ($scope,$filter,$http,$modal,$route,$routeParams,$location,Services) {
    $scope.finish_load="";
    
    $scope.__goto = function(route) {
        $location.path("/"+route); 
    };
    
    $scope.getTimes=function(n){
         return new Array(n);
    };
    
    $scope.init_home_page_design = function() {
        $scope.finish_load="loaded";
        aceDDinit();
        
        $http.post(BASE+'/gt',{op:"getRow",row:"Featured"})
        .success(function(data, status, headers, config) {
            $scope.featured=data;
        });
        
        $http.post(BASE+'/gt',{op:"getRow",row:"Recommended"})
        .success(function(data, status, headers, config) {
            $scope.suggested=data;
        });
        
        $http.post(BASE+'/gt',{op:"getRow",row:"Popular"})
        .success(function(data, status, headers, config) {
            $scope.popular=data;
        });
        
        $http.post(BASE+'/gt',{op:"getGamesForChoose"})
        .success(function(data, status, headers, config) {
            $scope.games=data.games;
        });
        aceFormInit();

        
    };
    
    $scope.init_games_example = function(){
        
        $scope.finish_load="loaded";
        aceDDinit();
        
        $http.post(BASE+'/gt',{op:"getRow",row:"Examples"})
        .success(function(data, status, headers, config) {
            $scope.examples=data;
        });
        
        $http.post(BASE+'/gt',{op:"getGamesForChoose"})
        .success(function(data, status, headers, config) {
            $scope.games=data.games;
        });
        aceFormInit();
    };
    
    $scope.saveRow = function(row,obj) {
        $http.post(BASE+'/gt',{op:"saveRow",row:row,no:$scope[obj].no,game_ids:$scope[obj].game_ids});
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
    
    $scope.setSelect = function(last) {
        if(last)
            $scope.gameSelectInit();
    };
    
    $scope.gameSelectInit = function() {
        setTimeout(function(){
            $(".game_select").chosen();
        
            $(".chosen-container").css({
                width:"100%"
            });  
        },100);
    };
    
    $scope.init_ph = function() {
        var f_w=Math.floor($("#main_scope").width());
        $scope.n_a=new Array(100);
        $scope.set_ph();
    };
    
    $scope.set_ph = function() {
        var f_w=Math.floor($("#main_scope").width());
        var b_w=Math.floor($("#main_scope").width()/$scope.suggested.no)-4;
        
        if(b_w<182)
            b_w=182;
        
        $scope.b_w=b_w;
    }
    
    $scope.edit_game = function(index) {
        
        var game=$scope.games[index];
        console.log(game);
    };
    
    
    
    $scope.toggle_game_approved = function(game_id) {

        var approved=$scope.games[game_id].approved;
        $http.post(BASE+'/gt',{op:"setApprovedGame",game_id:game_id,approved:approved});
        
    };    
    $scope.delete_game = function(index) {
        if(!confirm("Sure?"))
            return;
        $scope.games[index].id
        $http.post(BASE+'/gt',{op:"deleteGame",game_id:$scope.games[index].id});
        
        $scope.games.splice(index,1);
        
    }
    

    
    
    $scope.init_games = function() {
        ace_table_init();
        $http.post(BASE+'/gt',{op:"getGames"})
        .success(function(data, status, headers, config) {
            $scope.predicate = 'id';
            $scope.reverse=true;
            $scope.games=data.games;
        });
        
        $scope.finish_load="loaded";
    };
    
    $scope.init_users = function() {
        ace_table_init();
        aceFormInit();
        
        if($routeParams.query) {
            $scope.searchKeyUsers=$routeParams.query;
            $scope.search_in_users();
        }
            
        $scope.finish_load="loaded";
    };
    
    $scope.search_active=false;
    
    $scope.userSearch={
        dates:"",
        key:""
    };
    
    $scope.users_marked={};
    $scope.show_extra_search=false;
    $scope.search_in_users=function() {
        if($scope.$$phase || $scope.$root.$$phase) {}
        else {$scope.$apply(function () {});}

        var dates=$("#id-date-range-picker-1").val();
        if(!this.searchKeyUsers && !dates)
            return;

        $scope.search_active=true;
        $http.post(BASE+'/gt',{op:"getUsers",dates:dates,searchKeyUsers:this.searchKeyUsers?this.searchKeyUsers:""})
        .success(function(data, status, headers, config) {
            $scope.mk_all=false;
            $scope.users_marked={};
            $scope.search_active=false;
            $scope.users=data.users;
            $scope.show_extra_search=true;
            $scope.channels_filter="0";
            $scope.labels_filter="";
            $scope.channels_list=data.channels;
        });
        
        $http.post(BASE+'/gt',{op:"loadUserLables"})
        .success(function(data, status, headers, config) {
            $scope.label_list=data.labels;
        });
    };
    
    $scope.addLabel = function() {
      var name=prompt("Label name:");
      if(name) {
            $http.post(BASE+'/gt',{op:"addUserLabel",name:name})
            .success(function(data, status, headers, config) {
                $http.post(BASE+'/gt',{op:"loadUserLables"})
                .success(function(data, status, headers, config) {
                    $scope.label_list=data.labels;
                });
            });
      }
    };
    
    $scope.applyLabel = function() {
        
        if($scope.apply_label) {
            
            var ids=[];
            var users=$filter('filter')($scope.users, $scope.channels_filter);
            users=$filter('filter')(users, $scope.labels_filter);
            
            for(i in $scope.users_marked) {
                var mk=$scope.users_marked[i];
                if(mk) {
                    ids.push(users[i].id);
                }
            }

            
            $http.post(BASE+'/gt',{op:"applyLabel",label:$scope.apply_label,ids:ids})
            .success(function(data, status, headers, config) {
                $scope.search_in_users();
            });
            
        }
         
    };
    
    $scope.removeLabel = function(label_index,user_index) {
        var users=$filter('filter')($scope.users, $scope.channels_filter);
        users=$filter('filter')(users, $scope.labels_filter);
        var user=users[user_index];
        var label=user.labels[label_index];
        
        
        
        
        for(i in $scope.users) {
            if($scope.users[i].id==user.id) {
                $scope.users[i].labels.splice(label_index,1);
            }
        }
        
        $http.post(BASE+'/gt',{op:"removeLabel",user_id:user.id,label_index:label_index});
    };
    
    $scope.zeroing_mk_all = function() {


        var users=$filter('filter')($scope.users, $scope.channels_filter);
        users=$filter('filter')(users, $scope.labels_filter);
        //$scope.users_marked={};
        var ids=[];
        for(i in users) {
            ids.push(users[i].id);
        }
        
        
        for(i in $scope.users_marked) {
            //var mk=$scope.users_marked[i];
            var user_id=$scope.users[i].id;
            if(ids.indexOf(user_id)<0) {
                //$scope.users_marked.splice(i,1);
                delete $scope.users_marked[i];
            }
        }
        
        //console.log(ids);
        
        $scope.mk_all=false;
        for(i in $scope.users_marked) {
            $scope.users_marked[i]=false;
        }
        
        $scope.mk_all_changed();
    };
    
    $scope.mk_all=false;
    $scope.mk_all_changed = function() {
        //$scope.users_marked={};
        if($scope.mk_all) {
            for(i in $scope.users_marked) {
                $scope.users_marked[i]=true;
            }
        }
        else {
            for(i in $scope.users_marked) {
                $scope.users_marked[i]=false;
            }
        }
        
        $scope.show_mult_bar();
    }
    
    $scope.mb_show=false;
    $scope.show_mult_bar = function() {
        $scope.mb_show=false;
        $scope.users_marked_c=0;
        for(i in $scope.users_marked) {
            var mk=$scope.users_marked[i];
            if(mk) {
                $scope.users_marked_c++;
                $scope.mb_show=true;
            }
        }
    };
    
     $scope.users_download = function() {
        var users=$filter('filter')($scope.users, $scope.channels_filter);
        users=$filter('filter')(users, $scope.labels_filter);
        
        
        var user_to_file=[];
        
        for(i in users) {
            var user=users[i];
            var us=[];
            us.push(user.id);
            us.push(user.fname);
            us.push(user.lname);
            us.push(user.email);
            us.push(user.clock_time);
            
            var labels_str="";
            var c=0;
            for(j in user.labels) {
                if(c>0) {
                    labels_str+=", ";
                }
                labels_str+=user.labels[j];
                c++;
            }
            us.push(labels_str);
            
            user_to_file.push(us);
        }
        

        xlsx_download(["ID","First Name","Last Name","Email","Registration Date","Labels"],user_to_file);
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

        $http.post(BASE+'/gt',{op:"createXslx",data_arr:data_arr})
        .success(function(data, status, headers, config) {
            window.open(data.link);
        });
    };

    $scope.delete_user = function(index) {
        if(!confirm("Sure?"))
            return;
        $http.post(BASE+'/gt',{op:"deleteUser",user_id:$scope.users[index].id})
        .success(function(data, status, headers, config) {
            $scope.users.splice(index,1);
        });
    }
    
    $scope.edit_user = function(index) {
        var user_id=$scope.users[index].id;
        $location.path("/edit_user/"+user_id+"/"+$scope.searchKeyUsers);
    }
    
    $scope.load_user_data = function() {
        var user_id=$routeParams.user_id;
        $http.post(BASE+'/gt',{op:"loadUserData",user_id:user_id})
        .success(function(data, status, headers, config) {
            $scope.user=data;
        });
    }
    
    $scope.return_to_users = function() {
        if($routeParams.query) {
            $location.path("/users/"+$routeParams.query);
        }
        else {
            $location.path("/users");
        }
        
    }
    
    $scope.save_user = function() {
        $http.post(BASE+'/gt',{op:"saveUserData",user_data:this.user})
        .success(function(data, status, headers, config) {
            $scope.return_to_users()
        });
    }
    
    
    $scope.init_tags = function() {
        aceFormInit();
        
        
        
        $http.post(BASE+'/gt',{op:"getTags"})
        .success(function(data, status, headers, config) {
            //init_tags_select(ace.variable_US_STATES);
            init_tags_select(data);
        });

        
				
    }
    
    $scope.init_questions = function() {
        $scope.finish_load="loaded";
    }
    
    init_tags_select = function(source) {
        var tag_input = $('#game_tags');
        if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) ) 
        {

                tag_input.tag(
                  {
                        placeholder:tag_input.attr('placeholder'),
                        //enable typeahead by specifying the source array
                        source: source //defined in ace.js >> ace.enable_search_ahead
                  }
                );
        }
        else {

                //display a textarea for old IE, because it doesn't support this plugin or another one I tried!
                tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
                //$('#form-field-tags').autosize({append: "\n"});
        }
    };
    var activeTab='tag';
    $scope.activeTab = function(active_tab) {
        activeTab=active_tab;
    }
    
    $scope.search_active=false;
    $scope.search_in_quiz = function() {

        $scope.checked_q=0;
        $scope.questions_checked=[];
        $scope.multiple_actions=false;
        
        switch(activeTab){
            case "tag":
                searchTags();
            break;
            case "creator":
                searchCreator(this.searchKeyCreator);
            break;
            case "free":
                searchFree(this.searchKeyQuestions);
            break;
            
        }

        
        
        
    }
    
    
    $scope.questions_checked=[];
    searchCreator = function(searchKey) {

        if(!searchKey)
            return;
        
        $scope.search_active=true;
        $http.post(BASE+'/gt',{op:"searchQCreator",searchKey:searchKey})
        .success(function(data, status, headers, config) {
            $scope.questions=data.questions;
            $scope.search_active=false;
        });
    }
    
    searchFree = function(searchKey) {

        if(!searchKey)
            return;
        
        $scope.search_active=true;
        $http.post(BASE+'/gt',{op:"searchQFree",searchKey:searchKey})
        .success(function(data, status, headers, config) {
            $scope.questions=data.questions;
            $scope.search_active=false;
        });
    }
    
    searchTags = function() {
        if($("#game_tags").val()=="")
            return;
        $scope.search_active=true;
        $http.post(BASE+'/gt',{op:"searchTags",tags:$("#game_tags").val()})
        .success(function(data, status, headers, config) {
            $scope.questions=data.questions;
            $scope.search_active=false;
        });
    }
    
    $scope.count_q = function() {
        $http.post(BASE+'/gt',{op:"countQ"})
        .success(function(data, status, headers, config) {
            $scope.all_q_no=data.all_q_no;
        });
    }
    
    $scope.edit_quiz = function (index) {
        var q_id=$scope.questions[index].id;
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: BASE+'/app/partials/includes/edit_quiz_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'editQuiz',
          controller: editQuizCtrl,
          resolve: {
              f_data:function () {
                return {
                    q_id:q_id
                }
              }
          }
        });
        
        modalInstance.result.then(function (status) {
            if(status==1)
                $scope.search_in_quiz();
            
        }, function () {

        });
    }
    
    
    $scope.checked_q=0;
    $scope.multiple_actions=false;
    $scope.checkMultipleShow = function() {
        $scope.checked_q=0;

            for(i in $scope.questions_checked) {
               if($scope.questions_checked[i]=="1")
                   $scope.checked_q++;
            }

            if($scope.checked_q>0)
                $scope.multiple_actions=true;
            else
                $scope.multiple_actions=false;

        
    }
    
    $scope.createNewQuestionnaire = function() {
        var ids=[];
        for(i in $scope.questions_checked) {
            if($scope.questions_checked[i]=="1") {
                ids.push($scope.questions[i].id);
            }
        }

        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: BASE+'/app/partials/includes/createNewQuestionnaire_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'editQuiz',
          controller: createNewQuestionnaireCtrl,
          resolve: {
              f_data:function () {
                return {
                    ids:ids
                }
              }
          }
        });
        
        modalInstance.result.then(function (status) {

            
        }, function () {

        });
        
        
    }
    
    $scope.addToQuestionnaire = function() {
        var ids=[];
        for(i in $scope.questions_checked) {
            if($scope.questions_checked[i]=="1") {
                ids.push($scope.questions[i].id);
            }
        }
        
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: BASE+'/app/partials/includes/addToQuestionnaire_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'editQuiz',
          controller: addToQuestionnaireCtrl,
          resolve: {
              f_data:function () {
                return {
                    ids:ids
                }
              }
          }
        });
    }
    
    $scope.count_qie = function() {
        $http.post(BASE+'/gt',{op:"countQie",tags:$("#game_tags").val()})
        .success(function(data, status, headers, config) {
            $scope.all_qie_no=data.all_qie_no;
        });
    }
    
    $scope.init_questionnaire_tags = function() {
        aceFormInit();
        $http.post(BASE+'/gt',{op:"getTagsQuestionnaire"})
        .success(function(data, status, headers, config) {
            init_tags_select(data);
        });			
    }
    
    $scope.search_in_questionnaire = function() {
        switch(activeTab){
            case "tag":
                searchQuestionnaireTags();
            break;
            case "free":
                searchQuestionnaireFree(this.searchKeyQuestionnaire);
            break;
            
        }
    }
    
    searchQuestionnaireTags = function() {
        if($("#game_tags").val()=="")
            return;
        $scope.search_active=true;
        $http.post(BASE+'/gt',{op:"searchQuestionnaireTags",tags:$("#game_tags").val()})
        .success(function(data, status, headers, config) {
            $scope.questionnaires=data.questionnaires;
            $scope.search_active=false;
        });
    }
    
    searchQuestionnaireFree = function(searchKey) {
        if(!searchKey)
            return;
        
        $scope.search_active=true;
        $http.post(BASE+'/gt',{op:"searchQuestionnaireFree",searchKey:searchKey})
        .success(function(data, status, headers, config) {
            $scope.questionnaires=data.questionnaires;
            $scope.search_active=false;
        });
    }
    
    $scope.edit_questionnaire = function(index) {
        var q_id=$scope.questionnaires[index].id;
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: BASE+'/app/partials/includes/edit_questionnaire_popup.html?t='+t,
          keyboard:true,
          //backdrop:'static',
          windowClass: 'editQuiz',
          controller: editQuestionnaireCtrl,
          resolve: {
              f_data:function () {
                return {
                    q_id:q_id
                }
              }
          }
        });
        
        modalInstance.result.then(function (status) {
            if(status==1)
                $scope.search_in_questionnaire();
            
        }, function () {

        });
    };
    
    
    $scope.loadCampaigns = function() {
        $http.post(BASE+'/gt',{op:"getCampaigns"})
        .success(function(data, status, headers, config) {
            $scope.campaigns=data.campaigns;
        });
    };
    
    
    $scope.addNewCampaign = function() {
      var name=prompt("Campaign name"); 
      if(name) {
        $http.post(BASE+'/gt',{op:"createCampaign",name:name})
        .success(function(data, status, headers, config) {
            alert("New Campaign added successfully!");
            $scope.campaigns=data.campaigns;
        });
      }   
    };
    
    $scope.delete_campaign = function(id) {
        if(!confirm("Sure?"))
            return;
        $http.post(BASE+'/gt',{op:"delete_campaign",id:id})
        .success(function(data, status, headers, config) {
            $scope.campaigns=data.campaigns;
        });  
    };
    
    $scope.uploadUrls={};
    $scope.edit_campaign = function(id) {
        $http.post(BASE+'/gt',{op:"getCampaign",id:id})
        .success(function(data, status, headers, config) {
            $scope.campaign_edit=data;
            $http.post(BASE+'/gt',{op:"getImageUploadURL",id:id})
            .success(function(data, status, headers, config) {
                $scope.uploadUrls['img_upload_frame1']=data;
                $scope.uploadUrls['img_upload_frame1'].res_mark=makeid();
            }); 
        });  
    };
    
    $scope.addNewCampaignPage = function(campaign_id) {
        if($scope.$$phase || $scope.$root.$$phase) {
                $scope.page_campaign_edit=new_campaign_page;
                $scope.page_campaign_edit.campaign=campaign_id;
                if(!init_page_edit_done)
                    init_page_edit();
        }
        else {
            $scope.$apply(function () {
                $scope.page_campaign_edit=new_campaign_page;
                $scope.page_campaign_edit.campaign=campaign_id;
                if(!init_page_edit_done)
                    init_page_edit();
            });
        }
        
    };
    
    var init_page_edit_done=false;
    init_page_edit = function() {
        $('#id-input-file-1 , #id-input-file-2').ace_file_input({
                no_file:'No File ...',
                btn_choose:'Choose',
                btn_change:'Change',
                droppable:false,
                onchange:null,
                thumbnail:false //| true | large
                //whitelist:'gif|png|jpg|jpeg'
                //blacklist:'exe|php'
                //onchange:''
                //
        });
        $('#id-input-file-3').ace_file_input({
                style:'well',
                btn_choose:'Drop images here or click to choose',
                btn_change:null,
                no_icon:'icon-cloud-upload',
                droppable:true,
                thumbnail:'small'//large | fit
                //,icon_remove:null//set null, to hide remove/reset button
                /**,before_change:function(files, dropped) {
                        //Check an example below
                        //or examples/file-upload.html
                        return true;
                }*/
                /**,before_remove : function() {
                        return true;
                }*/
                ,
                preview_error : function(filename, error_code) {
                        //name of the file that failed
                        //error_code values
                        //1 = 'FILE_LOAD_FAILED',
                        //2 = 'IMAGE_LOAD_FAILED',
                        //3 = 'THUMBNAIL_FAILED'
                        //alert(error_code);
                }

        }).on('change', function(){
                //console.log($(this).data('ace_input_files'));
                //console.log($(this).data('ace_input_method'));
        });
        
        $('#editor1').ace_wysiwyg({
		toolbar:
		[
			'font',
			null,
			'fontSize',
			null,
			{name:'bold', className:'btn-info'},
			{name:'italic', className:'btn-info'},
			{name:'strikethrough', className:'btn-info'},
			{name:'underline', className:'btn-info'},
			null,
			{name:'insertunorderedlist', className:'btn-success'},
			{name:'insertorderedlist', className:'btn-success'},
			{name:'outdent', className:'btn-purple'},
			{name:'indent', className:'btn-purple'},
			null,
			{name:'justifyleft', className:'btn-primary'},
			{name:'justifycenter', className:'btn-primary'},
			{name:'justifyright', className:'btn-primary'},
			{name:'justifyfull', className:'btn-inverse'},
			null,
			{name:'createLink', className:'btn-pink'},
			{name:'unlink', className:'btn-pink'},
			null,
			{name:'insertImage', className:'btn-success'},
			null,
			'foreColor',
			null,
			{name:'undo', className:'btn-grey'},
			{name:'redo', className:'btn-grey'}
		],
		'wysiwyg': {
			fileUploadError: showErrorAlert
		}
	}).prev().addClass('wysiwyg-style2');
        init_page_edit_done=true;
    };
    
    
    
    var new_campaign_page = {
        id:0,
        campaign:0,
        title:"New Campaign Page Title",
        desc:"",
        image:"",
        game:""
    };
    
    $scope.edit_campaign_page = function(index) {
        
        $scope.page_campaign_edit=$scope.campaign_edit.pages[index];
        $("#editor1").html($scope.page_campaign_edit.desc);
        if(!init_page_edit_done)
            init_page_edit();
    };
    
    $scope.delete_campaign_page = function(index) {
        if(!confirm("Sure?"))
            return;
        var id=$scope.campaign_edit.pages[index].id;
        $http.post(BASE+'/gt',{op:"deleteCampaignPage",id:id});
        $scope.campaign_edit.pages.splice(index,1);
    };
    
    var file_in_proccess;
    var ux_object;
    $scope.onIMGSelect = function($files,form_id) {
        var formObj=$("#"+form_id);
        var file=$files[0];
        
        
        if(! (/\.(jpe?g|png|gif|bmp|jpg)$/i).test(file.name) ){
            alert("Images only!");
            return false;
        }
        
        var res_mark=formObj.attr("res_mark");
        var target=formObj.attr("target");
        ux_object=form_id;
        file_in_proccess=res_mark;
        $("."+form_id+"_form").hide();
        $("."+form_id).show();
        uploadInProcess=true;
        $("#"+target).attr("onload","onFileUploadDone('"+file_in_proccess+"')");
        formObj.submit();
        checkIFImgProcessDone();

        return;
    };
    
    getFileDataService = function(res_mark) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST', BASE+'/gt', false);  // `false` makes the request synchronous
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
    
    checkIFImgProcessDone = function() {
        if(files_checker.indexOf(""+file_in_proccess)>-1) {
            var res=getFileDataService(file_in_proccess);

            $("."+ux_object+"_form").show();
            $("."+ux_object).hide();
            
            $http.post(BASE+'/gt',{op:"getImageUploadURL"})
            .success(function(data, status, headers, config) {
                $scope.uploadUrls['img_upload_frame1']=data;
                $scope.uploadUrls['img_upload_frame1'].res_mark=makeid();
            });

            if(typeof res.img == 'undefined' || !res.img.length) {
                  alert('We\'re sorry, but your request could not be processed. Please try uploading this file again.');
            }
            else {
                  imgUploadDone(res);
            }
        }
        else {
            setTimeout(checkIFImgProcessDone,300);
        }
    };
    
    imgUploadDone = function(res) {
        $scope.page_campaign_edit.image=res.img;
    };
    
    
    $scope.save_campaign_page = function() {
        var e=$("#editor1");
        $scope.page_campaign_edit.desc=e.html();
        
        
        
        $http.post(BASE+'/gt',{op:"page_campaign_save",data:$scope.page_campaign_edit})
        .success(function(data, status, headers, config) {
            $scope.page_campaign_edit=false;
            $scope.campaign_edit=data;
            $http.post(BASE+'/gt',{op:"getImageUploadURL"})
            .success(function(data, status, headers, config) {
                $scope.uploadUrls['img_upload_frame1']=data;
                $scope.uploadUrls['img_upload_frame1'].res_mark=makeid();
            }); 
        });
        //console.log($scope.page_campaign_edit);
    };        
    
    $scope.loadGame = function() {
        
    };
    
    
    $scope.saveCampaign = function() {
        $http.post(BASE+'/gt',{
            op:"saveCampaign",
            name:this.campaign_edit.campaign.name,
            id:this.campaign_edit.campaign.id
        })
        .success(function(data, status, headers, config) {
            $scope.campaigns=data.campaigns;
        });
        
        
    };
    
    $scope.setPageCloseValue = function(page_id,close_val) {
        $http.post(BASE+'/gt',{
            op:"saveCloseValue",
            page_id:page_id,
            close_val:close_val
        });
    };
    
    
    makeDataReadyToSend = function(editData) {
        var editDataJson=JSON.stringify(editData);
        editDataJson=editDataJson.replace(/'/g,"\\\"");
        editDataJson=editDataJson.replace(/&/g,"___amp___");
        return editDataJson;
    };
    
    

    
});


var editQuestionnaireCtrl = function ($scope, $http,  $modalInstance, f_data, Services) {
    

    $http.post(BASE+'/gt',{op:"loadQuestionnaire",q_id:f_data.q_id})
    .success(function(data, status, headers, config) {
        $scope.questionnaire=data;
    });
    
    $scope.del_question = function(index) {
        $scope.questionnaire.questions.splice(index,1);
    }
    
    
    $scope.saveEditQuestionnaire = function() {
        $http.post(BASE+'/gt',{op:"saveEditQuestionnaire",q_id:f_data.q_id,questionnaire:$scope.questionnaire})
        .success(function(data, status, headers, config) {
            $modalInstance.close(1);
        });
    }


    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
    
    
}

var addToQuestionnaireCtrl = function ($scope, $http,  $modalInstance, f_data, Services) {
    
    $scope.c_q=f_data.ids.length;
    
    
    $scope.loadQuestionnaires = function() {
        $http.post(BASE+'/gt',{op:"loadQuestionnaires"})
        .success(function(data, status, headers, config) {
            $scope.questionnaires=data;
        });
    }
    
    $scope.initChoosenSelect = function() {
        setTimeout(function(){
           $(".chosen-select").chosen(); 
            $('#chosen-multiple-style').on('click', function(e){
                    var target = $(e.target).find('input[type=radio]');
                    var which = parseInt(target.val());
                    if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
                     else $('#form-field-select-4').removeClass('tag-input-style');
            });  
        },200)
       
    }
    
    $scope.set_questionnaire_id = function() {
        
    }
    
    $scope.addToQuestionnaire = function() {
        var questionnaire_id=$("#questionnaire_id").val();
        $http.post(BASE+'/gt',{
            op:"addToQuestionnaire",
            ids:f_data.ids,
            questionnaire_id:questionnaire_id
        })
        .success(function(data, status, headers, config) {
            $modalInstance.close(1);
        });
    };
    
    
    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
}


var createNewQuestionnaireCtrl = function ($scope, $http,  $modalInstance, f_data, Services) {
    
    $scope.c_q=f_data.ids.length;
    var t=new Date().getTime();
    $scope.questionnaire = {
        name:"",
        tags:"",
        public_q:"1",
        private_code:t 
    }

    
    $scope.createQuestionnaire = function() {
        if(!$scope.questionnaire.name)
            return;
        
        $http.post(BASE+'/gt',{
            op:"createQuestionnaire",
            ids:f_data.ids,
            questionnaire:$scope.questionnaire
        })
        .success(function(data, status, headers, config) {
            $modalInstance.close(1);
        });
    }
    
    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
}


var editQuizCtrl = function ($scope, $http,  $modalInstance, f_data, Services) {
    q_id=f_data.q_id;
    
    
    $scope.load_question = function() {
        $http.post(BASE+'/gt',{op:"loadQ",q_id:q_id})
        .success(function(data, status, headers, config) {
            $scope.q=data.q;
            aceFormInit()
        });
    }
    
    $scope.set_answer = function(index) {
        for(i in $scope.q.answers) {
            $scope.q.answers[i].right=0;
        }
        $scope.q.answers[index].right=1;
    }
    
    $scope.del_answer =function(index) {
        $scope.q.answers.splice(index,1);
    }
    
    $scope.load_question();
    
    $scope.save_questions = function() {
        $http.post(BASE+'/gt',{
            op:"saveQ",
            q_id:q_id,
            question:this.q.question,
            answers:this.q.answers,
            tags:this.q.tags,
            rank:this.q.rank
        })
        .success(function(data, status, headers, config) {
            $modalInstance.close(1);
        });
        
        
    }
    
    $scope.addAnswer = function() {
        $scope.q.answers.push({
            id:0,
            question_id:q_id,
            content:"",
            right:0
        });
    }
    
    $scope.close_popup = function() {
        $modalInstance.close(0);
    };
}


if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
         for (var i = (start || 0), j = this.length; i < j; i++) {
             if (this[i] === obj) { return i; }
         }
         return -1;
    }
}

function ace_table_init(){
    $.ajax({
        url: BASE+'/assets/js/jquery.dataTables.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/jquery.dataTables.bootstrap.js',
        dataType: "script",
        async:false
    });
    
    
    
    

    jQuery(function($) {
        var oTable1 = $('#sample-table-2').dataTable( {
        "aoColumns": [
      { "bSortable": false },
      null, null,null, null, null,
          { "bSortable": false }
        ] } );


        $('table th input:checkbox').on('click' , function(){
                var that = this;
                $(this).closest('table').find('tr > td:first-child input:checkbox')
                .each(function(){
                        this.checked = that.checked;
                        $(this).closest('tr').toggleClass('selected');
                });

        });


        $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
        function tooltip_placement(context, source) {
                var $source = $(source);
                var $parent = $source.closest('table')
                var off1 = $parent.offset();
                var w1 = $parent.width();

                var off2 = $source.offset();
                var w2 = $source.width();

                if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
                return 'left';
        }
})
}

function aceFormInit() {
    $.ajax({
        url: BASE+'/assets/js/jquery-ui-1.10.3.custom.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/jquery.ui.touch-punch.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/uncompressed/chosen.jquery.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/fuelux/fuelux.spinner.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/date-time/bootstrap-datepicker.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/date-time/bootstrap-timepicker.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/date-time/moment.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/date-time/daterangepicker.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/bootstrap-colorpicker.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/jquery.knob.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/jquery.autosize.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/jquery.inputlimiter.1.3.1.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/jquery.maskedinput.min.js',
        dataType: "script",
        async:false
    });
    $.ajax({
        url: BASE+'/assets/js/uncompressed/bootstrap-tag.js',
        dataType: "script",
        async:false
    });
    
    
    jQuery(function($) {
            $('#id-disable-check').on('click', function() {
                    var inp = $('#form-input-readonly').get(0);
                    if(inp.hasAttribute('disabled')) {
                            inp.setAttribute('readonly' , 'true');
                            inp.removeAttribute('disabled');
                            inp.value="This text field is readonly!";
                    }
                    else {
                            inp.setAttribute('disabled' , 'disabled');
                            inp.removeAttribute('readonly');
                            inp.value="This text field is disabled!";
                    }
            });


            $(".chosen-select").chosen(); 
            $('#chosen-multiple-style').on('click', function(e){
                    var target = $(e.target).find('input[type=radio]');
                    var which = parseInt(target.val());
                    if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
                     else $('#form-field-select-4').removeClass('tag-input-style');
            });


            $('[data-rel=tooltip]').tooltip({container:'body'});
            $('[data-rel=popover]').popover({container:'body'});

            $('textarea[class*=autosize]').autosize({append: "\n"});
            $('textarea.limited').inputlimiter({
                    remText: '%n character%s remaining...',
                    limitText: 'max allowed : %n.'
            });

            $.mask.definitions['~']='[+-]';
            $('.input-mask-date').mask('99/99/9999');
            $('.input-mask-phone').mask('(999) 999-9999');
            $('.input-mask-eyescript').mask('~9.99 ~9.99 999');
            $(".input-mask-product").mask("a*-999-a999",{placeholder:" ",completed:function(){alert("You typed the following: "+this.val());}});



            $( "#input-size-slider" ).css('width','200px').slider({
                    value:1,
                    range: "min",
                    min: 1,
                    max: 8,
                    step: 1,
                    slide: function( event, ui ) {
                            var sizing = ['', 'input-sm', 'input-lg', 'input-mini', 'input-small', 'input-medium', 'input-large', 'input-xlarge', 'input-xxlarge'];
                            var val = parseInt(ui.value);
                            $('#form-field-4').attr('class', sizing[val]).val('.'+sizing[val]);
                    }
            });

            $( "#input-span-slider" ).slider({
                    value:1,
                    range: "min",
                    min: 1,
                    max: 12,
                    step: 1,
                    slide: function( event, ui ) {
                            var val = parseInt(ui.value);
                            $('#form-field-5').attr('class', 'col-xs-'+val).val('.col-xs-'+val);
                    }
            });


            $( "#slider-range" ).css('height','200px').slider({
                    orientation: "vertical",
                    range: true,
                    min: 0,
                    max: 100,
                    values: [ 17, 67 ],
                    slide: function( event, ui ) {
                            var val = ui.values[$(ui.handle).index()-1]+"";

                            if(! ui.handle.firstChild ) {
                                    $(ui.handle).append("<div class='tooltip right in' style='display:none;left:16px;top:-6px;'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>");
                            }
                            $(ui.handle.firstChild).show().children().eq(1).text(val);
                    }
            }).find('a').on('blur', function(){
                    $(this.firstChild).hide();
            });

            $( "#slider-range-max" ).slider({
                    range: "max",
                    min: 1,
                    max: 10,
                    value: 2
            });

            $( "#eq > span" ).css({width:'90%', 'float':'left', margin:'15px'}).each(function() {
                    // read initial values from markup and remove that
                    var value = parseInt( $( this ).text(), 10 );
                    $( this ).empty().slider({
                            value: value,
                            range: "min",
                            animate: true

                    });
            });


            $('#id-input-file-1 , #id-input-file-2').ace_file_input({
                    no_file:'No File ...',
                    btn_choose:'Choose',
                    btn_change:'Change',
                    droppable:false,
                    onchange:null,
                    thumbnail:false //| true | large
                    //whitelist:'gif|png|jpg|jpeg'
                    //blacklist:'exe|php'
                    //onchange:''
                    //
            });

            $('#id-input-file-3').ace_file_input({
                    style:'well',
                    btn_choose:'Drop files here or click to choose',
                    btn_change:null,
                    no_icon:'icon-cloud-upload',
                    droppable:true,
                    thumbnail:'small'//large | fit
                    //,icon_remove:null//set null, to hide remove/reset button
                    /**,before_change:function(files, dropped) {
                            //Check an example below
                            //or examples/file-upload.html
                            return true;
                    }*/
                    /**,before_remove : function() {
                            return true;
                    }*/
                    ,
                    preview_error : function(filename, error_code) {
                            //name of the file that failed
                            //error_code values
                            //1 = 'FILE_LOAD_FAILED',
                            //2 = 'IMAGE_LOAD_FAILED',
                            //3 = 'THUMBNAIL_FAILED'
                            //alert(error_code);
                    }

            }).on('change', function(){
                    //console.log($(this).data('ace_input_files'));
                    //console.log($(this).data('ace_input_method'));
            });


            //dynamically change allowed formats by changing before_change callback function
            $('#id-file-format').removeAttr('checked').on('change', function() {
                    var before_change
                    var btn_choose
                    var no_icon
                    if(this.checked) {
                            btn_choose = "Drop images here or click to choose";
                            no_icon = "icon-picture";
                            before_change = function(files, dropped) {
                                    var allowed_files = [];
                                    for(var i = 0 ; i < files.length; i++) {
                                            var file = files[i];
                                            if(typeof file === "string") {
                                                    //IE8 and browsers that don't support File Object
                                                    if(! (/\.(jpe?g|png|gif|bmp)$/i).test(file) ) return false;
                                            }
                                            else {
                                                    var type = $.trim(file.type);
                                                    if( ( type.length > 0 && ! (/^image\/(jpe?g|png|gif|bmp)$/i).test(type) )
                                                                    || ( type.length == 0 && ! (/\.(jpe?g|png|gif|bmp)$/i).test(file.name) )//for android's default browser which gives an empty string for file.type
                                                            ) continue;//not an image so don't keep this file
                                            }

                                            allowed_files.push(file);
                                    }
                                    if(allowed_files.length == 0) return false;

                                    return allowed_files;
                            }
                    }
                    else {
                            btn_choose = "Drop files here or click to choose";
                            no_icon = "icon-cloud-upload";
                            before_change = function(files, dropped) {
                                    return files;
                            }
                    }
                    var file_input = $('#id-input-file-3');
                    file_input.ace_file_input('update_settings', {'before_change':before_change, 'btn_choose': btn_choose, 'no_icon':no_icon})
                    file_input.ace_file_input('reset_input');
            });




            $('#spinner1').ace_spinner({value:0,min:0,max:200,step:10, btn_up_class:'btn-info' , btn_down_class:'teamEditChangebtn-info'})
            .on('change', function(){
                    //alert(this.value)
            });
            $('#spinner2').ace_spinner({value:0,min:0,max:10000,step:100, touch_spinner: true, icon_up:'icon-caret-up', icon_down:'icon-caret-down'});
            $('#spinner3').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'icon-plus smaller-75', icon_down:'icon-minus smaller-75', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});



            $('.date-picker').datepicker({autoclose:true}).next().on(ace.click_event, function(){
                    $(this).prev().focus();
            });
            $('input[name=date-range-picker]').daterangepicker().prev().on(ace.click_event, function(){
                    $(this).next().focus();
            });

            $('#timepicker1').timepicker({
                    minuteStep: 5,
                    showSeconds: false,
                    showMeridian: false
            }).next().on(ace.click_event, function(){
                    $(this).prev().focus();
            });

            $('#colorpicker1').colorpicker();
            $('#simple-colorpicker-1').ace_colorpicker();


            $(".knob").knob();


            //we could just set the data-provide="tag" of the element inside HTML, but IE8 fails!
            var tag_input = $('#form-field-tags');
            if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) ) 
            {
                    tag_input.tag(
                      {
                            placeholder:tag_input.attr('placeholder'),
                            //enable typeahead by specifying the source array
                            source: ace.variable_US_STATES,//defined in ace.js >> ace.enable_search_ahead
                      }
                    );
            }
            else {
                    //display a textarea for old IE, because it doesn't support this plugin or another one I tried!
                    tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
                    //$('#form-field-tags').autosize({append: "\n"});
            }




            /////////
            $('#modal-form input[type=file]').ace_file_input({
                    style:'well',
                    btn_choose:'Drop files here or click to choose',
                    btn_change:null,
                    no_icon:'icon-cloud-upload',
                    droppable:true,
                    thumbnail:'large'
            })

            //chosen plugin inside a modal will have a zero width because the select element is originally hidden
            //and its width cannot be determined.
            //so we set the width after modal is show
            $('#modal-form').on('shown.bs.modal', function () {
                    $(this).find('.chosen-container').each(function(){
                            $(this).find('a:first-child').css('width' , '210px');
                            $(this).find('.chosen-drop').css('width' , '210px');
                            $(this).find('.chosen-search input').css('width' , '200px');
                    });
            })
            /**
            //or you can activate the chosen plugin after modal is shown
            //this way select element becomes visible with dimensions and chosen works as expected
            $('#modal-form').on('shown', function () {
                    $(this).find('.modal-chosen').chosen();
            })
            */

    });
}

function aceDDinit() {
    
    $.ajax({
        url: BASE+'/assets/js/jquery-ui-1.10.3.custom.min.js',
        dataType: "script",
        async:false
    });
    
    
    $('#simple-colorpicker-1').ace_colorpicker({pull_right:true}).on('change', function(){
            var color_class = $(this).find('option:selected').data('class');
            var new_class = 'widget-header';
            if(color_class != 'default')  new_class += ' header-color-'+color_class;
            $(this).closest('.widget-header').attr('class', new_class);
    });


    // scrollables
    $('.slim-scroll').each(function () {
            var $this = $(this);
            $this.slimScroll({
                    height: $this.data('height') || 100,
                    railVisible:true
            });
    });

    /**$('.widget-box').on('ace.widget.settings' , function(e) {
            e.preventDefault();
    });*/



    // Portlets (boxes)
    $('.widget-container-span').sortable({
        connectWith: '.widget-container-span',
                items:'> .widget-box',
                opacity:0.8,
                revert:true,
                forceHelperSize:true,
                placeholder: 'widget-placeholder',
                forcePlaceholderSize:true,
                tolerance:'pointer'
    });
}

function aceDashboardInit() {

                
    // assets/js/jquery.slimscroll.min.js
    // assets/js/jquery.easy-pie-chart.min.js
    // assets/js/jquery.sparkline.min.js
    // assets/js/flot/jquery.flot.min.js
    // assets/js/flot/jquery.flot.pie.min.js
    // assets/js/flot/jquery.flot.resize.min.js

    $.ajax({
        url: BASE+'/assets/js/jquery.ui.touch-punch.min.js',
        dataType: "script",
        async:false
    });
    
    $.ajax({
        url: BASE+'/assets/js/jquery.slimscroll.min.js',
        dataType: "script",
        async:false
    });
    
    $.ajax({
        url: BASE+'/assets/js/jquery.easy-pie-chart.min.js',
        dataType: "script",
        async:false
    });
    
    $.ajax({
        url: BASE+'/assets/js/jquery.sparkline.min.js',
        dataType: "script",
        async:false
    });
    
    $.ajax({
        url: BASE+'/assets/js/flot/jquery.flot.min.js',
        dataType: "script",
        async:false
    });
    
    $.ajax({
        url: BASE+'/assets/js/flot/jquery.flot.pie.min.js',
        dataType: "script",
        async:false
    });
    
    $.ajax({
        url: BASE+'/assets/js/flot/jquery.flot.resize.min.js',
        dataType: "script",
        async:false
    });

    
    
    
    
    jQuery(function($) {
            $('.easy-pie-chart.percentage').each(function(){
                    var $box = $(this).closest('.infobox');
                    var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
                    var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
                    var size = parseInt($(this).data('size')) || 50;
                    $(this).easyPieChart({
                            barColor: barColor,
                            trackColor: trackColor,
                            scaleColor: false,
                            lineCap: 'butt',
                            lineWidth: parseInt(size/10),
                            animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
                            size: size
                    });
            })

            $('.sparkline').each(function(){
                    var $box = $(this).closest('.infobox');
                    var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
                    $(this).sparkline('html', {tagValuesAttribute:'data-values', type: 'bar', barColor: barColor , chartRangeMin:$(this).data('min') || 0} );
            });




      var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
      var data = [
            { label: "social networks",  data: 38.7, color: "#68BC31"},
            { label: "search engines",  data: 24.5, color: "#2091CF"},
            { label: "ad campaigns",  data: 8.2, color: "#AF4E96"},
            { label: "direct traffic",  data: 18.6, color: "#DA5430"},
            { label: "other",  data: 10, color: "#FEE074"}
      ]
      function drawPieChart(placeholder, data, position) {
              $.plot(placeholder, data, {
                    series: {
                            pie: {
                                    show: true,
                                    tilt:0.8,
                                    highlight: {
                                            opacity: 0.25
                                    },
                                    stroke: {
                                            color: '#fff',
                                            width: 2
                                    },
                                    startAngle: 2
                            }
                    },
                    legend: {
                            show: true,
                            position: position || "ne", 
                            labelBoxBorderColor: null,
                            margin:[-30,15]
                    }
                    ,
                    grid: {
                            hoverable: true,
                            clickable: true
                    }
             })
     }
     drawPieChart(placeholder, data);

     /**
     we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
     so that's not needed actually.
     */
     placeholder.data('chart', data);
     placeholder.data('draw', drawPieChart);



      var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
      var previousPoint = null;

      placeholder.on('plothover', function (event, pos, item) {
            if(item) {
                    if (previousPoint != item.seriesIndex) {
                            previousPoint = item.seriesIndex;
                            var tip = item.series['label'] + " : " + item.series['percent']+'%';
                            $tooltip.show().children(0).text(tip);
                    }
                    $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
            } else {
                    $tooltip.hide();
                    previousPoint = null;
            }

     });






            var d1 = [];
            for (var i = 0; i < Math.PI * 2; i += 0.5) {
                    d1.push([i, Math.sin(i)]);
            }

            var d2 = [];
            for (var i = 0; i < Math.PI * 2; i += 0.5) {
                    d2.push([i, Math.cos(i)]);
            }

            var d3 = [];
            for (var i = 0; i < Math.PI * 2; i += 0.2) {
                    d3.push([i, Math.tan(i)]);
            }


            var sales_charts = $('#sales-charts').css({'width':'100%' , 'height':'220px'});
            $.plot("#sales-charts", [
                    { label: "Domains", data: d1 },
                    { label: "Hosting", data: d2 },
                    { label: "Services", data: d3 }
            ], {
                    hoverable: true,
                    shadowSize: 0,
                    series: {
                            lines: { show: true },
                            points: { show: true }
                    },
                    xaxis: {
                            tickLength: 0
                    },
                    yaxis: {
                            ticks: 10,
                            min: -2,
                            max: 2,
                            tickDecimals: 3
                    },
                    grid: {
                            backgroundColor: { colors: [ "#fff", "#fff" ] },
                            borderWidth: 1,
                            borderColor:'#555'
                    }
            });


            $('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
            function tooltip_placement(context, source) {
                    var $source = $(source);
                    var $parent = $source.closest('.tab-content')
                    var off1 = $parent.offset();
                    var w1 = $parent.width();

                    var off2 = $source.offset();
                    var w2 = $source.width();

                    if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
                    return 'left';
            }


            $('.dialogs,.comments').slimScroll({
                    height: '300px'
        });


            //Android's default browser somehow is confused when tapping on label which will lead to dragging the task
            //so disable dragging when clicking on label
            var agent = navigator.userAgent.toLowerCase();
            if("ontouchstart" in document && /applewebkit/.test(agent) && /android/.test(agent))
              $('#tasks').on('touchstart', function(e){
                    var li = $(e.target).closest('#tasks li');
                    if(li.length == 0)return;
                    var label = li.find('label.inline').get(0);
                    if(label == e.target || $.contains(label, e.target)) e.stopImmediatePropagation() ;
            });

            $('#tasks').sortable({
                    opacity:0.8,
                    revert:true,
                    forceHelperSize:true,
                    placeholder: 'draggable-placeholder',
                    forcePlaceholderSize:true,
                    tolerance:'pointer',
                    stop: function( event, ui ) {//just for Chrome!!!! so that dropdowns on items don't appear below other items after being moved
                            $(ui.item).css('z-index', 'auto');
                    }
                    }
            );
            $('#tasks').disableSelection();
            $('#tasks input:checkbox').removeAttr('checked').on('click', function(){
                    if(this.checked) $(this).closest('li').addClass('selected');
                    else $(this).closest('li').removeClass('selected');
            });


    })
}


function looksLikeMail(str) {
    var lastAtPos = str.lastIndexOf('@');
    var lastDotPos = str.lastIndexOf('.');
    return (lastAtPos < lastDotPos && lastAtPos > 0 && str.indexOf('@@') == -1 && lastDotPos > 2 && (str.length - lastDotPos) > 2);
}


function openSelectOrg() {
    $(".select_org").toggle();
}

function select_org(){
    var org_selection=$("#org_selection").val();
    setVirtualOrg(org_selection);
    window.location.reload();
}


function teamEditChange(value) {
   var scope = angular.element($("#edit_game_wrap")).scope();
   scope.teamEditApply(value); 
}

function userEditChange(value) {
   var scope = angular.element($("#edit_game_wrap")).scope();
   scope.userEditApply(value); 
}

function setMenu(menu) {
    $(".nav-list li").removeClass("active");
    $("."+menu).addClass("active");
}



String.prototype.hashCode = function() {
  var hash = 0, i, chr, len;
  if (this.length == 0) return hash;
  for (i = 0, len = this.length; i < len; i++) {
    chr   = this.charCodeAt(i);
    hash  = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};

function makeid()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 12; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function showErrorAlert (reason, detail) {
        var msg='';
        if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
        else {
                console.log("error uploading file", reason, detail);
        }
        $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+ 
         '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
}


function isFunction(functionToCheck) {
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}
