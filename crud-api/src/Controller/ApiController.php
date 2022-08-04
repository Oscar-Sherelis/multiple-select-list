<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Crud;
use App\Dto\CreatePostDto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CrudFactory;

class ApiController extends AbstractController
{

        /**
     * @param PostRepository $posts
     * @param EntityManagerInterface $objectManager
     * @param SerializerInterface $serializer
     */
    public function __construct(private EntityManagerInterface $objectManager)
    {
    }

    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/post_api', name: 'post_api', methods: 'POST')]
    public function post_api(#[Body] CreatePostDto $data): JsonResponse
    {
        // $parameter = json_decode($req->getContent(), true);
        $crud = new Crud();

        $entity = CrudFactory::create($data->getTitle(), $data->getTitleLoc());
        $this->objectManager->persist($entity);
        $this->objectManager->flush();

        // $title = $crud->setTitle($parameter['title']);
        // $titleLoc = $crud->setTitleLoc($parameter['titleLoc']);

        // // $this->objectManager->persist($crud);
        // // $this->objectManager->flush();

        // $entity = $crud->create($title, $titleLoc);
        // $this->objectManager->persist($entity);
        // $this->objectManager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'data' => $entity
        ]);    }

}
