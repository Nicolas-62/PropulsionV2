<?php

namespace App\Service;

use App\Constants\Constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;

class MediaService{

    public function __construct(
        private KernelInterface $appKernel,
        private Toolbox $toolbox

    )
    {
        // Chemin d'upload des images.
        $upload_dir = Constants::UPLOAD_PATH . Constants::ASSETS_IMG_PATH;
        // Si le dossier n'existe pas on le créer.
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->appKernel->getProjectDir().'/'.$upload_dir);
    }


    /**
     * @param $folderId
     * @param $filename
     * @param $imgBase64
     * @return string
     */
    public function getFile($folderId, $filename, $imgBase64 = null): string
    {
        $new_filename = false;
        // Si un fichier a été déposé
        if($folderId != null){
            // Dossier d'upload temporaire.
            $tmpPath = Constants::ASSETS_UPLOAD_PATH . $folderId . '/';
            // Chemin du fichier temporaire.
            $imageTmpPath = $tmpPath. '/'.$filename;
            // Si le fichier source existe.
            $filesystem = new Filesystem();
            if($filesystem->exists($imageTmpPath)){
                // Si on a une image en base64
                if(isset($imgBase64) && trim($imgBase64) != '' && str_contains($imgBase64, 'data:image/png;base64,')){
                    // Conversion de l'image en fichier
                    $img = str_replace('data:image/png;base64,', '', $imgBase64);
                    // On supprime l'image précédemment uploadée.
                    $filesystem->remove($imageTmpPath);
                    // On crée un nouveau fichier vide
                    $filesystem->touch($imageTmpPath);
                    // On écrit dans le fichier le contenu de l'image
                    $filesystem->appendToFile($imageTmpPath, base64_decode($img));
                }
                // Infos de l'image
                $file = new File($imageTmpPath);
                // On ajoute un identifiant unique au nom de l'image.
                $new_filename = $this->toolbox->url_compliant($file->getBasename('.' . $file->getExtension())).'-'.time().'.'.$file->guessExtension();
                // Chemin de destination
                $imagePath = Constants::ASSETS_IMG_PATH .$new_filename;
                // Si on arrive à le déplacer dans la médiatheque.
                $filesystem->rename($imageTmpPath, $imagePath);
                // On supprime le dossier temporaire.
                $filesystem->remove($tmpPath);
            }
        }
        return $new_filename;
    }



}