<?php
session_start();

//allowed file types
$arr_file_types = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'video/webm', 'video/mp4'];
 
if (!(in_array($_FILES['file']['type'], $arr_file_types))) {
    echo json_encode(['error'=>'Invalid File Type. Allowed types are: '.$arr_file_types]);
    return;
}

// create the path if not exists
$path= '../storage/tmpfiles/';
if (!file_exists($path)) {
    // recursively create the full path
    // https://stackoverflow.com/a/15012257/2365231
    mkdir($path, 0777, true);
}

//Check if File's already been uploaded
if (file_exists($path.($_FILES["file"]["name"]))) {
    echo json_encode(['error'=>'File already exists']);
    return;
// If it's not uploaded , then :
} else {
    //Then Save it to media folder!

    $substitute_params = array('#' => '_', '~' => '_', '&' => '_');
    // create the final name the file will have on the server hard disk

    // https://stackoverflow.com/a/12665861/2365231
    $filename_final_to_save =  strtr(time().'-'.$_FILES['file']['name'], $substitute_params);

    // move file from PHP temp location to custom temp location using this new name
    move_uploaded_file($_FILES['file']['tmp_name'], $path.$filename_final_to_save);

    // // create html entries to be returned to AJAX query=> Path on the server, Original name, extension, direct link
    // // echo "Upload: ".$_FILES["file"]["name"]."<br>";
    // // echo "Type: ".$_FILES["file"]["type"]."<br>";
    // // echo "Size: ".($_FILES["file"]["size"] / 1048576)." MB <br>";
    // // echo "Temp file:". $_FILES["file"]["tmp_name"]."<br>";

    // JSON Method
    $repsonse_array = ['filename_final_to_save' => $filename_final_to_save,
    'filename_original_name' => $_FILES['file']['name'],
    'filename_ext'=>pathinfo($filename_final_to_save, PATHINFO_EXTENSION),
    'original_filename_without_ext'=>pathinfo($_FILES['file']['name'])['filename'],
    'error'=>''];

    echo json_encode($repsonse_array);
}