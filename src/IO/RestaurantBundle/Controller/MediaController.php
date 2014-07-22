<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * Media Controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;

    /**
     * User Service
     * 
     * @Inject("io.media_service")
     * @var \IO\RestaurantBundle\Service\MediaService
     */
    public $mediaSv;
    
    /**
     * Admin restaurant index
     * 
     * @Route("/upload", name="media_upload")
     * @Secure(roles="ROLE_MANAGER")
     */
    public function uploadAction(Request $request)
    {
        $uploadFile = $request->files->get('media');
        if ($uploadFile === null) {
            return new JsonResponse(array('error' => 'invalid file'));
        }
        
        $media = $this->mediaSv->handleUploadFile($uploadFile);
        if ($media === null) {
            return new JsonResponse(array('error' => 'invalid file'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($media);
        $em->flush();
        
        return new JsonResponse(array('media' => array(
            'id' => $media->getId(),
            'path' => $this->mediaSv->getWebPath($media),
        )));
    }
}
