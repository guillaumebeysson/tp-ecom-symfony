<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Form\ChangePasswordType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
        ]);
    }

     /**
     * @Route("/account/pwd", name="account_pwd")
     */
    public function pwd(
        Request $request,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        $notification = null;
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();

            if($encoder->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $pwd = $encoder->encodePassword($user, $new_pwd);
                $user->setPassword($pwd);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $notification = "Changement de mot de passe effectué";
            } else{
                $notification = "Le mot de passe actuel n'est pas bon";
            }
        }

        return $this->render('account/pwd.html.twig', [
            'formulaire' => $form->createView(),
            'notification' => $notification
        ]);
    }

    /**
     * @Route("/account/address", name="account_address")
     */
    public function adddress(): Response
    {
        return $this->render("account/address.html.twig", [
        ]);
    }

    /**
     * @Route("/account/address/add", name="account_add_address")
     */
    public function adddressAdd(Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // dd($form->getData());
            $address->setUser($this->getUser());
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render("account/address-add.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/address/modify/{id}", name="account_modify_address")
     */
    public function addressModify(Request $request, AddressRepository $repo, EntityManagerInterface $em, $id)
    {

        $address = $repo->findOneById($id);

        if(!$address || $address->getUser() !== $this->getUser()){
            return $this->redirectToRoute("account_address");
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute("account_address");
        }

        return $this->render("account/address-add.html.twig", [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/account/address/delete/{id}", name="account_delete_address")
     */
    public function delete(Address $address, EntityManagerInterface $em)
    {
        if($address && $address->getUser() == $this->getUser())
        {
            
            $em = $this->getDoctrine()->getManager();
            $em->remove($address);
            $em->flush();
            return $this->redirectToRoute("account_address");
        }
        return $this->redirectToRoute('account_address');

    }
}
