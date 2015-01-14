app.directive('repeatDone', function() {
    return function(scope, element, attrs) {
        if (scope.$last) { // all are rendered
            scope.$eval(attrs.repeatDone);
        }
    }
});


app.directive('blurSet', function() {
    return function(scope, element, attrs) {
       element.on("blur",function() {
           scope.$eval(attrs.blurSet);
       });
    }
});

app.directive('autoComplete', function($timeout) {
    return function(scope, iElement, iAttrs) {
            iElement.autocomplete({
                source: scope[iAttrs.uiItems],
                minLength: 2,
                focus: function( event, ui ) {
                    var id=$(this).attr("id");
                    //$( "#"+id ).val( ui.item.label );
                    return false;
                },
                select: function(event, ui) {
                    var save_to=$(this).attr("save_to");
                    var id=$(this).attr("id");

                    $( "#"+id ).val( ui.item.label );
                    $( "#"+save_to ).val( ui.item.value );

                    return false;
                }
              
            })
            .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                    return $( "<li>" )
                            .append( "<a>" + item.label + "</a>" )
                            .appendTo( ul );
            };
    };
});



app.directive('wallDone', function() {
  return function(scope, element, attrs) {
    //console.log('ROW: index = ', scope.$index);
    scope.$watch('$last',function(v){
      if (v) {
          setLikes(); 
          setComment();
          setOpenComment();
      }
    });
  };
});


app.directive('style', function($compile) {
  return {
    restrict: 'E',
    link: function postLink(scope, element) {
      if (element.html()) {
        var template = $compile('<style ng-bind-template="' + element.html() + '"></style>');
        element.replaceWith(template(scope));
      }
    }
  };
});



app.directive('tarnslate', function() {
  return function(scope, element, attrs) {
      
            function getTranslate(val, terget_lang) {
                for(i in dictionary) {
                    
                    if((typeof dictionary[i] !== "function") && dictionary[i][or_lang].toLowerCase()==val.toLowerCase()) {
                        //console.log(dictionary[i][terget_lang]);
                        return dictionary[i][terget_lang];
                    }
                }
                return val;
            }
      
            if(lang===or_lang) 
                return;
            var txt=element[0].innerHTML;
            
            
            //console.log(txt);
            //console.log(element);

            element[0].innerHTML=getTranslate(txt, lang);

    };
});


app.directive('contTextBind', function() {
  return function(scope, element, attrs) {
            setTimeout(function(){
                
                var w=element.width();
                var parentIW=element.parent().width();
                var fs=element.css('font-size');
                
                
               // str.split(" ");
                
                
                var a=parentIW/w;
                
                
                var text=element[0].innerHTML;
                
                
                // test how many rows:
                var pe=element.parent();
                
                var text_element='<span class="test_element">abc</span>';
                
                
                pe.prepend(text_element);
                
                var line_h=$(".test_element").height();
                $(".test_element").html("");
                var rows=1;
                var h=line_h;
                for(i in text) {
                    var old_h=h;
                    h=$(".test_element").height();
                    if(old_h!=h)
                        rows++;
                        
                    $(".test_element").append(text[i]);                  
                }
                $(".test_element").remove();
                
                if(rows>3)
                    rows=3;
                pe.addClass("r"+rows);
            },0);
            
            //console.log(element);


    };
});



