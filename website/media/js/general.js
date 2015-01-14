var new_hire_link="solutions/newhire";
var peer_learning_link="solutions/plearning";
var knowledge_gaps_link="solutions/kgaps";

var m = 1.28;

$(document).ready(function() {
    
        setSolutionsLinks();

	/*oSu = $('.hSignUpMout');*/
	oSu = $('.signUpBinder');

	if (oSu.length > 0) {

		oSu.click(function() {

			fn_doModalSignUp();

		});
	}

	oCu = $('.hCsendMout');

	if (oCu.length > 0) {

		fn_doContactUs(oCu, '#footerWrapperR');

	}

	formAutoActivate();

	oPc = $('#playerContainer');
	/*
	 * merry-go-round
	 */
	oRp = $('#hpPlayer');
	/*
	 var _RpData = [{"content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='images/merry-go-round/01_list_c.png' alt='Bike'></a><a class='caption' href='#'>xxx</a></div>",
	 "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"},
	 {"content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='images/merry-go-round/01_MOUNTAINEER_choose_c.png' alt='Paint'></a><a class='caption' href='#'>Sample Carousel Pic Goes Here And The Best Part is that...</a></div>",
	 "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"},
	 {"content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='images/merry-go-round/Amdocs-CES9-Trivia-Screen_c.png' alt='Tunnel'></a><a class='caption' href='#'>Sample Carousel Pic Goes Here And The Best Part is that...</a></div>",
	 "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"},
	 {"content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='images/merry-go-round/suprise box_c.png' alt='Bike'></a><a class='caption' href='#'>Sample Carousel Pic Goes Here And The Best Part is that...</a></div>",
	 "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"},
	 {"content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='images/merry-go-round/The-Daily-Insight-Wall-Voting_c.png' alt='Paint'></a><a class='caption' href='#'>Sample Carousel Pic Goes Here And The Best Part is that...</a></div>",
	 "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"},
	 {"content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='images/merry-go-round/Voter-Serious-Example_c.png' alt='Paint'></a><a class='caption' href='#'>Sample Carousel Pic Goes Here And The Best Part is that...</a></div>",
	 "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"}];
	 */
	var _RpData = [
                {
                    "content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='website/media/images/merry-go-round/hp1.jpg' alt='a'></a></div>",
                    "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"
                }
                ];

	if (oRp.length > 0) {

		oRp.agile_carousel({
			/*carousel_data: data,
			 */carousel_data: _RpData,
			carousel_outer_height: 326,
			carousel_height: 228,
			slide_height: 248,
			carousel_outer_width: 555,
			slide_width: 396,
			transition_type: "fade",
			timer: 4000
		});

	}
        
        
        
        
        
        var benefits_player = $('#benefits_screen');
        
        var benefits_data =  [
                {
                    "content": "<div class='slide_inner'><a class='photo_link' href='#'><img class='photo' src='website/media/images/merry-go-round/hpd1.jpg' alt='a'></a></div>",
                    "content_button": "<div class='thumb'><img src='images/f2_thumb.jpg' alt='bike is nice'></div><p>Agile Carousel Place Holder</p>"
                }
            ];
        
        if (benefits_player.length > 0) {

		benefits_player.agile_carousel({
			/*carousel_data: data,
			 */carousel_data: benefits_data,
			carousel_outer_height: 326,
			carousel_height: 228,
			slide_height: 248,
			carousel_outer_width: 555,
			slide_width: 396,
			transition_type: "fade",
			timer: 4000
		});

	}


});

var send_pr = false;

