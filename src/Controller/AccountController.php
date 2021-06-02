<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
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
}
