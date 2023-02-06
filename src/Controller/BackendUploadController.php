<?php

namespace App\Controller;

use App\Form\FileUploadType;
use App\Service\FileUploader;
use App\Service\ServerListFileProcessor;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BackendUploadController extends AbstractController
{
    /**
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param ServerListFileProcessor $fileProcessor
     * @return Response
     */
    public function servers(
        Request $request,
        FileUploader $fileUploader,
        ServerListFileProcessor $fileProcessor
    ): Response
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $serversList = $form->get('servers_list')->getData();

            if ($serversList) {

                try {
                    $file = $fileUploader->upload($serversList);
                    $fileProcessor->process($file);
                }catch (Exception $e){
                    // TODO log $e->getMessage()
                    $this->addFlash('error', 'Something went wrong! ');

                    return $this->render('backend/index.html.twig', [
                        'form' => $form,
                    ]);
                }

                $this->addFlash('success', 'File successfully uploaded.');
            }
        }

        return $this->render('backend/index.html.twig', [
            'form' => $form,
            'location' => ''
        ]);
    }
}
