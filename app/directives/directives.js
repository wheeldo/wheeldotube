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

app.directive('uiColorpicker', function() {
    return {
        restrict: 'E',
        require: 'ngModel',
        scope: false,
        replace: true,
        template: "<span><input class='input-small' /></span>",
        link: function(scope, element, attrs, ngModel) {
            var input = element.find('input');
            var options = angular.extend({
                color: ngModel.$viewValue,
                change: function(color) {
                    scope.$apply(function() {
                      ngModel.$setViewValue(color.toHexString());
                    });
                }
            }, scope.$eval(attrs.options));

            ngModel.$render = function() {
              input.spectrum('set', ngModel.$viewValue || '');
            };

            input.spectrum(options);
        }
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


app.directive('formAutofillFix', function() {
  return function(scope, elem, attrs) {
    // Fixes Chrome bug: https://groups.google.com/forum/#!topic/angular/6NlucSskQjY
    elem.prop('method', 'POST');

    // Fix autofill issues where Angular doesn't know about autofilled inputs
    if(attrs.ngSubmit) {
      setTimeout(function() {
        elem.unbind('submit').submit(function(e) {
          e.preventDefault();
          elem.find('input, textarea, select').trigger('input').trigger('change').trigger('keydown');
          scope.$apply(attrs.ngSubmit);
        });
      }, 0);
    }
  };
});


app.directive('twitter', [
    function() {
        return {
            link: function(scope, element, attr) {
                setTimeout(function() {
                    twttr.widgets.createShareButton(
                        attr.url,
                        element[0],
                        function(el) {}, {
                            style:{width:"200px"},
                            count: 'none',
                            text: attr.text
                        }
                    );
                },100);
            }
        }
    }
]);



app.directive('ngTap', function() {
  var isTouchDevice = !!("ontouchstart" in window);
  return function(scope, elm, attrs) {

    if (isTouchDevice) {
      var tapping = false;
      elm.bind('touchstart', function() { tapping = true; });
      elm.bind('touchmove', function() { tapping = false; });
      elm.bind('touchend', function() {
        tapping && scope.$apply(attrs.ngTap);
      });
    } else {
      elm.bind('click', function() {
        scope.$apply(attrs.ngTap);
      });
    }
  };
});


app.directive('ngIf', function() {
    return {
        link: function(scope, element, attrs) {
            if(scope.$eval(attrs.ngIf)) {
                
                // remove '<div ng-if...></div>'
                //element.replaceWith(element.children())
            } else {
                element.replaceWith(' ')
            }
        }
    }
});



app.directive('ngFixHttp', function() {
  return function(scope, element, attrs) {
      setTimeout(function(){
            var id=element[0].id;
            element[0].addEventListener("change", 
                function(event) {
                    var val=document.getElementById(id).value;

                    if(val.indexOf("http://")<0 && val.indexOf("https://")<0) {
                        element[0].value="http://"+val;
                    }
                }
            );
      },100);
        
    };
});
