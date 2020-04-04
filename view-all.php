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



/*Paginator for filtering, it only fetches those particular rows which are required based on the page number and rows per page.*/
$sql = "SELECT COUNT(*) FROM `media_entries` WHERE ".$whereclause;
$resultCount = mysqli_query($link, $sql) or trigger_error("SQL", E_USER_ERROR);
$r = mysqli_fetch_row($resultCount);
$numrows = $r[0];

// number of rows to show per page
$rowsperpage = 10;
// find out total pages
$totalpages = ceil($numrows / $rowsperpage);

// get the current page or set a default
if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
    // cast var as int
    $currentpage = (int) $_GET['currentpage'];
} else {
    // default page num
    $currentpage = 1;
}

// if current page is greater than total pages...
if ($currentpage > $totalpages) {
    // set current page to last page
    $currentpage = $totalpages;
}

// if current page is less than first page...
if ($currentpage < 1) {
    // set current page to first page
    $currentpage = 1;
}

// the offset of the list, based on current page
$offset = ($currentpage - 1) * $rowsperpage;

// get the info from the db
$query = "SELECT * FROM `media_entries` WHERE ".$whereclause." ORDER BY id LIMIT $offset, $rowsperpage";
$result = mysqli_query($link, $query) or die(mysql_error());

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
        <?php
        /******  build the pagination links ******/
        echo '<nav aria-label="page navigation">
		<ul class="pagination justify-content-center">';

        // range of num links to show
        $range = 1;

        // if not on page 1, don't show back links
        if ($currentpage > 1) {
            // show << link to go back to page 1
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?type={$_GET['type']}&currentpage=1'><<</a> ";
            echo '</li>';
            // get previous page num
            $prevpage = $currentpage - 1;
            // show < link to go back to 1 page
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?type={$_GET['type']}&currentpage=$prevpage'>Prev</a> ";
            
            echo '</li>';
        } // end if

        // loop to show links to range of pages around current page
        for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
            // if it's a valid page number...
            if (($x > 0) && ($x <= $totalpages)) {
                // if we're on current page...
                if ($x == $currentpage) {
                    // 'highlight' it but don't make a link
                    echo '<li class="page-item active">';
                    echo " <a class='page-link' href='#'>$x</a> ";
                    echo '</li>';
                } else {
                    // if not current page...
                    // make it a link
                    echo '<li class="page-item">';
                    echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?type={$_GET['type']}&currentpage=$x'>$x</a> ";
                    echo '</li>';
                } // end else
            } // end if
        } // end for

        // if not on last page, show forward and last page links
        if ($currentpage != $totalpages) {
            // get next page
            $nextpage = $currentpage + 1;
            // echo forward link for next page
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?type={$_GET['type']}&currentpage=$nextpage'>Next</a> ";
            echo '</li>';
            // echo forward link for lastpage
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?type={$_GET['type']}&currentpage=$totalpages'>>></a> ";
            echo '</li>';
        } // end if
        echo '</ul>
        </nav>';
        /****** end build pagination links ******/
        ?>

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