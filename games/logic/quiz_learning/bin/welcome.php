<?
require_once 'top_functions.php';

$check_post=file_get_contents("php://input");


if($check_post) {
    $data = json_decode(file_get_contents("php://input"));
    
    
    //var_dump($data);
    $dbop=new dbop();
    $email=sha1(strtolower($data->email));
    $check=$dbop->selectAssocRow("user","WHERE `appID`='{$data->appID}' AND `empID`='{$data->employeeID}' AND `hashedEmail`='{$email}'");
    
    if($check) {
        $func="getCode";

        if($local) {
            $target="http://my.wheeldo.localhost/APIAD.php";

        }
        else {
            $target="http://my.wheeldo.com/APIAD.php";
        }

        $login_data['key']=1;
        $login_data['login']=1;

        $postdata=array('request' => $func,
                        'function_data[appID]'  => $data->appID,
                        'function_data[userID]'  => $check['user_id']
            );

        $postdata=array_merge($postdata,$login_data);

        $ch = curl_init(); // Init cURL
        curl_setopt($ch, CURLOPT_URL, $target); // Post location
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 1 = Return data, 0 = No return
        curl_setopt($ch, CURLOPT_POST, true); // This is POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); // Add the data to the request
        $o = curl_exec($ch); // Execute the request
        curl_close($ch); // Finish the request. Close it.
        
        
        $res=json_decode($o,true);
        
        
        //echo "socialArena.php?configID=".$data->appID."&token=".$res['code'];
        
        echo "land/".$data->appID."/".$res['code'];
        
        
        
    }
    else {
        echo 0;
    }
    die();
}

$appID=$_GET['appID'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BCM Game :::</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script type="text/javascript" src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="vendor/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
                <script type="text/javascript">
                function loginController($scope,$http) {
                    $scope.appID = <?=$appID?>;
                    $scope.response=false;
                    $scope.response_type="";
                    $scope.response_text="";
                    $scope.alerts="";

                    $scope.init = function() {
                        
                    }
                    
                   validateEmail = function(email){ 
                        var re = /\S+@\S+\.\S+/;
                        return re.test(email); 
                   }
                    
                    $scope.login = function() {
                            $scope.alerts="";
                            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                            var valid=true;
                            if(!validateEmail($("#inputEmail").val())) {
                                $scope.alerts='<div class="alert">Warning! please insert valid email address!</div>';
                                valid=false;
                            }
                            
                            if($("#inputEmployeeID").val()=="") {
                                $scope.alerts+='<div class="alert">Warning! please insert employee ID!</div>';
                                valid=false;
                            }
                            
                            if(!valid)
                                return;
                            
                            $http.post("bin/welcome.php", {
                                 appID:$scope.appID,
                                 email:$("#inputEmail").val(),
                                 employeeID:$("#inputEmployeeID").val(),
                                 
                             }).success(function (data, status, headers, config) {
                                 if(data==0) {
                                    $scope.alerts+='<div class="alert alert-error">Login failed!</div>';  
                                    $scope.alerts+='<div class="alert alert-info">If your login fails, please try your shortened or long email address.</div>';
                                 }
                                 else {
                                     $scope.alerts+='<div class="alert alert-success">You have successfully logged-in!</div>';
                                     window.location.href=data;
                                 }
                             }).error(function (data, status, headers, config) {
                                 
                             });
                    }
                }
                
        </script>
        <style type="text/css">
            body {
                background-color:#414141;
            }
            
            .container {
                margin-top:5%;
                max-width:400px;
                padding-top:10px;
                border:1px solid #AAB300;
                text-align:center;
                -webkit-border-radius: 8px;
                border-radius: 8px; 
                
                background-color:#ffffff;
            }
            
            .container img {
                margin:10px 0px;
            }
            
            .edit_input {
                border: 1px dashed #ABABAB;
                width:250px;
            }
            
            .form-horizontal .control-label {
                width:80px;
                padding-left:20px;
            }
            
            .form-horizontal .controls {
                margin-left:auto;
            }
            
            .alert{
                text-align:left;
            }
        </style>
    </head>
    <body ng-app>
        <div class="container" ng-controller="loginController" ng-init="init()">
                <img src="media/img/banner.png" />
                <form class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label" for="inputEmail">Email</label>
                        <div class="controls">
                            <input type="text" id="inputEmail" name="email" placeholder="user@server.com">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputPassword">Employee ID</label>
                        <div class="controls">
                            <input type="password" id="inputEmployeeID" placeholder="Employee ID">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <button type="button" class="btn" ng-click="login()">Sign in</button>
                        </div>
                    </div>
                </form>
                <div ng-bind-html-unsafe="alerts">
                    
                </div>
        </div>
        <script src='vendor/angularjs-1.0.7/angular.min.js'></script>
        <script src='vendor/angularjs-1.0.7/angular-resource.min.js'></script>
    </body>
</html>