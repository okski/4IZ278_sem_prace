<?php
if (empty($_GET['IdSong'])) {
    include __DIR__ . '/../../error/400.html';
    exit();
}

require_once __DIR__ . '/../inc/user.php';
require_once __DIR__ . '/../inc/db.php';


$idReview = 'add';
$song = array();
$userReview = array();
$remainingReviews = array();
$atLeastOneArticle = 1;
$sumOfReview = 0;
$countOfReview = 0;

$reviewsQuery = $db->prepare('SELECT *, song.Name as Name, a.Name as AlbumName FROM hosj03.`song` JOIN reviewOfSong rOS on song.IdSong = rOS.IdSong JOIN user u on u.IdUser = rOS.IdUser JOIN album a on a.IdAlbum = song.IdAlbum WHERE song.IdSong=:IdSong;');
$reviewsQuery->execute([
        ':IdSong'=>$_GET['IdSong']
]);
$reviews = $reviewsQuery->fetchAll(PDO::FETCH_ASSOC);

if (!empty($reviews)) {
    foreach ($reviews as $review) {
        $sumOfReview += $review['Rating'];
        $countOfReview++;
        if(isset($_SESSION['IdUser']) && $_SESSION['IdUser'] == $review['IdUser']) {
            $userReview = $review;
        } else {
            $remainingReviews[] = $review;
            if (!empty($review['Article'])) {
                $atLeastOneArticle = 0;
            }
        }
    }
} else {
    $songQuery = $db->prepare('SELECT *, song.Name as Name, a.Name as AlbumName FROM hosj03.song JOIN album a on a.IdAlbum = song.IdAlbum WHERE IdSong=:IdSong LIMIT 1;');
    $songQuery->execute([
        ':IdSong'=>$_GET['IdSong']
    ]);
    $song = $songQuery->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($reviews) && empty($song)) {
    include __DIR__ . '/../../error/400.html';
    exit();
}

if (!empty($_GET['delete']) && (empty($userReview) || empty($userReview['Article']))) {
    include __DIR__ . '/../../error/400.html';
    exit();
}

if (!empty($userReview) && !empty($userReview['Article']) &&
    !empty($_GET['delete']) && $_GET['delete'] == 'delete') {
    $updateQuery = $db->prepare('UPDATE hosj03.reviewOfSong SET Article=\'\' WHERE IdReview=:IdReview;');
    $updateQuery->execute([':IdReview'=>$userReview['IdReview']]);
    unset($_GET['delete']);
    header('Location: review.php?IdSong='.$_GET['IdSong']);
}

$errors = array();

if (!empty($_POST)) {
    if (empty($_POST['text'])) {
        $errors['empty'] = 'You must fill textarea with text.';
    } else {
        $postText = trim(@$_POST['text']);
        if (empty($postText)) {
            $errors['empty'] = 'You must fill textarea with text.';
        }
        if (empty($userReview)) {
            $errors['star'] = 'Give it some star rating before submitting article.';
        }
    }
    if (empty($errors)) {
        $saveQuery = $db->prepare('UPDATE hosj03.reviewOfSong SET Article=:Article, UpdatedAt=:UpdatedAt WHERE IdReview=:IdReview LIMIT 1;');
        $saveQuery->execute([
            ':Article'=>$_POST['text'],
            ':IdReview'=>$userReview['IdReview'],
            ':UpdatedAt'=>date('y-m-d')
        ]);
        unset($_POST);
        header('Location: review.php?IdSong='.$_GET['IdSong']);
        exit();
    }
}


if (!empty($_GET['Stars']) && $_GET['Stars'] >= 1 && $_GET['Stars'] <= 5) {
    if (!empty($userReview)) {
        $saveQuery = $db->prepare('UPDATE hosj03.reviewOfSong SET Rating=:Rating, UpdatedAt=:UpdatedAt WHERE IdReview=:IdReview LIMIT 1;');
        $saveQuery->execute([
            ':Rating'=>$_GET['Stars'],
            ':IdReview'=>$userReview['IdReview'],
            ':UpdatedAt'=>date('y-m-d')
        ]);
        unset($_GET['Stars']);
    } else {
        $saveQuery = $db->prepare('INSERT INTO hosj03.reviewOfSong (IdUser, IdSong, CreatedAt, UpdatedAt, Rating) VALUES (:IdUser, :IdSong, :CreatedAt, :UpdatedAt, :Rating);');
        $saveQuery->execute([
            ':IdUser'=>$_SESSION['IdUser'],
            ':IdSong'=>$_GET['IdSong'],
            ':CreatedAt'=>date('y-m-d'),
            ':UpdatedAt'=>date('y-m-d'),
            ':Rating'=>$_GET['Stars'],
        ]);
        unset($_GET['Stars']);
    }
    header('Location: review.php?IdSong='.$_GET['IdSong']);
    exit();
}

include __DIR__ . '/../inc/header.php';?>


<h1>Review of '
    <?php
        if (empty($reviews)) {
            echo htmlspecialchars($song[0]['Name']);
        } else {
            echo htmlspecialchars($reviews[0]['Name']);
        }
    ?>
'</h1>

<div class="rating_box">
    <div class="avarage_rating">
        <?php printAverageRating($sumOfReview, $countOfReview)?>
    </div>
    <div class="clearance"></div>
    <div class="user_rating">
        <?php printRatingStars($userReview)?>
    </div>
</div>

