<?php

namespace App\Controller\Admin;


use App\Service\AwsBucketS3Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{

    /**
     * @param Request $request
     * @param AwsBucketS3Service $awsBucketS3Service
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/admin', name: 'app_admin')]
    public function index(
        Request $request,
        AwsBucketS3Service $awsBucketS3Service,
        SessionInterface $session
    ): Response {
        // Retrieve the current page number
        $page = $request->query->getInt('page', 1);
        $limit = 4;

        // Retrieve the token for the current page from the session
        $tokens = $session->get('s3_pagination_tokens', []);

        if ($page > 1 && isset($tokens[$page - 1])) {
            $continuationToken = $tokens[$page - 1];
        } else {
            $continuationToken = $request->query->get('token', null);
        }

        // Retrieve images from the S3 bucket with pagination
        $listImagesBucket = $awsBucketS3Service->listImages($limit, $continuationToken);

        if ($listImagesBucket['status'] === 'failure') {
            $this->addFlash('error', $listImagesBucket['message']);
            //Redirect to an error page to avoid an infinite loop
            return $this->redirectToRoute('app_error');
        }

        $isTruncated = $listImagesBucket['isTruncated'];
        $nextToken = $listImagesBucket['nextContinuationToken'] ?? null;

        // Stores the current token so that from the next page we can return
        if ($isTruncated && $nextToken) {
            $tokens[$page] = $nextToken;
            $session->set('s3_pagination_tokens', $tokens);
        }

        // Rendering the template
        return $this->render('admin/index.html.twig', [
            'images' => $listImagesBucket['images'],
            'currentPage' => $page,
            'isTruncated' => $isTruncated,
            'nextToken' => $nextToken,
            'limit' => $limit,
            'tokens' => $tokens,
        ]);
    }

    #[Route('/admin/delete/{key}', name: 'app_admin_delete_image')]
    public function deleteImage(
        string $key,
        AwsBucketS3Service $awsBucketS3Service,
        SessionInterface $session
    ): Response {
        // Calling the service to delete the image
        $deleteResult = $awsBucketS3Service->deleteImage($key);

        // Handle deletion errors or success
        if ($deleteResult['status'] === 'failure') {
            $this->addFlash('error', $deleteResult['message']);
            return $this->redirectToRoute('app_error');
        }
        $this->addFlash('success', 'Image supprimée avec succès.');
        return $this->redirectToRoute('app_admin');
    }


    /**
     * @return Response
     */
    #[Route('/error', name: 'app_error')]
    public function error(): Response
    {
        return $this->render('admin/error.html.twig', []);
    }

}
