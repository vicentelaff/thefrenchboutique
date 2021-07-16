<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/account/addresses', name: 'account_address')]
    public function index(): Response
    {

        return $this->render('account/address.html.twig');
    }

    #[Route('/account/add-address', name: 'add_account_address')]
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address;

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush();
            if($cart->get()){
                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('account_address');
            }
        }

        return $this->render('account/form_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/account/edit-address/{id}', name: 'edit_account_address')]
    public function edit(Request $request, $id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneBy(['id' => $id]);

        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/form_address.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/account/delete-address/{id}', name: 'delete_account_address')]
    public function delete($id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneBy(['id' => $id]);

        if ($address && $address->getUser() == $this->getUser()) {
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirectToRoute('account_address');
    }
}
