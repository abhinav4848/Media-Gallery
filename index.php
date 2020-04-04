<?php
session_start();
$error="";
include 'includes/connect-db.php';

# search feature
if (array_key_exists("query", $_POST)) {
    if ($_POST['query']!= '') {
        # members
        $query = "SELECT * FROM `media_entries` WHERE `title` LIKE '%".mysqli_real_escape_string($link, $_POST['query'])."%' OR description LIKE '%".mysqli_real_escape_string($link, $_POST['query'])."%' LIMIT 10";
        $result = mysqli_query($link, $query);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                echo '<div class="card m-1 column" style="width: 18rem;">';
                if ($row['filename_ext']=='jpg' or $row['filename_ext']=='jpeg' or $row['filename_ext']=='png' or $row['filename_ext']=='gif') {
                    echo '<a href="view.php?id='.$row['id'].'"><img src="storage/files/'.$row['filename_final'].'" class="card-img-top"></a>';
                }
                echo '<div class="card-body">
                <h5 class="card-title">'.ucfirst($row['title']).'</h5>';

                echo '<p class="card-text">'.$row['description'].'</p>
                <a class="btn btn-primary" href="view.php?id='.$row['id'].'">VIEW</a>';
                
                echo '</div><!-- // card-body -->
                </div><!-- // card -->';
            }
        }
    }
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

    <title>Welcome to Media Server</title>

    <style type="text/css">
    /*restore the cancel button on searchbox that bootstrap breas*/
    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
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
                <p>Choose What files you want Or upload some</p>
                <a class="btn btn-primary btn-lg" href="view-all.php?type=vids" role="button">Vids</a>
                <a class="btn btn-primary btn-lg" href="view-all.php?type=pics" role="button">Pics</a>
                <a class="btn btn-primary btn-lg" href="view-all.php?type=gifs" role="button">Gifs</a>
                <a class="btn btn-warning btn-lg" href="upload.php" role="button">Upload</a>
            </div>
            <div class="search p-2">
                <input autofocus="" type="search" id="searchField" class="form-control" placeholder="Search for entries"
                    title="Type in something" autocomplete="off">
                <div class="mt-2 p-2 row" id="results"></div>
            </div>
        </div>

        <h3>Recent Uploads</h3>
        <ol>
            <?php
            $query = "SELECT * FROM `media_entries` ORDER BY id DESC LIMIT 50";
            $result = mysqli_query($link, $query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<li><a href="view.php?id='.$row['id'].'" target="_self">'.$row['title'].'&nbsp;&nbsp;';
                if ($row['filename_ext']=='jpg' or $row['filename_ext']=='jpeg' or $row['filename_ext']=='png') {
                    echo '<span class="badge badge-primary">Image</span>';
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

    <script>
    document.querySelector('#searchField').addEventListener('keyup', search, false);
    document.querySelector('#searchField').addEventListener("search", search, false);

    function search() {
        var query = $("#searchField").val();
        $.ajax({
            type: "POST",
            url: "index.php",
            data: {
                query: query
            },
            success: function(result) {
                if (result != '') {
                    $("#results").show();
                    $("#results").html(result);
                    mason();
                } else {
                    $("#results").hide();
                }
            }
        })
    }
    </script>
</body>

</html>