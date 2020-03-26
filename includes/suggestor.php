<?php
$error="";
include('connect-db.php');

if (array_key_exists("media_id", $_POST) and is_numeric($_POST['media_id'])) {
    // get all entries for this media_id in `media_tag` table
    $query_tags="SELECT * FROM `media_tag` WHERE media_id=".mysqli_real_escape_string($link, $_POST['media_id']);
    $result_tags = mysqli_query($link, $query_tags);
    
    if (mysqli_num_rows($result_tags)!=0) {
        while ($row_tags = mysqli_fetch_array($result_tags)) {
            // pick one row at a time
            // get detail about the tag from that row
            $query_tag_id="SELECT * FROM `media_list_of_tags` WHERE id=".mysqli_real_escape_string($link, $row_tags['tag_id'])." LIMIT 1";
            $result_tag_id = mysqli_query($link, $query_tag_id);
            $row_tag_id = mysqli_fetch_array($result_tag_id);


            // using the tag_id, get all media_id with same tag, except this very post
            $query_similar_posts = "SELECT * FROM `media_tag` WHERE tag_id=".mysqli_real_escape_string($link, $row_tags['tag_id'])." and NOT media_id=".mysqli_real_escape_string($link, $_POST['media_id'])." ORDER BY RAND() LIMIT 20";
            $result_similar_posts = mysqli_query($link, $query_similar_posts);
            
            if (mysqli_num_rows($result_tag_id)!=0) {
                echo '<h6>Posts with tag: '.$row_tag_id['tag_name'].'</h6>';
                while ($row_similar_posts = mysqli_fetch_array($result_similar_posts)) {
                    // loop over each media_id and get its full detail
                    $query_entry = "SELECT * FROM `media_entries` WHERE id=".mysqli_real_escape_string($link, $row_similar_posts['media_id'])." LIMIT 1";
                    $result_entry = mysqli_query($link, $query_entry);
                    $row_entry = mysqli_fetch_array($result_entry);

                    echo '<a href="./view.php?id='.$row_similar_posts['media_id'].'">'.$row_entry['title'].'</a> <span class="badge badge-primary">'.$row_entry['filename_ext'].'</span></br>';
                }
            }
        }
    }
}