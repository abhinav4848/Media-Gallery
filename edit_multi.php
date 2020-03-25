<?php
session_start();
$error="";
include('includes/connect-db.php');

if (array_key_exists("files", $_SESSION) and isset($_SESSION["files"])) {
    $total = count($_SESSION['files']);
} else {
    header('Location: multifile_uploader.php');
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
    <div class="container-fluid">
        <div class="grid">
            <?php
            // Loop through each file
            for ($i=0 ; $i < $total ; $i++) {
                $filename_final_to_save = $_SESSION['files'][$i];
                $filename_original_name = $_SESSION['filename_original_name'][$i];
                $filename_ext = $_SESSION['filename_ext'][$i];
                $original_filename_without_ext = $_SESSION['original_filename_without_ext'][$i];
                
                if (array_key_exists($i, $_SESSION['error'])) {
                    $error = $_SESSION['error'][$i];
                    echo '<div class="grid-item card" style="width: 18rem;">
                    <div class="card-body">
                    <p class="card-text">';
                    echo $error;
                    echo '</p></div></div>';
                } else {
                    echo '<div class="grid-item card" style="width: 18rem;">';

                    if ($filename_ext == 'jpg' or $filename_ext == 'jpeg' or $filename_ext == 'png' or $filename_ext == 'gif') {
                        echo '<img src="storage/tmpfiles/'.$filename_final_to_save.'" class="card-img-top">';
                    }
                    
                    echo '<div class="card-body">
                    <h5 class="card-title">$filename_original_name</h5>
                    <p class="card-text">Original Name: '.$filename_final_to_save.' Extension: '.$filename_ext.', original_filename_without_ext: '.$original_filename_without_ext.'</p>
                    <a href="#" class="btn btn-primary">Save</a>
                    </div>
                    </div>';
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
        }, 1000);
    });
    </script>
</body>

</html>