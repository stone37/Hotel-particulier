<?php

namespace App\Twig;

use App\Helper\TimeHelper;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigTimeExtension extends AbstractExtension
{
    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('duration', [$this, 'duration']),
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']]),
            new TwigFilter('countdown', [$this, 'countdown'], ['is_safe' => ['html']]),
            new TwigFilter('duration_short', [$this, 'shortDuration'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Génère une durée au format "30 min".
     */
    public function duration(int $duration): string
    {
        return TimeHelper::duration($duration);
    }

    /**
     * Génère une durée au format court hh:mm:ss.
     */
    public function shortDuration(int $duration): string
    {
        $minutes = floor($duration / 60);
        $seconds = $duration - $minutes * 60;
        /** @var int[] $times */
        $times = [$minutes, $seconds];
        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes - ($hours * 60);
            $times = [$hours, $minutes, $seconds];
        }

        return implode(':', array_map(
            function (int $duration) {
                str_pad(strval($duration), 2, '0', STR_PAD_LEFT);
            },
            $times
        ));
    }

    /**
     * Génère une date au format "Il y a" gràce à un CustomElement.
     */
    public function ago(DateTimeInterface $date, string $prefix = ''): string
    {
        $prefixAttribute = !empty($prefix) ? " prefix=\"{$prefix}\"" : '';

        return "<span data-time=\"{$date->getTimestamp()}\" $prefixAttribute ></span>";
    }

    public function countdown(DateTimeInterface $date): string
    {
        return "<span data-time=\"{$date->getTimestamp()}\"></span>";
    }
}

