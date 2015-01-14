var uploaderElement;
$(document).ready(function() {

});
function setUserFunc() {
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

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
         for (var i = (start || 0), j = this.length; i < j; i++) {
             if (this[i] === obj) { return i; }
         }
         return -1;
    }
}

makeDataReadyToSend = function(editData) {
    var editDataJson=JSON.stringify(editData);
    editDataJson=editDataJson.replace(/'/g,"\\\"");
    editDataJson=editDataJson.replace(/&/g,"___amp___");
    return editDataJson;
};


_gel = function(id) {
    return document.getElementById(id);
}

function setProcessing() {
    $("form").hide();
    $(".processing").fadeIn("fast");
}

function stopProcessingFaild() {
    $(".processing").hide();
    $("form").show();
}


function stopProcessing() {
    $(".processing").hide();
    $(".thanks").fadeIn("fast");
}


function coundDown(sec) {
    limit_sec=sec;
    ___cd();
}

var limit_sec=0;

___cd = function() {
    
    var scope = angular.element(document.getElementById("root_scope")).scope();
    if(scope.$$phase || scope.$root.$$phase) {
            scope.sec_left=limit_sec;
    }
    else {
        scope.$apply(function () {
            scope.sec_left=limit_sec;
        });
    }  
    
    
    limit_sec--;
    if(limit_sec>=0)
        setTimeout(___cd,1000);
}


fixLayout = function(m_w,m_h) {
    var search_w=m_w-200;
    search_w-=$("#top-user").width();
    search_w-=140;
    if(search_w>580) {
        search_w=580;
    }
    $(".search_top").css({
        width:search_w+"px"
    })
    
    var c_h=m_h-(35+8+20+30);
    $("#content").css({
        "min-height":c_h+"px"
    });
    $("#play_content").css({
        "min-height":c_h+"px"
    });
    
    
    var player_w=700;
    if(m_w<700) {
        player_w=m_w;
    }
    
    player_h=player_w*0.8;
    
    $("#player").css({
        "width":player_w+"px",
        "height":player_h+"px"
    });
    
    game_frame_h=player_h;
    
    $("#game_frame").css({
        "width":player_w+"px",
        height:game_frame_h+"px"
    });
    
    
    

};


var curr_w;
var curr_h;
layoutListener = function() {
    var m_w=$(document).width();
    var m_h=$('html').height();
    if(m_w!=curr_w || m_h!=curr_h) {
        curr_w=m_w;
        curr_h=m_h;
        
        fixLayout(m_w,m_h);
    }
    setTimeout(layoutListener,100);
};

setTimeout(function(){
      layoutListener();      
},300);



