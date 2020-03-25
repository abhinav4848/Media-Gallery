<?php
session_start();
$error="";
include('connect-db.php');

if (array_key_exists("submit", $_POST) and $_POST['submit']=='update_entry') {
    //check if submitting edit
    if ($_POST['title']=='') {
        echo 'title';
    } else {
        $query_update = "UPDATE `media_entries` SET 
        title = '".mysqli_real_escape_string($link, $_POST['title'])."',
        description = '".mysqli_real_escape_string($link, $_POST['description'])."'
        WHERE id = '".mysqli_real_escape_string($link, $_POST['id'])."' LIMIT 1";
    
        if (mysqli_query($link, $query_update)) {
            echo 'success';
        } else {
            echo "failed to update the entry.".$query_update;
        }
    }
    die();
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
        rename('storage/files/'. $row['filename_final'], $path.$row['filename_final'].'-deleted-'.time().'.'.$row['filename_ext']);
        echo 'success'; // ajax request
    } else {
        echo '<div id="tablediv">';
        echo "failed to delete the entry.";
        echo $query_delete;
        echo '</div>';
    }
    die();
}