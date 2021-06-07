<?php

namespace App\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart {

    // 1 création de ma session
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    
    public function add($id) {
        //$cart vaut ce que $this->get() contient, sinon un tableau vide

        $cart = $this->get([]);

        // Si id n'est pas vide alors tu ajoutes +1
        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            // sinon tu l'ajoutes
            $cart[$id] = 1;
        }

        // J'ai plus qu'à insérer ma variable $cart dans ma session cart
        $this->session->set('cart', $cart);
    }

    public function get() {
        // permet de récupérer ma session cart
        return $this->session->get('cart');
    }

    public function decrease($id){
        //$cart vaut ce que $this->get() contient, sinon un tableau vide
        $cart = $this->get([]);
        // Je vérifie si ma quantité est > 1
        if ($cart[$id] > 1){
            $cart[$id] --;
        }else{
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }

    public function removeOne($id){
        //$cart vaut ce que $this->get() contient, sinon un tableau vide
        $cart = $this->get([]);
        // unset permet de supprimer un élément passé en argument
        unset($cart[$id]);
        // je retourne ce qu'il reste de $cart
        return $this->session->set('cart', $cart);
    }

    public function remove() {
        //supprime tout le panier
        return $this->session->remove('cart');
    }

}