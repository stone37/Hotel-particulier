<?php

namespace App\Twig;

use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('date_french', [$this, 'ago']),
        ];
    }

    public function ago(DateTimeInterface $date, bool $prefix = false, bool $days = false): string
    {
        $date = explode('|', $date->format("w|d|n|Y"));

        $day = $this->days($prefix);
        $month = $this->month($prefix);


        return $days ?
            $date[1] . ' ' . $month[$date[2]-1] . ' ' . $date[3] :
            $day[$date[0]] . ' ' . $date[1] . ' ' . $month[$date[2]-1] . ' ' . $date[3];
    }

    private function month($prefix = false)
    {
        if ($prefix) {
            return ["janv", "févr", "mars", "avr", "mai", "juin", "juill", "août", "sept", "oct", "nov", "déc"];
        } else {
            return ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
        }
    }

    private function days($prefix = false): array
    {
        if ($prefix) {
            return ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
        } else {
            return ["Dimanche" ,"Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
        }
    }
}
