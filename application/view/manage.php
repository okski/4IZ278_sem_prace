<?php
//načteme připojení k databázi a inicializujeme session
require_once __DIR__ . '/../inc/user.php';
require_once __DIR__ . '/../inc/db.php';

$errors = array();

if (!isset($_SESSION['IdUser']) || $_SESSION['Admin'] != 1) {
    include __DIR__ . '/../../error/400.html';
    exit();
}

$selectArtist = 'SELECT * FROM hosj03.artist;';
$selectArtistQuery = $db->query($selectArtist);
$artistData = $selectArtistQuery->fetchAll(PDO::FETCH_ASSOC);

$selectAlbum = 'SELECT * FROM hosj03.album;';
$selectAlbumQuery = $db->query($selectAlbum);
$albumData = $selectAlbumQuery->fetchAll(PDO::FETCH_ASSOC);

if (!empty($_POST)) {
    if (isset($_POST['addAlbum'])) {
        if ($_POST['IdArtist'] == 'default') {
            $errors['IdArtist'] = 'This artist does not exist.';
        }
        if (empty(trim($_POST['Name']))) {
            $errors['Name'] = 'You have to set some name of album.';
        }
        if (empty(trim($_POST['ReleaseDate'])) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['ReleaseDate'])) {
            $errors['ReleaseDate'] = 'You have to set date in YYYY-MM-DD format.';
        }
        if (empty(trim($_POST['NumberOfSongs'])) || !preg_match('/^\d{1,2}$/', (int)$_POST['NumberOfSongs']) ||
            $_POST['NumberOfSongs'] == 0) {
            $errors['NumberOfSongs'] = 'You have to set number between 1 and 99.';
        }
        if (empty(trim($_POST['Cover'])) || !preg_match('/^.*\.(jpg|png|webp|jpeg)$/', $_POST['Cover'])) {
            $errors['ReleaseDate'] = 'You have to set file name of cover.';
        }
        if (empty($errors)) {
            $saveQuery = $db->prepare('INSERT INTO hosj03.album (IdArtist, Name, ReleaseDate, NumberOfSongs, Cover)
                                VALUES (:IdArtist, :Name, :ReleaseDate, :NumberOfSongs, :Cover);');
            $saveQuery->execute([
                ':IdArtist'=>$_POST['IdArtist'],
                ':Name'=>$_POST['Name'],
                ':ReleaseDate'=>$_POST['ReleaseDate'],
                ':NumberOfSongs'=>$_POST['NumberOfSongs'],
                ':Cover'=>$_POST['Cover']
            ]);
            unset($_POST['addAlbum']);
            header('Location: ./discography.php');
        }
    } else if (isset($_POST['addSong'])) {
        if ($_POST['IdAlbum'] == 'default') {
            $errors['IdAlbum'] = 'This album does not exist.';
        }
        if (empty(trim($_POST['Name']))) {
            $errors['Name'] = 'You have to set some name of song.';
        }
        if (empty(trim($_POST['Length'])) || !preg_match('/^\d:\d{2}$/', $_POST['Length'])) {
            $errors['Length'] = 'You have to set date in YYYY-MM-DD format.';
        }
        if (empty($errors)) {
            var_dump('emptyErr');
            $saveQuery = $db->prepare('INSERT INTO hosj03.song (IdAlbum, Name, Length)
                                VALUES (:IdAlbum, :Name, :Length);');
            $saveQuery->execute([
                ':IdAlbum'=>$_POST['IdAlbum'],
                ':Name'=>$_POST['Name'],
                ':Length'=>$_POST['Length']
            ]);
            unset($_POST['addSong']);
            header('Location: ./discography.php');
        }
    } else if (isset($_POST['importAlbums'])) {
        $filename = $_FILES["file"]["tmp_name"];
        if (empty($filename)) {
            $errors['noAlbumsFile'] = 'Select some file.';
        }

        $db->beginTransaction();
        $sqlSaveQuery = 'INSERT INTO hosj03.album (IdArtist, Name, ReleaseDate, NumberOfSongs, Cover)
                                VALUES (:IdArtist, :Name, :ReleaseDate, :NumberOfSongs, :Cover);';
        $saveQuery = $db->prepare($sqlSaveQuery);

        if (empty($errors)) {
            if ($_FILES["file"]["size"] > 0) {
                $file = fopen($filename, "r");

                if (isset($_POST["headerInc"])) {
                    fgets($file);
                }

                while (!feof($file)) {
                    $lineArr = fgetcsv($file, 1000, ";");
                    if ($lineArr == null) {
                        continue;
                    }
                    try {
                        $saveQuery->execute([
                            ':IdArtist'=>$lineArr[0],
                            ':Name'=>$lineArr[1],
                            ':ReleaseDate'=>$lineArr[2],
                            ':NumberOfSongs'=>$lineArr[3],
                            ':Cover'=>$lineArr[4]
                        ]);
                    } catch (PDOException $e) {
                        echo "<span class='invalid'>The import ended with failure.</span>";
                    }
                }
                $db->commit();
                fclose($file);
                unset($_POST['importAlbums']);
                header('Location: ./discography.php');
            }
        }

    } else if (isset($_POST['importSongs'])) {
        $filename = $_FILES["file"]["tmp_name"];
        if (empty($filename)) {
            $errors['noSongsFile'] = 'Select some file.';
        }

        $db->beginTransaction();
        $sqlSaveQuery = 'INSERT INTO hosj03.song (IdAlbum, Name, Length)
                                VALUES (:IdAlbum, :Name, :Length);';
        $saveQuery = $db->prepare($sqlSaveQuery);

        if (empty($errors)) {
            if ($_FILES["file"]["size"] > 0) {
                $file = fopen($filename, "r");

                if (isset($_POST["headerInc"])) {
                    fgets($file);
                }

                while (!feof($file)) {
                    $lineArr = fgetcsv($file, 1000, ";");
                    if ($lineArr == null) {
                        continue;
                    }
                    try {
                        $saveQuery->execute([
                            ':IdAlbum'=>$lineArr[0],
                            ':Name'=>$lineArr[1],
                            ':Length'=>$lineArr[2]
                        ]);
                    } catch (PDOException $e) {
                        echo "<span class='invalid'>The import ended with failure.</span>";
                    }
                }
                $db->commit();
                fclose($file);
                unset($_POST['importSongs']);
                header('Location: ./discography.php');
            }
        }
    }

}


include __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumb_div">
    <ul class="breadcrumb_ul">
        <li class="breadcrumb_li">
            <a href="./../../index.php">Home</a>
            <p class="arrow">→</p>
        </li>
        <li class="breadcrumb_li">
            <p>Manage</p>
        </li>
    </ul>
</div>

<h1>Admin page</h1>

<div class="checkbox_box">
    <div class="field">
        <label for="album">Add album</label>
        <input type="checkbox" id="album" name="album" onclick="changeVisibilityOfChildElement('album', 'albumSubMenu')"
        <?php
        if (!empty($errors) && (!empty($errors['noAlbumsFile']) || isset($_POST['addAlbum']) || isset($_POST['importAlbums']))) {
            echo 'checked';}
        ?>
        />
    </div>
    <div id="albumSubMenu" <?php
    if (!empty($errors) && (!empty($errors['noAlbumsFile']) || isset($_POST['addAlbum']) || isset($_POST['importAlbums']))) {
        echo 'onload="changeVisibilityOfChildElement(\'album\', \'albumSubMenu\')"';
    } else {
        echo 'style="display: none;"';
    }
    ?>
    >
        <form method="post">
            <div class='field'>
                <label for='IdArtist'>Artist: </label>
                <select name='IdArtist' id='IdArtist'>
                    <option value='default'>Select artist</option>
                    <?php
                    if (!empty($artistData)) {
                        foreach ($artistData as $artist) {
                            echo '<option value=\''.$artist['IdArtist'].'\'';
                            if (!empty($errors) && $_POST['IdArtist'] == $artist['IdArtist']) {
                                echo 'selected';
                            }
                            echo '>'.$artist['Name'].'</option>';
                        }
                    }
                    ?>
                </select>
                <?php
                if (!empty($errors["IdArtist"])) {
                    print "<div class='text-danger'>".$errors["IdArtist"]."</div>";
                }
                ?>
            </div>
            <div class="field">
                <label for="Name">Name: </label>
                <input type="text" name="Name" id="Name" placeholder="ex. The story begins" pattern="^\S+(\s)?\S*$" required
                <?php if(!empty($errors) && empty($errors['noSongsFile']) && empty($errors['noAlbumsFile'])) {
                    errorHandler($_POST['Name'], $errors, 'Name');
                } else {
                    echo '>';
                }?>
                <br>
            </div>
            <div class="field">
                <label for="ReleaseDate">Release date: </label>
                <input type="text" name="ReleaseDate" id="ReleaseDate" placeholder="ex. 2022-05-23" pattern="^[0-9]{4}-[0-9]{2}-[0-9]{2}$"
                <?php if(!empty($errors) && empty($errors['noSongsFile']) && empty($errors['noAlbumsFile'])) {
                    errorHandler($_POST['ReleaseDate'], $errors, 'ReleaseDate');
                } else {
                    echo '>';
                }?>
                <br>
            </div>
            <div class="field">
                <label for="NumberOfSongs">Number of songs: </label>
                <input type="text" name="NumberOfSongs" id="NumberOfSongs" pattern="^[0-9]{1,2}$" placeholder="ex. 12" required
                <?php if(!empty($errors) && empty($errors['noSongsFile']) && empty($errors['noAlbumsFile'])) {
                    errorHandler($_POST['NumberOfSongs'], $errors, 'NumberOfSongs');
                } else {
                    echo '>';
                }?>
                <br>
            </div>
            <div class="field">
                <label for="Cover">Cover: </label>
                <input type="text" name="Cover" id="Cover" placeholder="ex. 01_The_story_begins.jpg" pattern="^\S*(\s)?\S*\.(jpg|png|webp|jpeg)$" required
                <?php if(!empty($errors) && empty($errors['noSongsFile']) && empty($errors['noAlbumsFile'])) {
                    errorHandler($_POST['Cover'], $errors, 'Cover');
                } else {
                    echo '>';
                }?>
                <br>
            </div>
            <button type="submit" name="addAlbum" value="addAlbum">Add</button>
        </form>
        <form method="post" enctype="multipart/form-data">
            <div class="field">
                <label for="headerInc">Includes header: </label>
                <input type="checkbox" name="headerInc" id="headerInc" style="width: 50px">
                <br>
            </div>
            <div class="field">
                <label for="file">File: </label>
                <input type="file" name="file" id="file" accept=".text,.csv">
                <br>
            </div>
            <?php if(!empty($errors['noAlbumsFile'])) {
                echo '<span class="text-danger">'.$errors['noAlbumsFile'].'</span><br>';
            }?>
            <input type="submit" class="button" name="importAlbums" value="Import albums">
        </form>
    </div>
    <div class="field">
        <label for="song">Add song</label>
        <input type="checkbox" id="song" name="song" onclick="changeVisibilityOfChildElement('song', 'songSubMenu')"
        <?php
        if (!empty($errors) && (!empty($errors['noSongsFile']) || isset($_POST['addSong']) || isset($_POST['importSongs']))) {
            echo 'checked';}
        ?>
        />
    </div>
    <div id="songSubMenu"
    <?php
    if (!empty($errors) && (!empty($errors['noSongsFile']) || isset($_POST['addSong']) || isset($_POST['importSongs']))) {
        echo 'onload="changeVisibilityOfChildElement(\'album\', \'albumSubMenu\')"';
    } else {
        echo 'style="display: none;"';
    }
    ?>
    >
        <form method="post">
            <div class='field'>
                <label for='IdAlbum'>Album: </label>
                <select name='IdAlbum' id='IdAlbum'>
                    <option value='default'>Select album</option>
                    <?php
                    if (!empty($albumData)) {
                        foreach ($albumData as $album) {
                            echo '<option value=\''.$album['IdAlbum'].'\'';
                            if (!empty($errors) && $_POST['IdAlbum'] == $album['IdAlbum']) {
                                echo 'selected';
                            }
                            echo '>'.htmlspecialchars($album['Name']).'</option>';
                        }
                    }
                    ?>
                </select>
                <?php
                if (!empty($errors["IdAlbum"])) {
                    print "<div class='text-danger'>".$errors["IdAlbum"]."</div>";
                }
                ?>
            </div>
            <div class="field">
                <label for="Name">Name: </label>
                <input type="text" name="Name" id="Name" placeholder="ex. Like Ooh-Ahh" pattern="^\S+(\s)?\S*$" required
                <?php if(!empty($errors) && empty($errors['noSongsFile']) && empty($errors['noAlbumsFile'])) {
                    errorHandler($_POST['Name'], $errors, 'Name');
                } else {
                    echo '>';
                }?>
                <br>
            </div>
            <div class="field">
                <label for="Length">Length: </label>
                <input type="text" name="Length" id="Length" placeholder="ex. 3:21" pattern="^[0-9]{1}:[0-9]{2}$"
                <?php if(!empty($errors) && empty($errors['noSongsFile']) && empty($errors['noAlbumsFile'])) {
                    errorHandler($_POST['Length'], $errors, 'Length');
                } else {
                    echo '>';
                }?>
                <br>
            </div>
            <button type="submit" name="addSong" value="addSong">Add</button>
        </form>
        <form method="post" enctype="multipart/form-data">
            <div class="field">
                <label for="headerInc">Includes header: </label>
                <input type="checkbox" name="headerInc" id="headerInc" style="width: 50px">
                <br>
            </div>
            <div class="field">
                <label for="file">File: </label>
                <input type="file" name="file" id="file" accept=".text,.csv">
                <br>
            </div>
            <?php if(!empty($errors['noSongsFile'])) {
                echo '<span class="text-danger">'.$errors['noSongsFile'].'</span><br>';
            }?>
            <input type="submit" class="button" name="importSongs" value="Import songs">
        </form>
    </div>
</div>

<script src="./../../resources/js/admin.js"></script>

<?php
include __DIR__ . '/../inc/footer.php';

function errorHandler ($data, $errors, $index) {
    echo 'value="'.htmlspecialchars($data).'">';

    if (!empty($errors[$index])) {
        print "<div class='text-danger'>".$errors[$index]."</div>";
    }
}