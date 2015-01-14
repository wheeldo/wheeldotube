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

    // comment
}

function closeUploader() {
    uploaderElement.remove();
}

// This function is called by the game iframe to automatically subscribe the user to the channel when he presses "go"
function gameChannelSubscribe() {
	var scope = angular.element(document.getElementById("content_wrap")).scope();
	scope.subscribeChannle(scope.game.cid, 1);		// force subscribed state
}
/////////////////////////////////////


// This function is called by the game iframe to automatically hide frame data when he presses "go" on mobile device
function hideFrameData() {
	var scope = angular.element(document.getElementById("content_wrap")).scope();
	scope.hideFrameData();
}

function showFrameData() {
	var scope = angular.element(document.getElementById("content_wrap")).scope();
	scope.showFrameData();
}
/////////////////////////////////////

/////////////////////////////////////
function loadAnotherGame(gid) {
    var scope = angular.element(document.getElementById("content_wrap")).scope();
    scope.loadAnotherGame(gid);
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

sm_toggle = function() {
  $("body").toggleClass("sm_open");
};

fixLayout = function(m_w,m_h) {
    return;
    //console.log($("body").hasClass("sm_open"))
    if($("body").hasClass("sm_open")) {
        if(!__isMobile) {
            var wrapper_left=(m_w-1045)/2+260;
            $(".sm_open #content_wrap .wrapper").css({
                "margin-left":wrapper_left+"px",
                "max-width": "830px"
            });
        }
    }
    else {
        $("#content_wrap .wrapper").css({
            "margin":"0px auto",
            "max-width": "1045px"
        });
    }




    return;
    var search_w=m_w-200;
    search_w-=$("#top-user").width();
    search_w-=210;
    if(search_w>580) {
        search_w=580;
    }
    $(".search_top").css({
        width:search_w+"px"
    });

    var c_h=m_h-(35+8+20);
    $("#content").css({
        "min-height":c_h+"px"
    });

    $("#play_content").css({
        "min-height":c_h+"px"
    });

    $("#side_menu").css({
        "min-height":(m_h-35-16)+"px"
    });


    if(m_w<1000) {
        $("body").removeClass("sm_open");
    }
    else {
        $("body").addClass("sm_open");
    }



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


img_upload_done = function() {
//   var scope = angular.element(document.getElementById("content_wrap")).scope();
//   console.log(scope.imgUploadDone);
   //scope.$$childHead.imgUploadDone();

   var scope = angular.element(document.getElementById("root_scope")).scope();
   for(var cs = scope.$$childHead; cs; cs = cs.$$nextSibling) {
        // cs is child scope
        if(cs.imgUploadDone)
            cs.imgUploadDone();
   }



};




onFileUploadDone = function(file_t) {
    files_checker.push(file_t);
};


String.prototype.hashCode = function(){
    if (Array.prototype.reduce){
        return this.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
    }
    var hash = 0;
    if (this.length === 0) return hash;
    for (var i = 0; i < this.length; i++) {
        var character  = this.charCodeAt(i);
        hash  = ((hash<<5)-hash)+character;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}


function makeid()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 12; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}


if(__isMobile) {
    $(window).scroll(function() {
       if($(window).scrollTop() + $(window).height() +10 >= $(document).height()) {
           if(!no_frame)
                $("#mobile_footer").slideDown();
       }
       else {
           if(!no_frame)
            $("#mobile_footer").fadeOut();
       }
    });
}