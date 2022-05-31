<?php
require_once __DIR__ . '/Song.php';
require_once __DIR__ . '/../inc/db.php';

class Album {
    private $IdAlbum, $IdArtist, $Name, $ReleaseDate, $NumberOfSongs, $Cover, $Songs = array();

    /**
     * @param $IdAlbum
     * @param $IdArtist
     * @param $Name
     * @param $ReleaseDate
     * @param $NumberOfSongs
     * @param $Cover
     */
    public function __construct(array $data, array &$songsId)
    {
        $this->IdAlbum = (int)$data['IdAlbum'];
        $this->IdArtist = (int)$data['IdArtist'];
        $this->Name = (string)$data['Name'];
        $this->ReleaseDate = (string)$data['ReleaseDate'];
        $this->NumberOfSongs = (int)$data['NumberOfSongs'];
        $this->Cover = (string)$data['Cover'];
        $this->initializeSongs($songsId);
    }

    private function initializeSongs (array &$songsId) {
        require __DIR__ . '/../inc/db.php';
        $query='SELECT * FROM hosj03.`song` WHERE IdAlbum=:IdAlbum;';
        $qPrepare = $db->prepare($query);
        $qPrepare->bindParam(":IdAlbum", $this->IdAlbum);
        $qPrepare->execute();
        $songsData = $qPrepare->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($songsData)) {
            foreach ($songsData as $song) {
                $this->Songs[] = new Song($song, $songsId);
            }
        }
    }

    /**
     * @return int
     */
    public function getIdAlbum(): int
    {
        return $this->IdAlbum;
    }

    /**
     * @return int
     */
    public function getIdArtist(): int
    {
        return $this->IdArtist;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->Name;
    }

    /**
     * @return string
     */
    public function getReleaseDate(): string
    {
        return $this->ReleaseDate;
    }

    /**
     * @return int
     */
    public function getNumberOfSongs(): int
    {
        return $this->NumberOfSongs;
    }

    /**
     * @return string
     */
    public function getCover(): string
    {
        return $this->Cover;
    }

    public function printAlbum($uniqeYear) {
        if (substr($this->ReleaseDate, 0, 4) == $uniqeYear) {
            echo '<div>
                        <h3>'.htmlspecialchars($this->Name).'</h3>
                        <img src="../../resources/img_c/discography/'.htmlspecialchars($uniqeYear).'/'.htmlspecialchars($this->Cover).'" alt="Album of The story begins" width="600" height="600">
                        <ol>';

            $this->printSongs();

            echo '      </ol>
                    </div>
                </div>';
        }
    }

    private function printSongs () {
        foreach ($this->Songs as $song) {
            $song->printSongDiscography();
        }

    }

}