<?php

namespace App\Controller;

use App\Classes\Search;
use App\Entity\Products;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/products', name: 'products')]
    public function index(Request $request): Response
    {
        // On récupère les produits dans notre repository

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->em->getRepository(Products::class)->findWithSearch($search);
        } else {
            $products = $this->em->getRepository(Products::class)->findAll();
        }

        return $this->render('products/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{slug}', name: 'product')]
    public function show($slug): Response
    {

        // Recovering the product by slug:
        $product = $this->em->getRepository(Products::class)->findOneBy(['slug' => $slug]);

        $products = $this->em->getRepository(Products::class)->findBy(['isBest' => 1]);

        // Redir condition:
        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('products/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}
