<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route('/tag', name: 'tag')]
    public function index(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/tag/new', name: 'tag_new')]
    #[Route('/tag/edit/{id}', name: 'tag_edit')]
    public function edit(Tag $tag = null): Response
    {
        if (!$tag) {
            $tag = new Tag();
        }

        $form = $this->createForm(TagType::class, $tag);

        return $this->render('tag/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
