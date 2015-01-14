<?php

class wall extends dbop {
    private $appID;
    
    public function __construct($appID) {
        $this->appID=$appID;
        parent::__construct();
    }
    
    protected function addScore($user_id,$score) {
        return;
        $user=$this->getUser($user_id);
        $fields=array();
        $fields['appID']=$this->appID;
        $fields['userID']=$user_id;
        $fields['userName']=$user['user_name'];;
        $fields['score']=$score;
        
        $checkRowUser=$this->selectAssocRow("scores","WHERE `appID`='{$fields['appID']}' AND `userID`='{$fields['userID']}' ORDER BY `score` DESC ");
//        //$checkRowUser2=$this->selectAssocRow("scores","WHERE `appID`='{$fields['appID']}' AND `teamID`='{$user['user_id']}' AND `userID`='{$fields['userID']}'");
//        if($checkRowUser2)
//            $checkRowUser=$checkRowUser2;
            
        
        if($checkRowUser) {
            //echo "userScoreExist";
            $score+=intval($checkRowUser['score']);
            
            //echo "<br>".$checkRowUser['scoreID']."--".$score."--";
            $this->updateDB("scores", array("score"=>$score), $checkRowUser['id']);
            //echo mysql_error();
        }
        else {
            //echo "userScoreNotExist";
            $this->insertDB("scores", $fields, false);
        }
        
        //echo "out";
        
    }
    
    protected function getUser($user_id){
        return $this->selectAssocRow("users","WHERE `appID`='{$this->appID}' AND `user_id`='$user_id'");
    }
    
    protected function addOneToTrackUser($user_id,$field) {
        $userRow=$this->selectAssocRow("users","WHERE `appID`='{$this->appID}' AND `user_id`='$user_id'");
        $curr=intval($userRow[$field]);
        $new=$curr+1;
        $this->updateDB("users", array($field=>$new), $userRow['id']);
    }
    
    public function addPost($parent_id,$value,$user_id,$user_value,$content,$addScore=true,$hide_name=0) {
        
        $user=$this->getUser($user_id);
        $type="comments";
        $limit=3;
        if($parent_id==0) {
           $type="posts";
           $limit=5000;
        }
        
        if(intval($user[$type])<$limit) {
                $checkIfAlreadyComment=$this->selectAssocRow("wall_posts","WHERE `parent_id`='$parent_id' AND `user_id`='$user_id'");
                $checkIfAlreadyComment=false;
        
                if(!$checkIfAlreadyComment || $parent_id=="0") {
                    $fields=array();
                    $fields['appID']=$this->appID;
                    $fields['parent_id']=$parent_id;
                    $fields['value']=$value;
                    $fields['user_id']=$user_id;
                    $fields['user_name']=$user['user_name'];
                    $fields['hide_name']=$hide_name;
                    $fields['user_value']=$user_value;
                    $fields['content']=$content;
                    $fields['time']=time();
                    $this->insertDB("wall_posts", $fields);
                    
                    
                
                    if($parent_id==0) $this->addOneToTrackUser($user_id,"posts");
                    else $this->addOneToTrackUser($user_id,"comments");
                        
                        
                    if($addScore)
                        $this->addScore($user_id,5);

                }
                else {
                  //echo "denied";  
                }
        }
        else {
            
        }
        
    }
    
    public function removePost($postID) {
        $this->deleteDB("wall_posts", $postID);
    }
    
    public function likePost($postID,$user_id,$addScore=true) {
        
        $checkIfAlreadyLike=$this->selectAssocRow("wall_likes","WHERE `postID`='$postID' AND `user_id`='$user_id'");
        $checkIfPostYours=$this->selectAssocRow("wall_posts","WHERE `id`='$postID' AND `user_id`='$user_id'");
        
        if(!$checkIfAlreadyLike && !$checkIfPostYours) {
            $user=$this->getUser($user_id);
            $fields=array();
            $fields['postID']=$postID;
            $fields['user_id']=$user_id;
            $fields['user_name']=$user['user_name'];;
            $fields['time']=time();
            $this->insertDB("wall_likes", $fields);
            $this->addOneToTrackUser($user_id,"likes");
            
            if($addScore)
                $this->addScore($user_id,2);
            
        }
    }
    
    public function removeLikePost($likeID) {
        $this->deleteDB("wall_likes", $likeID);
    }
    
    public function getWall() {
        $wall=array();
        $posts=$this->selectDB("wall_posts","WHERE `appID`='{$this->appID}' AND `parent_id`='0' ORDER BY `time` DESC LIMIT 30");
	for($i=0;$i<$posts['n'];$i++) {
		$post=mysql_fetch_assoc($posts['p']);
                $postID=$post['id'];
                
                // load likes //
                $likesAr=array();
                $likes=$this->selectDB("wall_likes","WHERE `postID`='{$postID}' ORDER BY `time` DESC");
                for($lik=0;$lik<$likes['n'];$lik++) {
                    $like=mysql_fetch_assoc($likes['p']);
                    $likesAr[]=$like;
                }
                $post['user_name']=ucfirst($post['user_name']);
                //$post['likes']=$this->getLikeText($likesAr,$postID,$value);
                $post['likes']=$likesAr;
                ///////////////////
                
                // load comments //
                $commentsAr=array();
                $comments=$this->selectDB("wall_posts","WHERE `parent_id`='{$postID}' ORDER BY `time` ASC");
                for($com=0;$com<$comments['n'];$com++) {
                    $comment=mysql_fetch_assoc($comments['p']);
                    $commentID=$comment['id'];
                    // load likes //
                    $likesAr=array();
                    $likes=$this->selectDB("wall_likes","WHERE `postID`='{$commentID}' ORDER BY `time` DESC");
                    for($lik=0;$lik<$likes['n'];$lik++) {
                        $like=mysql_fetch_assoc($likes['p']);
                        $likesAr[]=$like;
                    }
                    $comment['user_name']=ucfirst($comment['user_name']);
                    $comment['likes']=$likesAr;
                    //$comment['likes']=$this->getLikeText($likesAr,$commentID,$value);
                    ///////////////////
                    $commentsAr[]=$comment; 
                }
                $post['comments']=$commentsAr;
                ///////////////////
                $wall[]=$post;
	}
        return $wall;
    }
    
    protected function getLikeText($likesAr,$postID,$value) {
        $size=count($likesAr);
        $res=array();
        if($size=="0") {
            $res['do_like']=0;
            return $res;
        }
        if($size==1){
            $res['do_like']=1;
            $res['like1']=ucfirst($likesAr[0]['user_name']);
            $res['more']=false;
            return $res;
        }
        
        if($size==2){
            $res['do_like']=1;
            $res['like1']=ucfirst($likesAr[0]['user_name']);
            $res['like2']=", ".ucfirst($likesAr[1]['user_name']);
            $res['more']=false;
            return $res;
        }
        
        if($size>2){
            $res['do_like']=1;
            $res['like1']=ucfirst($likesAr[0]['user_name']);
            $res['like2']=", ".ucfirst($likesAr[1]['user_name']);
            $res['more']=$more=$size-2;
            return $res;
        }
    }
     
}
