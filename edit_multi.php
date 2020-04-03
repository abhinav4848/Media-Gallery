<?php
session_start();
$error="";
include('includes/connect-db.php');

if (array_key_exists("files", $_SESSION) and isset($_SESSION["files"])) {
    $total = count($_SESSION['files']);
} else {
    header('Location: multifile_uploader.php');
}

if (array_key_exists("request_type", $_POST) and $_POST["request_type"]=='add_entry') {
    // var_dump($_POST);

    // check if file already exists
    $hash = sha1_file('storage/tmpfiles/'.$_POST['filename_final']);
    
    $query_check_hash = "SELECT `id` FROM `media_entries` WHERE hash='".mysqli_real_escape_string($link, $hash)."' LIMIT 1";
    $result_check_hash = mysqli_query($link, $query_check_hash);
    if (mysqli_num_rows($result_check_hash)!=0) {
        // duplicate upload detected
        $row_check_hash=mysqli_fetch_array($result_check_hash);

        // JSON Method
        $repsonse_array = ['error'=>'exists',
            'link'=>$row_check_hash['id']];

        //delete that uploaded file
        unlink('storage/tmpfiles/'.$_POST['filename_final']);
    } else {
        $query = "INSERT INTO `media_entries` (`title`, `description`, `filename_final`, `filename_original_name`, `filename_ext`, `hash`, `upload_time`)
    		VALUES (
    		'".mysqli_real_escape_string($link, $_POST['title'])."',
    		'".mysqli_real_escape_string($link, $_POST['description'])."',
            '".mysqli_real_escape_string($link, $_POST['filename_final'])."',
    		'".mysqli_real_escape_string($link, $_POST['filename_original_name'])."',
    		'".mysqli_real_escape_string($link, $_POST['filename_ext'])."',
            '".mysqli_real_escape_string($link, $hash)."',
    		'".date('Y-m-d H:i:s')."');";
    
        if (mysqli_query($link, $query)) {
            // create the path if not exists
            $path= 'storage/files/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            rename('storage/tmpfiles/'.$_POST['filename_final'], $path.'/'. $_POST['filename_final']);

            // JSON Method
            $repsonse_array = ['error'=>'',
                'link'=>mysqli_insert_id($link)];
        } else {
            $repsonse_array = ['error'=>'unknown'];
        }
    }

    // delete that key from index of files
    // if (($key = array_search($_POST['filename_final_to_save'], $_SESSION['files'])) !== false) {
    //     unset($_SESSION['files'][$key]);
    // }
    echo json_encode($repsonse_array);
    die();
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

    <title>Process Multiple files</title>

    <style type="text/css">
    </style>
</head>

<body>
    <?php include('includes/navbar.php');?>

    <div class="container-fluid">
        <div class="grid">
            <?php
            // Loop through each file
            for ($i=0 ; $i < $total ; $i++) {
                $filename_final_to_save = $_SESSION['files'][$i];
                $filename_original_name = $_SESSION['filename_original_name'][$i];
                $filename_ext = $_SESSION['filename_ext'][$i];
                $original_filename_without_ext = $_SESSION['original_filename_without_ext'][$i];
                
                if (file_exists('storage/tmpfiles/'.$filename_final_to_save)) {
                    if (array_key_exists($i, $_SESSION['error'])) {
                        $error = $_SESSION['error'][$i];
                        echo '<div class="grid-item card mb-1" style="width: 18rem;">
                        <div class="card-body">
                        <p class="card-text text-danger">';
                        echo $error;
                        echo '</p></div></div>';
                    } else {
                        echo '<div class="grid-item card mb-1" style="width: 18rem;">';

                        if ($filename_ext == 'jpg' or $filename_ext == 'jpeg' or $filename_ext == 'png' or $filename_ext == 'gif') {
                            echo '<img src="storage/tmpfiles/'.$filename_final_to_save.'" class="card-img-top">';
                        } else {
                            echo '<a href="storage/tmpfiles/'.$filename_final_to_save.'" target="_blank" class="btn btn-secondary">Link</a>';
                        }
                    
                        echo '<div class="card-body">
                        <form>
                        
                        <input type="hidden" name="filename_original_name" value="'.$filename_original_name.'">
                        <input type="hidden" name="filename_ext" value="'.$filename_ext.'">
                        <input type="hidden" name="filename_final" value="'.$filename_final_to_save.'">
                        <input type="hidden" name="original_filename_without_ext" value="'.$original_filename_without_ext.'">
                        
                        <h5 class="card-title"><input type="text" class="form-control" name="title" placeholder="Title" value="'.$original_filename_without_ext.'"></h5>
                        <p class="card-text"><textarea class="form-control" name="description" placeholder="Description"></textarea><b>Original Name</b>: '.$filename_original_name.' <br /><b>Extension</b>: '.$filename_ext.'<br /></p>
                        <button type="button" class="btn btn-primary" onclick="save(this.form)">Save</button>
                        </form>
                        </div>
                        </div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="includes/libraries/jquery-3.2.1.min.js">
    </script>
    <script src="includes/libraries/popper.min.js">
    </script>
    <script src="includes/libraries/bootstrap.min.js">
    </script>
    <script src="includes/libraries/masonry.min.js">
    </script>

    <script>
    $(document).ready(function($) {
        setTimeout(function() {
            // initialize
            $('.grid').masonry({
                // options
                itemSelector: '.grid-item',
                columnWidth: 30
            });
        }, 800);
    });

    function save(oform) {
        // http://javascript-coder.com/javascript-form/javascript-get-all-form-objects.phtml

        // SEQUENCE:
        // filename_original_name
        // filename_ext
        // filename_final
        // original_filename_without_ext
        // title
        // description

        if (oform.elements[4].value == '') {
            alert('Please enter a title');
            oform.elements[4].value = oform.elements[3].value;
        } else {
            $.ajax({
                type: "POST",
                url: "edit_multi.php",
                data: {
                    filename_original_name: oform.elements[0].value,
                    filename_ext: oform.elements[1].value,
                    filename_final: oform.elements[2].value,
                    title: oform.elements[4].value,
                    description: oform.elements[5].value,
                    request_type: 'add_entry'
                },
                success: function(result) {
                    console.log(result)

                    try {
                        var obj = JSON.parse(result);
                        if (obj.error == '') {
                            alert('Success. File is at: ' + obj.link);
                        }
                        if (obj.error == 'exists') {
                            alert('File already exists: ' + obj.link);

                        }
                        if (obj.error == 'unknown') {
                            alert('Unknown Error');
                        }
                    } catch (err) {
                        // not JSON
                        alert(result)
                    }


                }
            })
        }
    }
    </script>
</body>

</html>