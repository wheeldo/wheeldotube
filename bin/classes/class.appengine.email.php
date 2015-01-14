<?php
use \google\appengine\api\mail\Message;

class appengineEmail  {
    
    public static function sendEmail($to,$from,$subject,$content,$attachments=array()) {
        // appengineEmail::sendEmail($to,$from,$subject,$content,$attachments=array());
        
        
        try
        {
          $message = new Message();
          $message->setSender($from);
          $message->addTo($to);
          $message->setSubject($subject);
          $message->setHtmlBody($content);
          foreach($attachments as $attachment):
              $message->addAttachment('image.jpg', 'image data', $image_content_id);
          endforeach;
          $res=$message->send();
//          echo "<pre>";
//          print_r($res);
//          echo "</pre>";
        } catch (InvalidArgumentException $e) {
          echo $e;
        }

    }
  
}

