<?php

namespace App\Twig;

use App\Constants\Constants;
use App\Entity\Media;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getName', [$this, 'getName']),
            new TwigFunction('getThumbnailPath', [$this, 'getThumbnailPath']),
            new TwigFunction('getGalleryPath', [$this, 'getGalleryPath']),
            new TwigFunction('const', [$this, 'const']),
            new TwigFunction('truncateHtml', [$this, 'truncateHtml']),
            new TwigFunction('formatCustomDate', [$this, 'formatCustomDate']),
            new TwigFunction('formatCustomDateWithoutYears', [$this, 'formatCustomDateWithoutYears']),


        ];
    }

    public function getName($filename): string
    {
        return Media::getNameOf($filename);
    }

    public function getThumbnailPath($filename): string
    {
        return Constants::DYN_IMG_PATH.$filename;
    }

    public function getGalleryPath($filename): string
    {
        return Constants::GALLERY_PATH.$filename;
    }

    public function const(string $value): string
    {
        $value = strtoupper($value);
        if(defined("App\Constants\Constants::$value")) {
            return constant("App\Constants\Constants::$value");
        }else{
            return '';
        }
    }

    public function truncateHtml($input, $length)
    {
        $plainText = strip_tags($input);

        if (mb_strlen($plainText) <= $length) {
            return $input;
        }

        $truncatedText = mb_substr($plainText, 0, $length);

        $lastOpenTag = strrpos($truncatedText, '<');

        $lastCloseTag = strrpos($truncatedText, '>');

        if ($lastOpenTag > $lastCloseTag) {
            $truncatedText = substr($truncatedText, 0, $lastOpenTag);
        }

        $result = substr($input, 0, mb_strlen($truncatedText));

        return $result;

    }


    public function formatCustomDate($date)
    {
        if($date != null and $date instanceof \DateTimeInterface ) {

            $dayOfWeek = [
              'Mon' => 'LUN.',
              'Tue' => 'MAR.',
              'Wed' => 'MER.',
              'Thu' => 'JEU.',
              'Fri' => 'VEN.',
              'Sat' => 'SAM.',
              'Sun' => 'DIM.',
            ];

            $monthNames = [
              'Jan' => 'JANVIER',
              'Feb' => 'FÉVRIER',
              'Mar' => 'MARS',
              'Apr' => 'AVRIL',
              'May' => 'MAI',
              'Jun' => 'JUIN',
              'Jul' => 'JUILLET',
              'Aug' => 'AOÛT',
              'Sep' => 'SEPTEMBRE',
              'Oct' => 'OCTOBRE',
              'Nov' => 'NOVEMBRE',
              'Dec' => 'DÉCEMBRE',
            ];

            $formattedDate = $dayOfWeek[$date->format('D')] . ' ' . $date->format('j') . ' ' . $monthNames[$date->format('M')] . ' ' . $date->format('Y');

            return strtoupper($formattedDate);
        }else{
            return '';
        }
    }

    public function formatCustomDateWithoutYears($date)
    {
        if($date != null and $date instanceof \DateTimeInterface ) {

            $dayOfWeek = [
              'Mon' => 'LUN.',
              'Tue' => 'MAR.',
              'Wed' => 'MER.',
              'Thu' => 'JEU.',
              'Fri' => 'VEN.',
              'Sat' => 'SAM.',
              'Sun' => 'DIM.',
            ];

            $monthNames = [
              'Jan' => 'JANVIER',
              'Feb' => 'FÉVRIER',
              'Mar' => 'MARS',
              'Apr' => 'AVRIL',
              'May' => 'MAI',
              'Jun' => 'JUIN',
              'Jul' => 'JUILLET',
              'Aug' => 'AOÛT',
              'Sep' => 'SEPTEMBRE',
              'Oct' => 'OCTOBRE',
              'Nov' => 'NOVEMBRE',
              'Dec' => 'DÉCEMBRE',
            ];

            $formattedDate = $dayOfWeek[$date->format('D')] . ' ' . $date->format('j') . ' ' . $monthNames[$date->format('M')];

            return strtoupper($formattedDate);
        }else{
            return '';
        }
    }



}