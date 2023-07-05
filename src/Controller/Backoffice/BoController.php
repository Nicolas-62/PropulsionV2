<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Service\Secure;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BoController extends AbstractCrudController
{
    public function __construct(
    )
    {
    }

    /**
     * Enregistre sur le serveur le fichier déposé par l'utilisateur
     *
     * @param AdminContext $context
     * @return JsonResponse
     */
    public function upload(Secure $secureService, AdminContext $context): JsonResponse
    {
        // Objet réponse.
        $response = array(
          'error' => null,
          'folderId' => null,
          'filename' => null,
//          'file' => ''
        );

        // Récupération de l'image
        $file = $context->getRequest()->files->get('file');

        // DEBUG
        //dump($file->isValid());

        // Si l'image a été récupérée.
        if(isset($file) && $file->isValid()) {
            // Dossier temporaire de l'image = chaine alphanumérique de 10 caractères.
            $folderId = $secureService->random_hash(5);
            // Chemin temporaire de l'image
            $imageBasePath = Constants::ASSETS_UPLOAD_PATH . $folderId . '/';
            // Déplacement de l'image dans le dossier temporaire
            if($file->move($imageBasePath, $file->getClientOriginalName())){
                $response["folderId"] = $folderId;
                $response["filename"] = $file->getClientOriginalName();
                // Transformation de l'image en base64
//                $response["file"]     = base64_encode(file_get_contents($imageBasePath . $file->getClientOriginalName()));
                $response["url"]      =  $context->getRequest()->getBaseUrl() . $imageBasePath . $file->getClientOriginalName();
            }
        }else{
            $response["error"] = "Impossible de récupérer le fichier";
        }
        // Retour
        return new JsonResponse($response);
    }


}