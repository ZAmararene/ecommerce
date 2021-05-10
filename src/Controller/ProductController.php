<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBySlug($slug);

        if (!$category) {
            // throw new NotFoundHttpException("La catégorie demandée n'existe pas.");
            throw $this->createNotFoundException("La catégorie demandée n'existe pas.");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository)
    {
        $product = $productRepository->findOneBySlug($slug);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas.");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        // $client = [
        //     'nom' => 'chamla',
        //     'prenom' => 'Lior',
        //     'voiture' => [
        //         'marque' => 'Peugeot',
        //         'couleur' => 'noire'
        //     ]
        // ];

        // $collection = new Collection([
        //     'nom' => new NotBlank(['message' => 'le nom ne doit pas être vide']),
        //     'prenom' => [
        //         new NotBlank(['message' => 'le prénom ne doit pas être vide']),
        //         new Length(['min' => 3, 'minMessage' => 'Le prénom doit avoir au moins 3 caractères'])
        //     ],
        //     'voiture' => new Collection([
        //         'marque' => new NotBlank(['message' => 'la marque ne doit pas être vide']),
        //         'couleur' => new NotBlank(['message' => 'le couleur ne doit pas être vide']),
        //     ])
        // ]);

        // $result = $validator->validate($client, $collection);

        // $result = $validator->validate($product);

        // if ($result->count() > 0) {
        //     dd('Il y a des erreurs de validation', $result);
        // }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $product = new product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
