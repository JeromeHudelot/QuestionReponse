<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use App\Entity\Question;
use App\Form\Type\SubscribeType;

class MainController extends AbstractController
{
    public function home($page = 1, PaginatorInterface $paginator): Response
    {
		
		$repository = $this->getDoctrine()->getRepository(Question::class);
		$donnees = $repository->findBy([],['posted_at' => 'desc']);
		
		$questions = $paginator->paginate(
            $donnees,
            $page,
            10
        );
		
        return $this->render('Main/home.html.twig', [
            'questions' => $questions,
        ]);
    }
	
    public function subscribe(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
		$user = new User();

        $form = $this->createForm(SubscribeType::class, $user, [
            'action' => $this->generateUrl('subscribe'),
            'method' => 'POST',
        ]);
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('Main/subscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
    public function edit(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
		$user = $this->getUser();
		
        $form = $this->createForm(SubscribeType::class, $user, [
            'action' => $this->generateUrl('subscribe'),
            'method' => 'POST',
        ]);
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('Main/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}