<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use App\Service\ImgUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #Display the table of products in GET method, check and persist the new Product in POST method
    #[Route('/', name: 'products_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MEMBER', message: 'Vous devez confirmer votre email ou être connecté pour acceder à cette partie du site', statusCode: 403)]
    public function indexAndFormAddProduct(ProductsRepository $productsRepository, Request $request, EntityManagerInterface $entityManager, ImgUploader $uploader): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check user roles //
            if (!$this->isGranted('ROLE_MANAGER')){
                throw $this->createAccessDeniedException('Vous devez être manager pour créer un produit');
            }
            // Check if product already exist by name query : display error if already exist //
            if($productsRepository->findOneBy(['name'=>$product->getName()])){
                $this->addFlash('error', 'Produit déjà existant');
                return $this->redirectToRoute('products_index');
            }
            // Give new name if an image has been uploaded //
            $file = $form['imagePath']->getData();
            if($file instanceof UploadedFile){
                $filename = $uploader->getFileName($file);
                $product->setImagePath("image/$filename");
            }
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Article ajouté avec succés');
            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    #Show the product details
    #[Route('/{id}', name: 'products_show', methods: ['GET'])]
    #[IsGranted('ROLE_MEMBER', message: 'Vous devez confirmer votre email pour acceder à cette partie du site', statusCode: 403)]
    public function show(Products $product, ProductsRepository $repository): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
            'products' => $repository->findAll()
        ]);
    }

    #Display the form for edit a product in GET method, check and persist the Product edited in POST method
    #[Route('/{id}/edit', name: 'products_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MANAGER', message: 'Vous devez être manager pour accéder à cette partie du site', statusCode: 403)]
    public function edit(string $id, Request $request, Products $product, EntityManagerInterface $entityManager, ImgUploader $uploader): Response
    {
        $productRepository = $entityManager->getRepository(Products::class);
        $nameBeforePost = $productRepository->find($id)->getName();

        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if product name change in field post and if is already in db //
            if($nameBeforePost !== $product->getName() && $productRepository->findOneBy(['name'=>$product->getName()])){
                $this->addFlash('error', 'Nom de produit déjà existant');
                return $this->redirectToRoute('products_edit', ['id'=>$request->get('id')]);
            }

            // Give new name to the image if user uploaded one //
            $file = $form['imagePath']->getData();
            if($file instanceof UploadedFile){
                $filename = $uploader->getFileName($file);
                $fullPath = "image/$filename";
                $product->setImagePath($fullPath);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Article édité avec succés');
            return $this->redirectToRoute('products_show', ['id'=> $request->get('id')], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'products_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER', message: 'Vous devez être manager ou administrateur pour supprimer un article', statusCode: 403)]
    public function delete(Request $request, Products $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
    }
}
