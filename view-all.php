<?php
session_start();
$error="";

if (array_key_exists("type", $_GET) and $_GET['type']!='') {
    include('includes/connect-db.php');
} else {
    header("Location: index.php");
}

$whereclause='';
if ($_GET['type']=='vids') {
    $whereclause.=" filename_ext='".mysqli_real_escape_string($link, 'mp4')."' OR filename_ext='".mysqli_real_escape_string($link, 'webm')."'";
}
if ($_GET['type']=='pics') {
    $whereclause.=" filename_ext='".mysqli_real_escape_string($link, 'png')."' OR filename_ext='".mysqli_real_escape_string($link, 'jpg')."' OR filename_ext='".mysqli_real_escape_string($link, 'jpeg')."'";
}
if ($_GET['type']=='gifs') {
    $whereclause.=" filename_ext='".mysqli_real_escape_string($link, 'gif')."'";
}

$query = "SELECT * FROM `media_entries` WHERE ".$whereclause."  LIMIT 20";
$result = mysqli_query($link, $query);
// echo $query;
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

    <title>Welcome to Media Server</title>

    <style type="text/css">
    </style>
</head>

<body>
    <?php include('includes/navbar.php');?>
    <div class="container-fluid">
        <h1>Watch</h1>
        <div class="grid">
            <?php
            while ($row = mysqli_fetch_array($result)) {
                echo '<div class="grid-item card mb-3" style="width: 18rem;">';
                if ($_GET['type']=='pics' or $_GET['type']=='gifs') {
                    echo '<a href="view.php?id='.$row['id'].'"><img src="storage/files/'.$row['filename_final'].'" class="card-img-top"></a>';
                }
                echo '<div class="card-body">
                <h5 class="card-title">'.ucfirst($row['title']).'</h5>';

                if ($_GET['type']=='vids') {
                    echo '<p class="card-text">'.$row['description'].'</p>
                    <a class="btn btn-primary" href="view.php?id='.$row['id'].'">VIEW</a>';
                }
                
                echo '</div><!-- // card-body -->
                </div><!-- // card -->';
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
        }, 200);
    });
    </script>

</body>

</html>