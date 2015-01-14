//This handles retrieving data and is used by controllers. 3 options (server, factory, provider) with 
//each doing the same thing just structuring the functions/data differently.
app.service('Services', ['$http',function () {
        

     loadService = function(op) {
            var res;
            var request = new XMLHttpRequest();
            request.open('POST',  BASE+'/gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op="+op);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
            return res;
     };

     
     
}]);