<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use App\Repository\QuestionRepository;
use App\Entity\Question;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private $logger;
    private $isDebug;
    private $entityManager;

    public function __construct(LoggerInterface $logger, bool $isDebug, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(QuestionRepository $questionRepository)
    {
        $allFoundQuestions = $questionRepository->findBy([], ['askedAt' => 'DESC']);

        return $this->render('question/homepage.html.twig', [
            'questionList' => $allFoundQuestions
        ]);
    }


    /**
     * @Route("/questions/new")
     */
    public function new() {
        return $this->render('question/new.html.twig');
    }


    /**
     * @Route("/questions/postQuestion", name="app_question_postQuestion", methods="POST")
     */
    public function postNewQuestion(Request $request) {

        $newQuestion = new Question();

        $newQuestion->setName($request->request->get('question-title'))
            ->setText($request->request->get('question-text'))
            ->setAskedBy($request->request->get('question-asked-by'))
            ->setSlug($request->request->get('question-slug'));

        $this->entityManager->persist($newQuestion);
        $this->entityManager->flush();


        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug, MarkdownHelper $markdownHelper)
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode!');
        }

        $repository = $this->entityManager->getRepository(Question::class);
        /** @var Question|null $foundQuestion */
        $foundQuestion = $repository->findOneBy(['slug' => $slug]);

        if (!$foundQuestion) {
            throw $this->createNotFoundException(sprintf('No question found for "%s"', $slug));
        }

        $answers = [
            'Make sure your cat is sitting `purrrfectly` still 🤣',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        return $this->render('question/show.html.twig', [
            'question' => $foundQuestion,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function postQuestionVote(Question $question, Request $request) {
        $direction = $request->request->get('direction');

        if ($direction === 'up') {
            $question->setVotes($question->getVotes() + 1);
        } elseif ($direction === 'down') {
            $question->setVotes($question->getVotes() - 1);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_question_show', [
            'slug' => $question->getSlug()
        ]);
    }
}
