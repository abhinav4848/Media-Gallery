<?php
session_start();
$error="";
include('includes/connect-db.php');

if (array_key_exists("submit", $_POST) and $_POST['submit']=='update_entry') {
    //check if submitting edit
    if ($_POST['title']=='') {
        $error.='Enter a title. ';
    }
    
    if ($error=='') {
        $query_update = "UPDATE `media_entries` SET 
        title = '".mysqli_real_escape_string($link, $_POST['title'])."',
        description = '".mysqli_real_escape_string($link, $_POST['description'])."'
        WHERE id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
    
        if (mysqli_query($link, $query_update)) {
        } else {
            echo '<div id="tablediv">';
            echo "failed to update the entry.";
            echo $query_update;
            echo '</div>';
        }
    }
}

if (array_key_exists("delete", $_POST) and array_key_exists("id", $_POST) and $_POST['id']!='' and is_numeric($_POST['id'])) {
    // check if deleting
    // get data about the filename being deleted
    $query = "SELECT * FROM `media_entries` WHERE id=".mysqli_real_escape_string($link, $_POST['id'])." LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);

    $query_delete = "DELETE FROM `media_entries`
        WHERE id = '".mysqli_real_escape_string($link, $_POST['id'])."' LIMIT 1";
    
    if (mysqli_query($link, $query_delete)) {
        $path = 'storage/tmpfiles/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $query_delete_tags = "DELETE FROM `media_tag` WHERE `media_id` = ".mysqli_real_escape_string($link, $_POST['id']);
        if (mysqli_query($link, $query_delete_tags)) {
            rename('storage/files/'. $row['filename_final'], $path.$row['filename_final'].'-deleted-'.time().'.'.$row['filename_ext']);
            echo 'success'; // ajax request
        }
    } else {
        echo '<div id="tablediv">';
        echo "failed to delete the entry.";
        echo $query_delete;
        echo '</div>';
    }
    die();
}

if (array_key_exists("id", $_GET) and $_GET['id']!='' and is_numeric($_GET['id'])) {
    // check if viewing
    $query = "SELECT * FROM `media_entries` WHERE id=".mysqli_real_escape_string($link, $_GET['id'])." LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
} else {
    header("Location: index.php");
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

    <title>Edit Entry</title>

    <style type="text/css">
    </style>
</head>

<body>
    <?php include('includes/navbar.php');?>
    <div class="container my-2">
        <h1>Edit Media</h1>
        <?php
        if ($error!="") {
            echo '<div class="alert alert-danger" role="alert" id="alert-error">'.$error.'</div>';
        }
        echo '<p>Editing File: <a class="badge badge-warning" href="view.php?id='.$row['id'].'" target="_blank">'. $row['title'].'</a></p>';
        ?>
        <button class="btn btn-outline-danger btn-sm delete m-1" data-id="<?=$row['id']?>">X</button>
        <form method="post" class="card p-2">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" <?php
                if (array_key_exists('title', $_POST)) {
                    echo 'value="'.$_POST['title'].'"';
                } else {
                    echo 'value="'.$row['title'].'"';
                }
                ?>>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea type="date" class="form-control" rows="3" id="description" name="description"><?php
                if (array_key_exists('description', $_POST)) {
                    echo $_POST['description'];
                } else {
                    echo $row['description'];
                }
                ?></textarea>
            </div>

            <button type="submit" class="submit btn btn-primary" value="update_entry" name="submit">Submit</button>

        </form>
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
    $(".delete").click(function() {
        if (confirm("You Sure?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                type: "POST",
                url: "edit.php",
                data: {
                    delete: 'delete_entry',
                    id: id
                },
                success: function(result) {
                    if (result == 'success') {
                        window.location.replace('index.php');
                    } else {
                        alert(result);
                    }
                }
            })
        }
    })
    </script>
</body>

</html>