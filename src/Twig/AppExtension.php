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

        ];
    }

    public function getName($filename)
    {
        return Media::getNameOf($filename);
    }

    public function getThumbnailPath($filename)
    {
        return Constants::ASSETS_IMG_PATH.$filename;
    }
}