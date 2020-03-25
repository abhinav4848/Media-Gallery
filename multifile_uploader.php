<?php
session_start();
$error="";
include('includes/connect-db.php');

//allowed file types
$arr_file_types = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'video/webm', 'video/mp4'];

if (array_key_exists("submit", $_POST) and $_POST["submit"]==='submit') {
    $_SESSION['files']=array();
    $_SESSION['filename_original_name']=array();
    $_SESSION['filename_ext']=array();
    $_SESSION['original_filename_without_ext']=array();
    $_SESSION['error']=array();

    $total = count($_FILES['upload']['name']);
    
    // Loop through each file
    for ($i=0 ; $i < $total ; $i++) {
        $filename_final_to_save = str_replace('#', '_', urlencode(htmlentities(time().'-'.$_FILES['upload']['name'][$i])));

        // array pushing
        $_SESSION['files'][] = $filename_final_to_save;
        $_SESSION['filename_original_name'][] = $_FILES['upload']['name'][$i];
        $_SESSION['filename_ext'][] = pathinfo($filename_final_to_save, PATHINFO_EXTENSION);
        $_SESSION['original_filename_without_ext'][] = pathinfo($_FILES['upload']['name'][$i])['filename'];

        if (!(in_array($_FILES['upload']['type'][$i], $arr_file_types))) {
            $_SESSION['error'][$i]='<b>Valid File Types:</b> '.implode(", ", $arr_file_types);
        } else {
            //Get the temp file path
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
            
            //Make sure we have a file path
            if ($tmpFilePath != "") {
                //Setup our new file path
                $path= './storage/tmpfiles/';
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                //Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $path.$filename_final_to_save)) {
                    echo 'Success in uploading: '.$filename_final_to_save.'<br />';
                } else {
                    $_SESSION['error']='Couldn\t Upload';
                }
            }
        }
    }
    header('Location: edit_multi.php');
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.95, shrink-to-fit=no">
    <meta name="theme-color" content="#28a745">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="includes/libraries/bootstrap.min.css">

    <title>Create a tag/star</title>

    <style type="text/css">
    </style>
</head>

<body>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="upload[]" multiple>
        <button type="submit" value="submit" name="submit">Submit</button>
    </form>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="includes/libraries/jquery-3.2.1.min.js">
    </script>
    <script src="includes/libraries/popper.min.js">
    </script>
    <script src="includes/libraries/bootstrap.min.js">
    </script>
</body>

</html>