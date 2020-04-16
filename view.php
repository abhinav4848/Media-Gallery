<?php
session_start();
$error="";

if (array_key_exists("id", $_GET) and $_GET['id']!='' and is_numeric($_GET['id'])) {
    include('includes/connect-db.php');
} else {
    header("Location: index.php");
}

$query = "SELECT * FROM `media_entries` WHERE id=".mysqli_real_escape_string($link, $_GET['id'])." LIMIT 1";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result);

if ($row['id']=='') {
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

    <title>View</title>

    <style type="text/css">
    img {
        max-height: 100%;
        max-width: 100%;
    }

    #results {
        display: none;
    }

    .badge {
        cursor: pointer;
    }

    .delete {
        cursor: not-allowed !important;
    }

    #error,
    #success {
        display: none;
    }
    </style>
</head>

<body>
    <?php
        include('includes/navbar.php');
    ?>
    <div class="container">
        <div class="row mt-1">
            <div class="col-sm-8">
                <?php
                    if ($row['filename_ext']=='jpg' or $row['filename_ext']=='jpeg' or $row['filename_ext']=='png' or $row['filename_ext']=='gif') {
                        echo '<img src="storage/files/'.$row['filename_final'].'" width="640">';
                    }
                    if ($row['filename_ext']=='webm' or $row['filename_ext']=='mp4') {
                        echo '<div class="embed-responsive embed-responsive-16by9">
                        <video class="embed-responsive-item" controls>
                        <source src="storage/files/'.$row['filename_final'].'" type="video/mp4">
                        Your browser does not support the video tag.
                        </video>
                        </div>';
                    }
                ?>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <?php
                            echo '<h2 class="card-title">'.ucfirst($row['title']).'</h2>';
                            echo '<span class="text-muted small" id="media_id" value="'.$row['id'].'">'.date("d-m-Y h:i:s A", strtotime($row['upload_time'])).'</span>';
                            echo '<p class="card-text">Format: <span class="badge badge-primary">'.$row['filename_ext'].'</span>, ';
                            echo 'Actions: <span class="badge badge-warning" data-toggle="modal" data-target="#editModal">Edit</span></p>';

                            $url_regex  = '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i';
                            $row['description'] = preg_replace($url_regex, '<a href="$1" target="_blank">$1</a>', $row['description']);
                    
                            echo '<p class="card-text"><b>Description: </b>'.$row['description'].'</p>';
                        ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tags:</h5>
                        <p class="card-text" id="tags"></p>
                        <input type="text" class="form-control" name="tag" id="tag" placeholder="Search Tags"
                            autocomplete="off">
                        <span id="results"></span>
                        <span id="suggestCreatingTag"></span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Similar: </h5>
                        <a href="similar.php?fileid=<?=$row['id']?>">Find Similar</a>
                        <p class="card-text" id="similar"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLabel">Edit Media</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger fade show mt-2" id="error"> </div>
                    <div class="alert alert-success fade show mt-2" id="success"> </div>
                    <span class="float-right">Go to <a href="edit.php?id=<?=$row['id']?>">Basic Edit Page</a></span>
                    <form id="edit_entry">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?=$row['title']?>">
                        </div>
                        <div class=" form-group">
                            <label for="description">Description</label>
                            <textarea type="date" class="form-control" rows="3" id="description"
                                name="description"><?=$row['description']?></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="submit btn btn-primary" name="submit">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
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
    <script>
    window.onload = function() {
        showtags();
        showSimilar();
    };

    function showtags() {
        $.ajax({
            type: "POST",
            url: "includes/tag_handler.php",
            data: {
                media_id: document.getElementById('media_id').getAttribute('value'),
                request_type: 'show_tags'
            },
            success: function(result) {
                $("#tags").html(result);
            }
        })
    }

    document.querySelector('#tag').addEventListener('keyup', search, false);

    function search() {
        var text = $("#tag").val();

        $.ajax({
            type: "POST",
            url: "includes/tag_handler.php",
            data: {
                text: text,
                request_type: 'search'
            },
            success: function(result) {
                var obj = JSON.parse(result);
                if (!("error" in obj)) {
                    $("#results").show().html("");
                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            var val = obj[key];
                            $("#results").append('<span data-tag-id="' + val +
                                '" onclick="addtag(this)"><span class="badge badge-pill badge-dark">' +
                                key + '</span></span> ');
                        }
                    }
                } else {
                    $("#results").hide();
                }
            }
        })

        if (text != '') {
            $("#suggestCreatingTag").html(
                '<hr /><span class="badge badge-pill badge-warning" onclick="createTag(this)" data-tag-name="' +
                text +
                '">Create and add Tag: ' + text
                .toUpperCase() + '</span> ');
        } else {
            $("#suggestCreatingTag").html('');
        }

    }

    function addtag(e) {
        $.ajax({
            type: "POST",
            url: "includes/tag_handler.php",
            data: {
                media_id: document.getElementById('media_id').getAttribute('value'),
                tag_id: e.getAttribute('data-tag-id'),
                request_type: 'add_tag'
            },
            success: function(result) {
                console.log(result)
                showtags();
            }
        })
    }

    function createTag(e) {
        $.ajax({
            type: "POST",
            url: "includes/tag_handler.php",
            data: {
                media_id: document.getElementById('media_id').getAttribute('value'),
                tag_name: e.getAttribute('data-tag-name'),
                request_type: 'create_tag'
            },
            success: function(result) {
                console.log(result)
                showtags();
            }
        })

    }

    function deleteTag(e) {
        var check = confirm('Delete?');
        if (check) {
            $.ajax({
                type: "POST",
                url: "includes/tag_handler.php",
                data: {
                    media_id: document.getElementById('media_id').getAttribute('value'),
                    tag_id: e.getAttribute('data-tag-id'),
                    request_type: 'delete_tag'
                },
                success: function(result) {
                    console.log(result)
                    showtags();
                }
            })
        }
    }

    // For updating the entry
    $("#edit_entry").submit(function(e) {
        e.preventDefault();
        var title = $("#title").val();
        var description = $("#description").val();

        $.ajax({
            type: "POST",
            url: "includes/edit_handler.php",
            data: {
                id: document.getElementById('media_id').getAttribute('value'),
                title: title,
                description: description,
                submit: 'update_entry'
            },
            success: function(result) {
                console.log(result)
                if (result == 'title') {
                    document.getElementById('error').style.display = 'block';
                    $("#error").html('Enter a Title');
                    window.setTimeout(() => {
                            document.getElementById('error').style.display = 'none';
                        },
                        3000);
                }

                if (result == 'success') {
                    document.getElementById('success').style.display = 'block';
                    $("#success").html('Successful');
                    window.setTimeout(() => {
                            document.getElementById('success').style.display = 'none';
                        },
                        3000);

                    location = location; // page reload
                }
            }
        })
    })

    function showSimilar() {
        $.ajax({
            type: "POST",
            url: "includes/suggestor.php",
            data: {
                media_id: document.getElementById('media_id').getAttribute('value')
            },
            success: function(result) {
                $("#similar").html(result);
            }
        })
    }
    </script>
</body>

</html>