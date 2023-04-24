<?php

namespace App\Twig;

use App\Constants\Constants;
use App\Entity\Media;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getName', [$this, 'getName']),
            new TwigFunction('getThumbnailPath', [$this, 'getThumbnailPath']),
            new TwigFunction('const', [$this, 'const']),

        ];
    }

    public function getName($filename): string
    {
        return Media::getNameOf($filename);
    }

    public function getThumbnailPath($filename): string
    {
        return Constants::ASSETS_IMG_PATH.$filename;
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
}