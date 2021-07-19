<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/signup', name: 'signup')]
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $search_email = $this->em->getRepository(User::class)->findOneBy(array('email' => $user->getEmail()));

            if (!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                $this->em->persist($user);
                $this->em->flush();


                // Send confirmation e-mail:
                $mail = new Mail();
                $content = "Bonjour " . $user->getFirstname() . "<br/>Welcome to the first e-shop 100% Made in France.<br/>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sollicitudin condimentum sapien, non iaculis metus accumsan et. Aliquam nec justo in metus venenatis porta. Fusce mollis lorem id nisi sodales accumsan. Suspendisse massa dui, dapibus a est a, malesuada pulvinar magna. Pellentesque in velit augue. Integer aliquet venenatis faucibus. Nunc vitae feugiat enim, quis fermentum ipsum.";
                $mail->send($user->getEmail(), $user->getFirstname(), 'Welcome to The French Boutique', $content);

                $notification = "Your account has been created successfully.";
            } else {
                $notification = "The e-mail provided already has an existing account.";
            }
        }


        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
