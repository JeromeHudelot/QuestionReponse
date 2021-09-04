<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\Tag;
use App\Entity\Comment;
use App\Entity\UserVoteAnswer;
use App\Form\Type\QuestionType;
use App\Form\Type\AnswerType;
use App\Form\Type\CommentType;

class QuestionController extends AbstractController
{
	
	public function postQuestion(Request $request): Response
	{
		$question = new Question();

        $form = $this->createForm(QuestionType::class, $question, [
            'action' => $this->generateUrl('post_question'),
            'method' => 'POST',
        ]);
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
			
            $question = $form->getData();
			$question->setPostedAt(new \DatetimeImmutable());
			$question->setUser($this->getUser());
			$question->setSlug(str_replace(' ', '-', $question->getTitle()));
			$question->setResolve(false);
			
			$repository = $this->getDoctrine()->getRepository(Tag::class);
            $entityManager = $this->getDoctrine()->getManager();
			
			foreach($question->getTags() as $tag){
				
				$tag->addQuestion($question);
				$question->addTag($tag);
				$entityManager->persist($tag);
				
			}
			
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('subject', array(
				'id' => $question->getId(),
				'slug' => $question->getSlug(),
			));
        }

        return $this->render('Question/post_question.html.twig', [
            'form' => $form->createView(),
        ]);
		
	}
	
	public function postAnswer(Request $request, string $slug): Response
	{
		$answer = new Answer();

        $form = $this->createForm(AnswerType::class, $answer, [
            'action' => $this->generateUrl('post_answer', array(
				'slug' => $slug,
			)),
            'method' => 'POST',
        ]);
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $answer = $form->getData();
			$repository = $this->getDoctrine()->getRepository(Question::class);
			$question = $repository->findOneBySlug($slug);
			$answer->setUser($this->getUser());
			$question->addAnswer($answer);
			$answer->setVote(0);
			$answer->setPostedAt(new \DatetimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($answer);
            $entityManager->flush();

            return $this->redirectToRoute('subject', array(
				'id' => $question->getId(),
				'slug' => $question->getSlug(),
			));
			
        }

        return $this->render('Question/post_answer.html.twig', [
            'form' => $form->createView(),
        ]);
	}
	
	public function postCommentQuestion(Request $request, string $slug): Response
	{
		$comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('post_comment_question', array(
				'slug' => $slug,
			)),
            'method' => 'POST',
        ]);
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
			$repository = $this->getDoctrine()->getRepository(Question::class);
			$question = $repository->findOneBySlug($slug);
			$comment->setUser($this->getUser());
			$question->addComment($comment);
			$comment->setPostedAt(new \DatetimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('subject', array(
				'id' => $question->getId(),
				'slug' => $question->getSlug(),
			));
        }

        return $this->render('Question/post_answer.html.twig', [
            'form' => $form->createView(),
        ]);
		
	}
	
	public function postCommentAnswer(Request $request, int $id): Response
	{
		$comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('post_comment_answer', array(
				'id' => $id,
			)),
            'method' => 'POST',
        ]);
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
			$repository = $this->getDoctrine()->getRepository(Answer::class);
			$answer = $repository->find($id);
			$comment->setUser($this->getUser());
			$answer->addComment($comment);
			$comment->setPostedAt(new \DatetimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('subject', array(
				'id' => $question->getId(),
				'slug' => $answer->getQuestion()->getSlug(),
			));
        }

        return $this->render('Question/post_answer.html.twig', [
            'form' => $form->createView(),
        ]);
		
	}
	
	public function subject(int $id, string $slug): Response
	{
		$repository = $this->getDoctrine()->getRepository(Question::class);
		$question = $repository->findOneBySlugAndId($id, $slug);

        return $this->render('Question/subject.html.twig', [
            'question' => $question,
        ]);
		
	}
	
	public function resolve(string $slug): Response
	{
		$repository = $this->getDoctrine()->getRepository(Question::class);
		$question = $repository->findOneBySlug($slug);
		$question->setResolve(true);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('subject', array(
			'id' => $question->getId(),
			'slug' => $question->getSlug(),
		));
		
	}
	
	public function vote(int $id, int $value): Response
	{
		
		$repository = $this->getDoctrine()->getRepository(UserVoteAnswer::class);
		$userVoteAnswer = $repository->findOneBy(array('user' => $this->getUser(), 'answer' => $id));
		$repository = $this->getDoctrine()->getRepository(Answer::class);
		$answer = $repository->find($id);
		if($userVoteAnswer == null)
		{
			$answer->setVote($answer->getVote() + $value);
			$userVoteAnswer = new UserVoteAnswer();
			$userVoteAnswer->setUser($this->getUser());
			$userVoteAnswer->setAnswer($answer);
			$userVoteAnswer->setVote(true);
			$manager = $this->getDoctrine()->getManager();
			$manager->persist($userVoteAnswer);
			$manager->flush();
		}

		return $this->redirectToRoute('subject', array(
			'id' => $question->getId(),
			'slug' => $answer->getQuestion()->getSlug(),
		));
		
	}
	
}