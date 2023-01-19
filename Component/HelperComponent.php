<?php

namespace DecodoMastodonService\Component;

use DateTime;
use DateTimeImmutable;

class HelperComponent
{
    public static function timeAgo($time)
    {
        $now = new DateTime("now");
        $backThen = new DateTimeImmutable($time);
        $interval = $now->diff($backThen);

        $arr = [
            'y' => ['Jahr', 'Jahre'],
            'm' => ['Monat', 'Monate'],
            'd' => ['Tag', 'Tage'],
            'h' => ['Stunde', 'Stunden'],
            'i' => ['Minute', 'Minuten'],
            's' => ['Sekunde', 'Sekunden'],
        ];

        foreach ($arr as $f =>$l){
            if ($interval->{$f } > 0) {
                return $interval->{$f } . ' '.$l[($interval->{$f } <= 1 ? 0:1)];
            }
        }

    }

    public static function getPlaceholderImage($title,$style='',$class='')
    {
        //bd-placeholder-img card-img-top width="100%" height="225"
        $class.= ' bd-placeholder-img';
        return '<svg  '.(!empty($class) ? ' class="'.trim($class).'" ':'').' '.(!empty($style) ? ' style="'.$style.'" ':'').
            'xmlns="http://www.w3.org/2000/svg" width="100%" role="img" aria-label="Placeholder: Thumbnail" ' .
            'preserveAspectRatio="xMidYMid slice" focusable="false">' .
            '<title>Keine Vorschau</title><rect width="100%" height="100%" fill="#55595c"/>' .
            '<text x="50%" y="50%" fill="#eceeef" dy=".3em">Keine Vorschau</text></svg>';
    }
}
