<?php

class api extends dbop {
    
    
    public function users($request_data) {
        $result=array();
        try
        {
            $uid=$request_data['uid'];
            $email=$request_data['email'];
            $user=$this->selectAssocRow("users","WHERE `unique_link`='{$uid}' AND `email`='{$email}'");
            if($user) {
                unset($user['id']);
                unset($user['password']);
                unset($user['activation_link']);
                unset($user['active']);
                $result['status']="ok";
                $result['user']=$user;
            }
            else {
                $result['status']="faild";
                $result['error']="authenticate faild";
            }
            return $result;

        }
        catch (Exception $e)
        {
         throw new Exception( 'Something really gone wrong', 0, $e);
        }
    }
    
    
    public function authenticate() {
        
    }
}