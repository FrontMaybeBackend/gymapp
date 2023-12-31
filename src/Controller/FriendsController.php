<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Entity\Invitations;
use App\Entity\User;
use App\Repository\FriendsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class FriendsController extends AbstractController
{
    #[Route('/friends', name: 'app_friends')]
    public function index(FriendsRepository $friendsRepository): Response
    {

        return $this->render('friends/index.html.twig', [
            'friends' => $friendsRepository->findAll(),

        ]);


    }

//Wysyła zaproszenie do znajomych
    #[Route('/friends/add/{id}/{username}', name: 'app_add_friends', methods: ['POST'])]
    public function add(Request $request, FriendsRepository $friendsRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, $id, $username, UserInterface $userMain, User $sender): Response
    {

        if ($request->isMethod('POST')) {
            $receiver = $userRepository->findOneBy(['id' => $id, 'username' => $username]);
            //ustawiam wysyłającego jako zalogowane usera

            //sprawdza czy user istnieje
            if (!$receiver) {
                return new Response('User not found', Response::HTTP_NOT_FOUND);
            }


            //wysyłanie zapytania o dodanie do znajomych.
            $invitations = new Invitations();
            $invitations->addSender($sender);
            $invitations->setSendero($userMain->getUserIdentifier());
            $invitations->setReceiver($receiver);
            $invitations->setStatus('oczekujące');

            $entityManager->persist($invitations);
            $entityManager->flush();

            flash()->addSuccess('Friend ivitation sent');
            return $this->redirectToRoute('app_friends');
        }

        return $this->render('friends/index.html.twig', [
            'friends' => $friendsRepository->findFriends($id),
        ]);
    }

    //Ustawia znajomych w relacji
    #[Route('/friends/set/{id}', name: 'app_friends_set')]
    public function set(int $id, EntityManagerInterface $entityManager, UserInterface $user): Response
    {

        $status = $entityManager->getRepository(Invitations::class)->find($id);
        $loggedUser = $this->getUser();

        if ($status->getStatus() === 'przyjęte') {
            $newFriend = new Friends();

            $loggedUser->addFriend($newFriend);
            $newFriend->setUsername($status->getSendero());
            $newFriend->addUser($loggedUser);

            $entityManager->persist($newFriend);
            $entityManager->persist($loggedUser);
            $entityManager->flush();


        }
        return $this->redirectToRoute('app_friends');
    }


    #[Route('/friends/show', name: 'app_friends_show', methods: 'GET')]
    public function show(UserInterface $user, FriendsRepository $friendsRepository, PaginatorInterface $paginator, Request $request)
    {
        $loggedInUserId = $user->getId();


        $friendsData = [];


        $results = $friendsRepository->findFriends($loggedInUserId);

        foreach ($results as $result) {
            $friendsData[] = [
                'friends' => $result['friend_username']
            ];
        }

        $pagination = $paginator->paginate(
            $friendsData,
            $request->query->getInt('page', 1),
            6
        );


        return $this->render('friends/show.html.twig', [
            'friends' => $pagination,
        ]);
    }


}
