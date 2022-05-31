<?php

class Song {
    private $IdSong, $IdAlbum, $Name, $Length;

    public function __construct(array $data, array &$songsId) {
        $this->IdSong = (int)$data['IdSong'];
        $this->IdAlbum = (int)$data['IdAlbum'];
        $this->Name = (string)$data['Name'];
        $this->Length = (string)$data['Length'];
        $songsId[] = (int)$data['IdSong'];
    }


    public function printSongDiscography() {
        echo '<li id="'.htmlspecialchars($this->IdSong).'"><a href="review.php?IdSong='.htmlspecialchars($this->IdSong).'">'.htmlspecialchars($this->Name).'</a></li>';
    }
}