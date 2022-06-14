<?php

require_once("config.php");

//Get Request to $url using a custom User Agent (Defined in Config) that is allowed by https://i.instagram.com/api/v1/,
//And the Cookie Information also Defined in Config
function instagramRequest($url){
    $options  = array(
        'http' => array(
            'user_agent' => USER_AGENT,
            'header'=> "Accept-language: en\r\n" .
                "Cookie: sessionid=" . SESSIONID . "; ds_user_id=" . DS_USER_ID . "\r\n"
        )
    );
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return $response;
}

//Returns Instagram User ID from Username as Input
function getUserId($username){
   $response = instagramRequest("https://i.instagram.com/api/v1/users/web_profile_info/?username={$username}");
   $response_obj = json_decode($response);
   $user_id = $response_obj -> data -> user -> id;

   return $user_id;
}

//Returns if the user account is private or not
function isPrivate($username){
    $response = instagramRequest("https://i.instagram.com/api/v1/users/web_profile_info/?username={$username}");
    $response_obj = json_decode($response);
    $is_private = $response_obj -> data -> user -> is_private;

    return $is_private;
}

//Returns an array of Instagram stories for the provided user id
function getStories($user_id){
    $response = instagramRequest("https://i.instagram.com/api/v1/feed/user/{$user_id}/reel_media/");
    $response_obj = json_decode($response);
    
    if($response_obj -> user -> is_private){
        return ["error"=>true, "error_message"=>"User is private."];
    }

    if($response_obj -> latest_reel_media == NULL){
        return ["error"=>true, "error_message"=>"User has no story."];
    }

    $story_count = count($response_obj -> items);

    $story_array = [];

    for($i = 0; $i < $story_count; $i = $i + 1){
        $story_array[$i] = convertStoryObject($response_obj -> items[$i]);
    }

    $final = [
        "error" => false,
        "count" => $story_count,
        "stories" => $story_array,
    ];

    return $final;
}

//Converts the Instagram story item object to an array of information to be returned by this api
function convertStoryObject($story_object){
    

    $media_type = "";
    
    if($story_object -> media_type == 1){
        $media_type = "image";
    }elseif($story_object -> media_type == 2){
        $media_type = "video";
    }else{
        return ["error"=>true, "error_message"=>"Unknown media type."];
    }

    $preview_img = $story_object -> image_versions2 -> candidates[0] -> url;

    $final_img_vid = "";

    if($media_type == "image"){
        $final_img_vid = $preview_img;
    }else{
        $final_img_vid = $story_object -> video_versions[0] -> url;
    }


    $story = [
        "upload_time" => $story_object -> taken_at,
        "expire_time" => $story_object -> expiring_at,
        "media_type" => $media_type,
        "preview_image" => $preview_img,
        "final_img_vid" => $final_img_vid,
    ];

    return $story;
}

?>