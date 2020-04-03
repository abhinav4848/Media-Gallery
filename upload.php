<?php
session_start();
$error="";
include('includes/connect-db.php');

if (array_key_exists("submit", $_POST) and $_POST["submit"]==='add_entry') {
    if ($_POST['filename_final']=='') {
        $error.='Please upload a file. ';
    }
    
    if ($_POST['title']=='') {
        $error.='Enter a title. ';
    }

    $hash = sha1_file('storage/tmpfiles/'.$_POST['filename_final']);
    
    $query_check_hash = "SELECT `id` FROM `media_entries` WHERE hash='".mysqli_real_escape_string($link, $hash)."' LIMIT 1";
    $result_check_hash = mysqli_query($link, $query_check_hash);
    if (mysqli_num_rows($result_check_hash)!=0) {
        $row_check_hash=mysqli_fetch_array($result_check_hash);
        $error.='File exists: <a href="view.php?id='.$row_check_hash['id'].'" target="_blank">here</a>';

        //delete that uploaded file
        unlink('storage/tmpfiles/'.$_POST['filename_final']);
    }

    
    if ($error=='') {
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
            
            header("Location: view.php?id=".mysqli_insert_id($link));
        } else {
            echo '<div id="tablediv">';
            echo "failed to insert the entry.";
            echo '</div>';
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

    <title>Welcome to Media Server</title>

    <style type="text/css">
    @media screen and (min-width: 480px) {
        img {
            margin: 0px 5px 5px 5px !important;
            border: 0px solid #dee2e6;
            width: 200px;
        }
    }

    @media screen and (max-width: 480px) {
        img {
            margin-bottom: 20px !important;
        }
    }
    </style>
</head>

<body>
    <?php include('includes/navbar.php');?>
    <div class="container my-2">
        <h1>Upload Media</h1>
        <?php
        if ($error!="") {
            echo '<div class="alert alert-danger" role="alert" id="alert-error">'.$error.'</div>';
        }
        ?>
        <form method="post" class="card p-2">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" <?php
                if (array_key_exists('title', $_POST)) {
                    echo 'value="'.$_POST['title'].'"';
                }
                ?>>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" rows="3" id="description" name="description"><?php
                if (array_key_exists('description', $_POST)) {
                    echo $_POST['description'];
                }
                ?></textarea>
            </div>

            <div class="form-group">
                <!-- The whole div below gets replaced by the ajax returned line in case of successful upload -->
                <div class="output mb-1">

                    <?php
                    if (array_key_exists('filename_final', $_POST)) {
                        // if a filename has already been returned by ajax, use that
                        echo "<p class='mb-0'>Stored in: <a href='storage/tmpfiles/". $_POST['filename_final']."' target='_blank'>storage/tmpfiles/". $_POST['filename_final']."</a></p>";
                    } else {
                        //else create the form for uploading the file
                        
                        echo '<!-- Choose File -->
                        <input type="file" name="image" class="image mb-1">
                        <!-- Upload Button -->
                        <button class="btn btn-primary btn-sm upload mb-1">Upload</button>
                        <!-- Progress Bar -->
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%" role="progressbar" id="progress"></div>
                        </div>';
                    }
                    ?>

                    <input type="hidden" name="filename_final" <?php
                    if (array_key_exists('filename_final', $_POST)) {
                        echo 'value="'.$_POST['filename_final'].'"';
                    }
                    ?>>

                    <input type="hidden" name="filename_original_name" <?php
                    if (array_key_exists('filename_original_name', $_POST)) {
                        echo 'value="'.$_POST['filename_original_name'].'"';
                    }
                    ?>>

                    <input type="hidden" name="filename_ext" <?php
                    if (array_key_exists('filename_ext', $_POST)) {
                        echo 'value="'.$_POST['filename_ext'].'"';
                    }
                    ?>>

                </div>
            </div>
            <button type="submit" class="submit btn btn-primary" value="add_entry" name="submit">Submit</button>

        </form>
        <div id="media"></div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="includes/libraries/jquery-3.2.1.min.js">
    </script>
    <script src="includes/libraries/popper.min.js">
    </script>
    <script src="includes/libraries/bootstrap.min.js">
    </script>

    <script type="text/javascript">
    $(function() {
        $('.upload').on('click', function() {
            var file_data = $('.image').prop('files')[0];

            if (file_data != undefined) {
                var form_data = new FormData();
                form_data.append('file', file_data);
                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                //document.getElementById('progress').innerHTML = percentComplete + '%';

                                document.getElementById("progress").style.width =
                                    percentComplete + '%';

                                if (percentComplete === 100) {
                                    document.getElementById("progress").style
                                        .width = '0%';
                                }

                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: 'includes/ajax-upload-receiver.php',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(response) {
                        if (response == 'type') {
                            alert('Invalid file type');
                        } else if (response == 'exists') {
                            alert('File already exists');
                        } else {
                            var obj = JSON.parse(response);
                            if (obj.error == '') {
                                $(".output").html(
                                    "<input type='hidden' name='filename_final' id='filename_final' value='" +
                                    obj.filename_final_to_save +
                                    "'><input type='hidden' name='filename_original_name' id='filename_original_name' value='" +
                                    obj.filename_original_name +
                                    "'><input type='hidden' name='filename_ext' id='filename_ext' value='" +
                                    obj.filename_ext +
                                    "'><p class='mb-0'>Stored in: <a href='storage/tmpfiles/" +
                                    obj.filename_final_to_save +
                                    "' target='_blank'>storage/tmpfiles/" + obj
                                    .filename_final_to_save + "</a></p>");
                                if (document.getElementById("title").value == '') {
                                    document.getElementById("title").value =
                                        obj.original_filename_without_ext;
                                }
                                if (obj.filename_ext == 'jpg' ||
                                    obj.filename_ext == 'jpeg' ||
                                    obj.filename_ext == 'png' ||
                                    obj.filename_ext == 'gif') {
                                    $("#media").html("<img src='storage/tmpfiles/" +
                                        obj.filename_final_to_save +
                                        "' class='img-thumbnail float-right'>")
                                } else {
                                    $("#media").html(
                                        '<video class="embed-responsive-item" controls><source src="storage/tmpfiles/' +
                                        obj.filename_final_to_save +
                                        '" type="video/mp4">Your browser does not support the video tag.</video>'
                                    )
                                }

                            } else {
                                alert(obj.error);
                            }
                        }
                        $('.image').val('');
                    }
                });
            }
            return false;
        });
    });
    </script>
</body>

</html>