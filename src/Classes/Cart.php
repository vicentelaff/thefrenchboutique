<?php

namespace App\Classes;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

  private $session;
  private $em;

  public function __construct(EntityManagerInterface $em, SessionInterface $session)
  {
    $this->session = $session;
    $this->em = $em;
  }

  public function add($id)
  {
    $cart = $this->session->get('cart', []);

    if (!empty($cart[$id])) {
      $cart[$id]++;
    } else {
      $cart[$id] = 1;
    }


    $this->session->set('cart', $cart);
  }


  public function get()
  {
    return $this->session->get('cart');
  }

  public function remove()
  {
    return $this->session->remove('cart');
  }

  public function delete($id)
  {
    $cart = $this->session->get('cart', []);

    unset($cart[$id]);

    return $this->session->set('cart', $cart);
  }

  public function decrease($id)
  {
    $cart = $this->session->get('cart', []);

    if ($cart[$id] > 1) {
      $cart[$id]--;
    } else {
      unset($cart[$id]);
    }

    return $this->session->set('cart', $cart);
  }

  public function getFull()
  {
    $cartComplete = [];

    if ($this->get()) {
        foreach ($this->get() as $id => $quantity) {
          $product_object = $this->em->getRepository(Products::class)->findOneBy(['id' => $id]);
          
          // Preventing users from adding fake product IDs to their carts via the URL.
          if(!$product_object) {
            $this->delete($id);
            continue;
          }
          
          $cartComplete[] = [
              'product' => $product_object,
              'quantity' => $quantity
          ];
        }
    }

    return $cartComplete;
  }
}
