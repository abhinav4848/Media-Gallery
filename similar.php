<?php
session_start();
$error="";
include('includes/connect-db.php');
include('includes/compareImages.php');

if (array_key_exists("fileid", $_GET) and is_numeric($_GET['fileid'])) {
    // find the image and calculate its hash
    $query = "SELECT * FROM `media_entries` WHERE id=".mysqli_real_escape_string($link, $_GET['fileid'])." LIMIT 1";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result)!=0) {
        $row = mysqli_fetch_array($result);

        $imgpath = 'storage/files/'.$row['filename_final'];
        $compareMachine = new compareImages($imgpath);
        $imghash = $compareMachine->getHasString();
    }

    // search through all the images in database with their hashes
    $query_getall = "SELECT `id`, `similar_hash` FROM `media_entries` WHERE `filename_ext`= 'jpg' or `filename_ext`= 'jpeg' or `filename_ext`= 'png'";
    $result_getall = mysqli_query($link, $query_getall);
    if (mysqli_num_rows($result_getall)!=0) {
        $list_of_similar_images = [];

        while ($row_getall = mysqli_fetch_array($result_getall)) {
            $diff = $compareMachine->compareHash($row_getall['similar_hash']);
            if ($diff<12) {
                array_push($list_of_similar_images, $row_getall['id']);
            }
        }
    }
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

    <title>Compare Images</title>

    <style type="text/css">
    </style>
</head>

<body>
    <?php include('includes/navbar.php');?>
    <div class="container">
        <form method="get">
            <div class="form-group">
                <label for="fileid">File Id to find similar images</label>
                <input type="number" class="form-control" name="fileid" id="fileid" placeholder="Enter file id" <?php
                if (array_key_exists("fileid", $_GET)) {
                    echo 'value="'.$_GET['fileid'].'"';
                }?>>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <h4>Chosen Image</h4>
        <?php
        if (isset($imgpath)) {
            echo '<div class="card" style="width: 18rem;">
            <img src="'.$imgpath.'" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">'.$row['title'].'</h5>
                <p class="card-text"><b>Hash:</b> <span class="bg-secondary text-white">'.$imghash.'</span> <br /><b>Description:</b> '.$row['description'].' </p>
                <a href="view.php?id='.$row['id'].'" class="btn btn-primary">View</a>
            </div>
            </div>';
        }
        ?>
        <hr>
        <h4>Similar Image</h4>
        <div class="row">
            <?php
            if (!empty($list_of_similar_images)) {
                foreach ($list_of_similar_images as $imageid) {
                    $query_getimageDetails = "SELECT * FROM `media_entries` WHERE id=".mysqli_real_escape_string($link, $imageid)." LIMIT 1";
                    $result_getimageDetails = mysqli_query($link, $query_getimageDetails);
                    $row_getimageDetails = mysqli_fetch_array($result_getimageDetails);

                    $imgpath = 'storage/files/'.$row_getimageDetails['filename_final'];

                    echo '<div class="card m-1 column" style="width: 18rem;">
                    <img src="'.$imgpath.'" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">'.$row_getimageDetails['title'].'</h5>
                        <p class="card-text"><b>Hash:</b> <span class="bg-secondary text-white">'.$row_getimageDetails['similar_hash'].'</span> <br /><b>Description:</b> '.$row_getimageDetails['description'].' </p>
                        <a href="view.php?id='.$row_getimageDetails['id'].'" class="btn btn-primary">View</a>
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
</body>

</html>