<p>
    <?php
        if (empty($reviews)) {
            echo htmlspecialchars($song[0]['Name']).' is one of '.$song[0]['NumberOfSongs'].' songs in album '.$song[0]['AlbumName'];
        } else {
            echo htmlspecialchars($reviews[0]['Name']).' is one of '.$reviews[0]['NumberOfSongs'].' songs in album '.$reviews[0]['AlbumName'];
        }
    ?>
</p>

<svg id="stars" style="display: none;">
    <symbol id="stars-empty-star" viewBox="0 0 102 18" fill="#F1E8CA">
        <path d="M9.5 14.25l-5.584 2.936 1.066-6.218L.465 6.564l6.243-.907L9.5 0l2.792 5.657 6.243.907-4.517 4.404 1.066 6.218" />
    </symbol>
    <symbol id="stars-full-star" viewBox="0 0 102 18" fill="#D3A81E">
        <path d="M9.5 14.25l-5.584 2.936 1.066-6.218L.465 6.564l6.243-.907L9.5 0l2.792 5.657 6.243.907-4.517 4.404 1.066 6.218" />
    </symbol>
</svg>
<div class="clearance"></div>


<?php
if (!empty($_SESSION['IdUser']) && (empty($userReview) || empty($userReview['Article']) || (!empty($_GET['edit']) && $_GET['edit'] == 'edit'))) {
    echo '<form method="post" class="comment">
    <input type="hidden" name="id" value="'.$idReview.'">

    <div>
        <label for="text">Comment:</label>
        <br>
        <textarea name="text" id="text" required class="comment_textarea">';
    if (!empty($_GET['edit']) && $_GET['edit'] == 'edit') {
        echo htmlspecialchars($userReview['Article']);
    }
        echo '</textarea>
    </div>';

    if (!empty($_GET['edit']) && $_GET['edit'] == 'edit') {
        echo '<button type="submit" class="btn">Edit</button>';
    } else {
        echo '<button type="submit" class="btn">Add</button>';
    }
    echo '</form>';
}

if (!empty($errors['star'])) {
    echo '<div class="invalid-feedback">'.$errors['star'].'</div>';
}

if (!empty($errors['empty'])) {
    echo '<div class="invalid-feedback">'.$errors['empty'].'</div>';
}

?>
<div class="review_box">
    <h2 class="review_title">
        Articles
    </h2>
    <div class="reviews">
        <?php printReviews($userReview, $remainingReviews, $atLeastOneArticle);?>
    </div>

</div>

<?php

function printStars($stars) {
    echo '<svg aria-hidden="true" focusable="false" class="rating">';

    for ($i = 0; $i < $stars; $i++) {
        echo '<a href="?IdSong='.$_GET['IdSong'].'&Stars='.($i+1).'"><use xlink:href="#stars-full-star"></use></a>';
    }

    for ($i = 0; $i < 5 - $stars; $i++) {
        echo '<a href="?IdSong='.$_GET['IdSong'].'&Stars='.($i+$stars+1).'"><use xlink:href="#stars-empty-star"></use></a>';
    }
    echo '</svg>';
}

function printStarsArticle($stars) {
    echo '<svg aria-hidden="true" focusable="false" class="rating">';

    for ($i = 0; $i < $stars; $i++) {
        echo '<use xlink:href="#stars-full-star"></use>';
    }

    for ($i = 0; $i < 5 - $stars; $i++) {
        echo '<use xlink:href="#stars-empty-star"></use>';
    }
    echo '</svg>';
}

function printReviews($userReview, $remainingReviews, $atLeastOneArticle) {
    if ((empty($userReview) || empty($userReview['Article'])) && (empty($remainingReviews) || $atLeastOneArticle)) {
        echo '<p>None have written review for this song yet. <span class="bold">Be the first one.</span></p>';
    } else {
        if (!empty($userReview) && !empty($userReview['Article'])) {
            echo '<article class="review"><div class="header_article"><h3>'.htmlspecialchars($userReview['Username']).' <span>';
            printStarsArticle($userReview['Rating']);
            echo '</span></h2> <span class="change"><a href="?IdSong='.$_GET['IdSong'].'&edit=edit" >edit</a>
<a href="?IdSong='.$_GET['IdSong'].'&delete=delete">delete</a></span></div>
                    <p>'.htmlspecialchars($userReview['Article']).'
                    <span class="timestamp">'.htmlspecialchars(date("d. m. Y", strtotime($userReview['UpdatedAt']))).'</span></p>
                    
                </article>';
        }

        if (!empty($remainingReviews)) {
            foreach ($remainingReviews as $review) {
                if (!empty($review['Article'])) {
                    echo '<article class="review"><div class="header_article"><h3>'.htmlspecialchars($review['Username']).' <span>';
                    printStarsArticle($review['Rating']);
                    echo '</span></h2></div>
                    <p>'.htmlspecialchars($review['Article']).'
                    <span class="timestamp">'.htmlspecialchars(date("d. m. Y", strtotime($review['UpdatedAt']))).'</span></p>
                    
                </article>';
                }
            }
        }
    }

}

function printRatingStars($userReview) {
    if (!isset($_SESSION['IdUser'])) {
        return;
    } else {
        if (!empty($_SESSION['IdUser'])) {
            if (!empty($userReview)) {
                printStars($userReview['Rating']);
            } else {
                printStars(0);
            }
        }
    }

}

function printAverageRating($sumOfReview, $countOfReview) {
    if ($countOfReview == 0) {
        echo '<span class="">None</span>';
    } else {
        echo round(($sumOfReview/$countOfReview) * 2 * 10).'%';
    }
}

include __DIR__ . '/../inc/footer.php';