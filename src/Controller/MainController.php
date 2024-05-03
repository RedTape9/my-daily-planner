<?php

namespace App\Controller;

use App\Form\PostType;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('main/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/add', name: 'add-task')]
    public function addTask(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Task Added Successfully');

            return $this->redirectToRoute('app_main');
        }


        return $this->render('main/add-task.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('edit-task/{id}', name: 'edit-task')]
    public function editTask(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('success', 'Task Updated Successfully');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('main/add-task.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('delete-task/{id}', name: 'delete-task')]
    public function deleteTask($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $this->em->remove($post);
        $this->em->flush();
        $this->addFlash('success', 'Task Deleted Successfully');
        return $this->redirectToRoute('app_main');
    }
}