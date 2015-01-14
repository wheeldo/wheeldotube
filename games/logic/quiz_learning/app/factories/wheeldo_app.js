app.factory('wheeldo_app', function($window) {
    var scope = angular.element(document.getElementById("root_scope")).scope();
    
    loadGameEndService = function() {
        $.ajax({
                type: "post",
                url: BASE+'/gt',
                dataType:"json",               
                data:{
                  op:"loadGameEnd"
                },
                success: function(data, textStatus, jqXHR) {
                        if(scope.$$phase || scope.$root.$$phase) {
                                scope.end_data=data;
                        }
                        else {
                            scope.$apply(function () {
                                scope.end_data=data;
                            });
                        }
                        
                }
        });
    }
    
    
    get5050Service_old = function(q_id) {
       $.ajax({
                type: "post",
                url: BASE+'/gt',
                dataType:"json",               
                data:{
                  op:"get5050",
                  q_id:q_id
                },
                success: function(data, textStatus, jqXHR) {
                    if(data.status=="ok") {
                        for(i in data.dis){
                            var d=data.dis[i];
                            if(typeof d !== "function")
                                $("#ans_"+d).addClass("disabled");
                        }
                        
                        
                        var scope = angular.element(document.getElementById("lifes")).scope();
                        if(scope.$$phase || scope.$root.$$phase) {
                                scope.game_quiz_user.use_5050=1;
                        }
                        else {
                            scope.$apply(function () {
                                scope.game_quiz_user.use_5050=1;
                            });
                        }
                        
                    }
                }
        }); 
    };
    
    loadQuestions = function() {
        $.ajax({
                type: "post",
                url: BASE+'/gt',
                dataType:"json",               
                data:{
                  op:"loadQuestions"
                },
                success: function(data, textStatus, jqXHR) {
                    if(scope.$$phase || scope.$root.$$phase) {
                            scope.questions=data;
                    }
                    else {
                        scope.$apply(function () {
                            scope.questions=data;
                        });
                    }
                    
                    if(scope.questions.length===0 || game_quiz_user.strikes>=3) {
                        goEnd();
                    }
                    
                    
                    loadded_parts.question=true;
                    setLoader();
                }
        }); 
    };
    
    getDictionary = function(lang) {
        $.ajax({
                type: "post",
                url: BASE+'/gt',
                dataType:"json",
                data:{
                  op:"getDictionary",
                  lang:lang
                },
                success: function(data, textStatus, jqXHR) {
                    if(scope.$$phase || scope.$root.$$phase) {
                            scope.dictionary=data;
                    }
                    else {
                        scope.$apply(function () {
                            scope.dictionary=data;
                        });
                    }
                    
                    loadded_parts.dictionary=true;
                    setLoader();
                }
        }); 
    };

    
    getPairs = function(level) {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST',  BASE+'/gt', false);  // `false` makes the request synchronous
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send("op=getPairs&level="+level);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    };
    
   
});