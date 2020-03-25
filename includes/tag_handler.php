<?php
$error="";

if (array_key_exists("request_type", $_POST) and $_POST['request_type']!='') {
    include('connect-db.php');
} else {
    header("Location: index.php");
}

if ($_POST['request_type']=='show_tags' and is_numeric($_POST['media_id'])) {
    // get a list of all tags for this video
    $query_tags="SELECT * FROM `media_tag` WHERE media_id=".mysqli_real_escape_string($link, $_POST['media_id']);
    $result_tags = mysqli_query($link, $query_tags);
    if (mysqli_num_rows($result_tags)!=0) {
        while ($row_tags = mysqli_fetch_array($result_tags)) {
            // get details of each tag
            $query_tag_id="SELECT * FROM `media_list_of_tags` WHERE id=".mysqli_real_escape_string($link, $row_tags['tag_id'])." LIMIT 1";
            $result_tag_id = mysqli_query($link, $query_tag_id);
            $row_tag_id = mysqli_fetch_array($result_tag_id);

            echo '<span class="badge badge-danger">'.$row_tag_id['tag_name'].' <span class="badge badge-secondary delete" onclick="deleteTag(this)" data-tag-id="'.$row_tag_id['id'].'">X</span></span> ';
        }
    } else {
        echo 'Be the first to add some tags';
    }
}

if ($_POST['request_type']=='search') {
    $tags_array = [];
    
    if ($_POST['text']!='') {
        $query_search = "SELECT * FROM `media_list_of_tags` WHERE tag_name LIKE '%".mysqli_real_escape_string($link, $_POST['text'])."%' LIMIT 20";
        $result_search = mysqli_query($link, $query_search);
        if (mysqli_num_rows($result_search)!=0) {
            while ($row_search = mysqli_fetch_array($result_search)) {
                $tags_array[$row_search['tag_name']] = $row_search['id'];
            }
        } else {
            $tags_array = ['error'=>'No results found'];
        }
    } else {
        $tags_array = ['error'=>'Empty Search'];
    }
    echo json_encode($tags_array);
}

if ($_POST['request_type']=='add_tag' and is_numeric($_POST['media_id']) and is_numeric($_POST['tag_id'])) {
    $query_search = "SELECT * FROM `media_tag` WHERE 
    media_id = '".mysqli_real_escape_string($link, $_POST['media_id'])."' AND 
    tag_id = '".mysqli_real_escape_string($link, $_POST['tag_id'])."'";

    $result_search = mysqli_query($link, $query_search);
    
    if (mysqli_num_rows($result_search)==0) {
        $query_add_tag = "INSERT INTO `media_tag` (media_id, tag_id) 
        VALUES (
        '".mysqli_real_escape_string($link, $_POST['media_id'])."',
        '".mysqli_real_escape_string($link, $_POST['tag_id'])."');";
  
        if (mysqli_query($link, $query_add_tag)) {
            $entry_id = mysqli_insert_id($link); //get id of most recent inserted row
            echo $entry_id;
        }
    } else {
        echo 'tag already exists for this media';
    }
}

if ($_POST['request_type']=='create_tag' and is_numeric($_POST['media_id']) and $_POST['tag_name']!='') {
    // check if that tag already exists
    $query_search = "SELECT * FROM `media_list_of_tags` WHERE  
    tag_name = '".mysqli_real_escape_string($link, $_POST['tag_name'])."'";

    $result_search = mysqli_query($link, $query_search);
    
    if (mysqli_num_rows($result_search)==0) {
        // create tag
        $query_create_tag = "INSERT INTO `media_list_of_tags` (tag_name, description) 
        VALUES (
        '".mysqli_real_escape_string($link, $_POST['tag_name'])."',
        '".mysqli_real_escape_string($link, '')."');";
  
        if (mysqli_query($link, $query_create_tag)) {
            // add tag to media
            $entry_id = mysqli_insert_id($link);
            $query_add_tag = "INSERT INTO `media_tag` (media_id, tag_id) 
                VALUES (
                '".mysqli_real_escape_string($link, $_POST['media_id'])."',
                '".mysqli_real_escape_string($link, $entry_id)."');";
  
            if (mysqli_query($link, $query_add_tag)) {
                echo 'Success';
            } else {
                echo 'Tag couldn\'t be added';
            }
        } else {
            echo 'Tag could not be created';
        }
    } else {
        echo 'Tag already exists';
    }
}

if ($_POST['request_type']=='delete_tag' and is_numeric($_POST['media_id']) and is_numeric($_POST['tag_id'])) {
    // check if that tag already exists
    $query_delete = "DELETE FROM `media_tag` WHERE 
    media_id = '".mysqli_real_escape_string($link, $_POST['media_id'])."' AND 
    tag_id='".mysqli_real_escape_string($link, $_POST['tag_id'])."'";
    if (mysqli_query($link, $query_delete)) {
        echo 'Success';
    } else {
        echo 'Tag couldn\'t be deleted'.$query_delete;
    }
}