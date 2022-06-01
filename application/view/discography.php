<?php
chdir("../");
$currentDIR = getcwd();

require_once __DIR__ . '/../inc/user.php';
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../classes/Album.php';

include __DIR__ . '/../inc/header.php';


$albums = array();
$uniqeYears = array();
$songsId = array();
$albumCount = 0;

$query='SELECT * FROM hosj03.`album` ORDER BY ReleaseDate;';
$data = $db->query($query);

$albumsData = $data->fetchAll(PDO::FETCH_ASSOC);

if (!empty($albumsData)) {
    foreach ($albumsData as $album) {
        $albumCount++;
        if (preg_match('/^[0-9]{1,4}/', $album['ReleaseDate'])) {
            $year = substr($album['ReleaseDate'], 0, 4);
        }
        if (!in_array($year, $uniqeYears)) {
            $uniqeYears[] = $year;
        }
        $albums[] = new Album($album, $songsId);
    }

    $_SESSION['albums'] = $albums;
    $_SESSION['songsId'] = $songsId;
}

function printContents ($uniqeYears) {
    echo '<div class="contents">
            <div>
                <h2>Contents</h2>
            </div>
            <ol>';
    foreach ($uniqeYears as $uniqeYear) {
        echo '<li>
                    <a href="#year_'.htmlspecialchars($uniqeYear).'">'.htmlspecialchars($uniqeYear).'</a>
                </li>';
    }

    echo '  </ol>
            </div>';
}

function printAlbums($uniqeYears, $albums) {
    echo '<div>';

    foreach ($uniqeYears as $uniqeYear) {

        echo '<div id="year_'.htmlspecialchars($uniqeYear).'">
                <h2>'.htmlspecialchars($uniqeYear).'</h2>
                <div class="gallery_discography">';

        foreach ($albums as $album) {
            $album->printAlbum($uniqeYear);
        }

        echo '</div>';
    }


    echo '</div>';
}

?>


    <div class="breadcrumb_div">
        <ul class="breadcrumb_ul">
            <li class="breadcrumb_li">
                <a href="./../../index.php">Home</a>
                <p class="arrow">→</p>
            </li>
            <li class="breadcrumb_li">
                <p>Discography</p>
            </li>
        </ul>
    </div>
    <h1>
        Discography
    </h1>
    <div class="content">
        <p>
            Twice debuted with the album "The story begins" in 2015 and released over <?php echo (round($albumCount / 5) * 5) ?> albums, singles and repackaged albums so far.
        </p>
        <?php printContents($uniqeYears);?>
        <div>
            <?php printAlbums($uniqeYears, $albums);?>
        </div>
    </div>

<?php
include __DIR__ . '/../inc/footer.php';