function formAutoActivate() {

	$(".formSubmit").unbind("click");
	$(".formSubmit").click(function() {

		var form_class = $(this).attr("form_class");
		formElement = $("." + form_class);

		var error = false;
		var formKeys = [];
		var formValues = [];
		var formNames = [];
		var c = 0;

		formElement.find("input").each(function(index) {
			var field_name = $(this).attr("field_name");
			var value = $(this).val();
			var name = $(this).attr("name");
			formKeys[c] = name;
			formValues[c] = value;
			formNames[c] = field_name;

			if ($(this).attr("req") && value == "") {
				$(this).css("background-color", "#FFBABA");
				error = true;
			}
			else {
				$(this).css("background-color", "#FFFFFF");
			}

//            if($(this).attr("req") && value=="") {
//                if(!error) {
//                    error=""+field_name+", ";
//                }
//                else
//                   error+=""+field_name+", ";
//            }

			c++;
		});

		formElement.find("textarea").each(function(index) {
			var field_name = $(this).attr("field_name");
			var value = $(this).val();
			var name = $(this).attr("name");
			formKeys[c] = name;
			formValues[c] = value;
			formNames[c] = field_name;

			if ($(this).attr("req") && value == "") {
				$(this).css("background-color", "#FFBABA");
				error = true;
			}
			else {
				$(this).css("background-color", "#FFFFFF");
			}

//            if($(this).attr("req") && value=="") {
//                if(!error) {
//                    error=""+field_name+", ";
//                }
//                else
//                   error+=""+field_name+", ";
//            }

			c++;
		});

		if (error) {
			//alert("Please insert: "+error);
			return;
		}
		else {

			var bObj = $(".register");
			var orHtml = bObj.html();

                        $(".showAfter").html("Please wait...");
                        $(".hideAfter").hide();
                        $(".showAfter").show();
                        
                        
			if (send_pr)
				return;
			send_pr = true;


			$.ajax({
				type: "post",
				url: BASE_DIR+'ws/register',
				dataType: "json",
				data: {
					form_name: form_class,
					formKeys: formKeys,
					formValues: formValues,
					formNames: formNames
				},
				error: function(jqXHR, textStatus, errorThrown) {
				},
				success: function(data, textStatus, jqXHR) {
                                        send_pr = false;
                                        if(data.state==true) {
                                            clearFormData(form_class);
                                            $(".showAfter").html(data.msg);
                                            
                                            $(".hideAfter").hide();
                                            $(".showAfter").show();
                                            
                                            setTimeout(
                                            function() {
							$('#bg_blue_1').dialog('close');
                                                        $(".showAfter").hide();
                                                        $(".hideAfter").show();
                                                        $(".msg").html("");
                                                        $(".msg").hide();
                                                        
						}, 
                                                8000);
                                        }
                                        else {
                                            $(".showAfter").hide();
                                            $(".hideAfter").show();
                                            $(".msg").html(data.msg);
                                            $(".msg").fadeIn("slow");
                                        }

					
//					send_pr = false;
//					
//
//					bObj.html(orHtml);
//                                        fn_EndSignUp(data);
//
//					o = $('#bg_blue_1');
					if (o.length > 0) {
//						setTimeout(function() {
//							$('#bg_blue_1').dialog('close');
//						}, 2000);
						//o.dialog('close');
					}
				}
			});
		}

	});
}

function clearFormData(className) {

	formElement = $("." + className);
	formElement.find("input").val("");
	formElement.find("textarea").val("");

}

function fn_doModalSignUp() {

	o = $('#bg_blue_1');

	if (o.length < 1) {
		return;
	}

	o.dialog({modal: true, width: 600, height: 400, closeText: "x", dialogClass: 'fixed-dialog',
		open: function(event, ui) {
//$("#bg_blue_1").css("position", "fixed");
		},
		close: function(event, ui) {
//$("#bg_blue_1").css("position", "absolute");
		}
	});


//	o.dialog({
//		open: function( event, ui ) {
//			$("#bg_blue_1").css("position","fixed");
//			alert("test")
//		},
//		close: function(event, ui) {
//		}
//	});

	o.on("dialogclose", function(event, ui) {
		//console.log('ppp');
	});
}

function fn_doModalClose() {
	o = $('#bg_blue_1');
	o.dialog("close");
}


