<?php

namespace App\Service;

use App\Constants\Constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;

class MediaService{

    public function __construct(
        private KernelInterface $appKernel,
        private Toolbox $toolbox

    )
    {
        // Chemin d'upload des images.
        $upload_dir = Constants::PUBLIC_PATH . Constants::DYN_IMG_PATH;
        // Si le dossier n'existe pas on le créer.
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->appKernel->getProjectDir().'/'.$upload_dir);
    }


    /**
     * Ecrase le fichier uploadé par l'utilisateur par l'image envoyé en base64 par le plugin cropper.js cas échéant,
     * déplace le fichier du dossier d'upload vers la médiathèque.
     *
     * @param $folderId : nom dossier temporaire d'upload
     * @param $filename : nom du fichier temporaire d'upload
     * @param $imgBase64 : image croppée en base64
     * @return string
     */
    public function getFile($folderId, $filename, $imgBase64 = null, $dest_path = null, $removeUploadFolder = false): string
    {
        $new_filename = false;
        // Si un fichier a été déposé
        if($folderId != null){
            // Dossier d'upload temporaire.
            $tmpPath = Constants::DYN_UPLOAD_PATH . $folderId . '/';
            // Chemin du fichier temporaire.
            $imageTmpPath = $tmpPath. $filename;
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
                // Identifiant unique du fichier
                $uniqueId = uniqid();

                // On ajoute un identifiant unique au nom de l'image.
                $new_basename = $this->toolbox->url_compliant($file->getBasename('.' . $file->getExtension())).'-'.$uniqueId;
                $new_filename = $new_basename.'.'.$file->guessExtension();
                // Si c'est un pdf
                if($file->getMimeType() == 'application/pdf') {
                    // On recherche la vignette du pdf.
                    $thumb_filename = pathinfo($file->getBasename('.' . $file->getExtension()), PATHINFO_FILENAME) . '.jpg';
                    // Si elle existe
                    if($filesystem->exists($tmpPath . $thumb_filename)){
                        // On ajoute un identifiant unique au nom de l'image.
                        $new_thumb_filename = $new_basename.'.jpg';
                        // On déplace la vignette du pdf
                        $filesystem->rename($tmpPath . $thumb_filename, Constants::DYN_IMG_PATH . $new_thumb_filename);
                    }
                }
                $mediatheque_path = Constants::DYN_IMG_PATH;
                // Si un chemin de destination a été précisé
                if(isset($dest_path)){
                    $mediatheque_path   .=   $dest_path;
                }
                if( ! $filesystem->exists($mediatheque_path)){
                    $filesystem->mkdir($mediatheque_path);
                }
                // Si on arrive à le déplacer dans la médiatheque.
                $filesystem->rename($imageTmpPath, $mediatheque_path . $new_filename);

                if($removeUploadFolder){
                    // On supprime le dossier temporaire.
                    $filesystem->remove($tmpPath);
                }
            }
        }
        return $new_filename;
    }

    /**
     * Déplace les fichiers uploadées d'un dossier vers un dossier de destination le cas échéant
     * @param $folderId
     * @param $dest_path
     * @return array
     */
    public function getFiles($folderId, $dest_path = null): array
    {
        // Variable, liste des fichiers récupérés dans l'upload et déplacés dans la photothèque
        $new_filenames = array();
        // Dossier d'upload temporaire.
        $tmpPath = Constants::DYN_UPLOAD_PATH . $folderId . '/';

        $finder = new Finder();
        // Pour chaque fichier
        foreach($finder->files()->in($tmpPath) as $file){
            // Récupère le fichier et le déplace dans la photothèque.
            $new_filenames[] = $this->getFile($folderId, $file->getFilename(), null, $dest_path);
        }
        // retour
        return $new_filenames;
    }



}