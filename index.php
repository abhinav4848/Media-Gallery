<?php
session_start();
$error="";
include 'includes/connect-db.php';

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
    <div class="container my-2">
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h1 class="display-4">Media Server</h1>
                <p class="lead">Find all types of media here.
                </p>
                <hr class="my-4">
                <p>Choose What files you want</p>
                <a class="btn btn-primary btn-lg" href="view-all.php?type=vids" role="button">Vids</a>
                <a class="btn btn-primary btn-lg" href="view-all.php?type=pics" role="button">Pics</a>
                <a class="btn btn-primary btn-lg" href="view-all.php?type=gifs" role="button">Gifs</a>
                <p>Or upload some</p>
                <a class="btn btn-warning btn-lg" href="upload.php" role="button">Upload</a>
            </div>
        </div>

        <h3>Recent Uploads</h3>
        <ol>
            <?php
            $query = "SELECT * FROM `media_entries` ORDER BY id DESC LIMIT 30";
            $result = mysqli_query($link, $query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<li><a href="view.php?id='.$row['id'].'" target="_self">'.$row['title'].'&nbsp;&nbsp;';
                if ($row['filename_ext']=='jpg' or $row['filename_ext']=='jpeg' or $row['filename_ext']=='png') {
                    echo '<span class="badge badge-primary">IMAGE</span>';
                }
                
                if ($row['filename_ext']=='gif') {
                    echo '<span class="badge badge-warning">GIF</span>';
                }

                if ($row['filename_ext']=='mp4') {
                    echo '<span class="badge badge-danger">Long Vid</span>';
                }

                if ($row['filename_ext']=='webm') {
                    echo '<span class="badge badge-secondary">Short Vid</span>';
                }
                
                echo '</a> <span class="text-muted small">'.date("d-m-Y h:i:s A", strtotime($row['upload_time'])).'</span></li>';
            }

            ?>
        </ol>
    </div>
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