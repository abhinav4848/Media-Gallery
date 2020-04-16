<nav class="navbar navbar-expand-lg navbar-light bg-light mb-1">
    <a class="navbar-brand" href="index.php">Media Server</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="upload.php">Upload</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Choose Type
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="view-all.php?type=vids">Vids</a>
                    <a class="dropdown-item" href="view-all.php?type=pics">Pics</a>
                    <a class="dropdown-item" href="view-all.php?type=gifs">Gifs</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="multifile_uploader.php">Upload Multi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="similar.php">Similar Images</a>
            </li>
        </ul>
    </div>
</nav>