sendOpen=true;
function fn_doContactUs(sendBtn, wrapperE) {

	oW = $(wrapperE);

	if (oW.length < 1) {
		return;
	}

	sendBtn.unbind("click");
	sendBtn.click(function() {
            if(!sendOpen)
                return;
		var form_class = $(this).attr("form_class");

		formElement = $("." + form_class);
		if (formElement.length < 1) {
			return;
		}
		var error = false;
		var formKeys = [];
		var formValues = [];
		var formNames = [];
		var c = 0;

		formElement.find("input").each(function(index) {

			var field_name = $(this).attr("field_name");
			var value = $(this).val();
			var name = $(this).attr("name");
			formKeys[c] = name;
			formValues[c] = value;
			formNames[c] = field_name;

			if ($(this).attr("req") && value == "") {

				$(this).css("background-color", "#FFBABA");
				error = true;

			} else {

				$(this).css("background-color", "#767676");

			}
			/*
			 if ($(this).attr("req") && value == "") {
			 if (!error) {
			 error = "" + field_name + ", ";
			 }
			 else
			 error += "" + field_name + ", ";
			 }*/
			c++;
		});

		formElement.find("textarea").each(function(index) {

			var field_name = $(this).attr("field_name");
			var value = $(this).val();
			var name = $(this).attr("name");
			formKeys[c] = name;
			formValues[c] = value;
			formNames[c] = field_name;

			if ($(this).attr("req") && value == "") {
				$(this).css("background-color", "#FFBABA");
				error = true;
			}
			else {
				$(this).css("background-color", "#767676");
			}
			c++;

		});

		if (error) {
			//alert("Please insert: "+error);
			return;
		} else {
                        $(".hCsendMout").find("div").css("background-image","url("+BASE_DIR+"media/img/wait_contact.gif)");
                        sendOpen=false;
			$.ajax({
				type: "post",
				url: '/op',
				dataType: "json",
				data: {
                                        op:'contactUsSend',
                                        contact:{
                                            name:$("#fName").val(),
                                            email:$("#email").val(),
                                            message:$("#oname").val()
                                        }
                                        
				},
				error: function(jqXHR, textStatus, errorThrown) {
				},
				success: function(data, textStatus, jqXHR) {
			//clear here the fields
                                    clearFormData(form_class);
                                    $(".hCsendMout").find("div").css("background-image","url("+BASE_DIR+"media/img/hCsendMout.png)");
                                    sendOpen=true;
                                    alert("Your message has been sent successfully!");
                                    
				}
			});
		}

	});

}

function fn_EndSignUp() {

	o = arguments[0];
        
        alert("end");

	if (o) {

		oo = $('section.register').find("article");

		if (o.state) {

			oo.html(o.msg);
			fn_hideForm();

		} else {
			/*
			 * Message not sent.
			 */
			if (o.action == 'sayHey') {
				oo.html(o.msg);
				fn_hideForm();
				console.log('=user does exist=' + o.action);
			} else {
				console.log('=user does not exist, but error occured=' + o.action);
			}
		}

	}

}

function fn_hideForm() {
        alert("hide form");
        $(".hideAfter").hide();

        return;
	o = $('.input_block');
	if (o.length > 0) {
		o.hide();
	}

}


function setSolutionsLinks() {
//    $(".new_hire_link").click(function(){
//       window.location.href=new_hire_link; 
//    });
//    
//    $(".peer_learning_link").click(function(){
//       window.location.href=peer_learning_link; 
//    });
//    
//    $(".knowledge_gaps_link").click(function(){
//       window.location.href=knowledge_gaps_link; 
//    });
    
    
}


var hp_c=0;
function hpDevicesStart(sets_array) {
    
    var img_array=sets_array[hp_c];
    $(".mac").css({"background-image":"url('"+img_array[0]+"')"});
    $(".ipad").css({"background-image":"url('"+img_array[1]+"')"});
    $(".iphone").css({"background-image":"url('"+img_array[2]+"')"});
    
    hp_c++;
    
    if(!sets_array[hp_c])
        hp_c=0;
    
    setTimeout(hpDevicesStart,5000,sets_array);
    
}



function Usertrack () {
    this.__event = function(event,event_id) {
        //$http.post('/ut',{op:"event",event:event,event_id:event_id});
        
        $.ajax({
                type: "post",
                url: '/ut',
                dataType: "json",
                data: {
                        op:'event',
                        event:event,
                        event_id:event_id

                }
        }); 
    };
}
var track = new Usertrack();