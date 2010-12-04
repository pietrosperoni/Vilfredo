<?php
$error = "";
$msg = "";

$fileElementName = "fileToUpload";

if(empty($_FILES["fileToUpload"]["tmp_name"]) || $_FILES["fileToUpload"]["tmp_name"] == "none"){
   $error = "file size Limit to ".ini_get("post_max_size").". Cannot process your request";
}
else {
   $msg .= " File Name: " . $_FILES["fileToUpload"]["name"] . ", ";
   $msg .= " File Size: " . @filesize($_FILES["fileToUpload"]["tmp_name"]);
   $target_path = "./uploads/" . basename( $_FILES[$fileElementName]["name"]); 
   move_uploaded_file($_FILES[$fileElementName]["tmp_name"], $target_path);
}

$pairs["error"]["value"]=$error;
$pairs["message"]["value"]=$msg;
$pairs["imagepath"]["value"]=$target_path;

echo json_encode($pairs);
?>
