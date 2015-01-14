<?php
//var_dump($_SESSION);

function getNextValue($table){
    $query = "SHOW TABLE STATUS LIKE '$table'";
    $results=mysql_query($query);
    if(mysql_errno() != 0) {
        $result['count'] = -1;
        $result['error'] = "Error: ".mysql_error();
    } else {
        $result['count'] = mysql_num_rows($results);
        for($counter=0;$counter<$result['count'];$counter++) {
            $result[$counter] = mysql_fetch_assoc($results);
        }
    }
    return $result[0]['Auto_increment'];
    mysql_close();
}


function createGhostUser() {
    $name="User ".getNextValue("user");
    $_SESSION['user']['name']=$name;
    $_SESSION['user']['photo']="";
    $_SESSION['user']['ID']=getNextValue("user");
    $_SESSION['user']['teamID']=0;
    $_SESSION['user_as_json']=json_encode($_SESSION['user']);
    
}

//unset($_SESSION['login']);
if(!isset($_SESSION['login'])) {
    // create ghost user
    $_SESSION['login']['appConfig']=12;
    createGhostUser();
    
}

$userID=$_SESSION['user']['ID'];



