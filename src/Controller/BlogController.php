<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"})
     * @param int $page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list($page = 1, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @param BlogPost $id
     * @param BlogPostRepository $blogPostRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @ParamConverter("id", class="App:BlogPost", options={"mapping": {"id": "id"}})
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function post(BlogPost $id, BlogPostRepository $blogPostRepository)
    {
      return $this->json([
          $blogPostRepository->find($id)
      ]);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * The below annotation is not required when $post is typehinted with BlogPost
     * and route parameter name matches any field on the BlogPost entity
     * @ParamConverter("slug", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
     * @param BlogPost $slug
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postBySlug(BlogPost $slug)
    {
        // Same as doing findOneBy(['slug' => contents of {slug}])
        return $this->json($slug);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @Route("/add", name="blog_add", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addPost(Request $request, SerializerInterface $serializer)
    {
        $post = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        return $this->json($post);
    }

    /**
     * @param BlogPost $post
     * @Route("/post/{id}", name="blog_delete", methods={"DELETE"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deletePost(BlogPost $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
       return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
