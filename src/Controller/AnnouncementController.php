<?php

namespace App\Controller;

use App\Entity\Announcement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        $query = $em->createQuery("SELECT a FROM App\Entity\Announcement a 
                    WHERE 
                        :today BETWEEN COALESCE(a.published_at, :today) AND COALESCE(a.closed_at, :today)
                        AND a.is_active = 1
                     ")
        ->setParameter('today', new \DateTime());

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Web/announcement/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'AnnouncementController',
        ]);
    }

    /**
     * @Route("/announcement/{id}", name="announcement")
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function show($id, EntityManagerInterface $em){
        $repository = $em->getRepository(Announcement::class);

        /**
         * @var $announcement Announcement
         */
        $announcement = $repository->find($id);
        $announcement->setShowCount($announcement->getShowCount()+1);
        $em->persist($announcement);
        $em->flush();

        return $this->render('Web/announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * @Route("/all_tops", name="all_tops")
     */
    public function allTops()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Announcement::class);
        $announcements = $repository->findBy(['']);

        return $this->render('Web/announcement/index.html.twig', [
            'announcements' => $announcements,
            'controller_name' => 'AnnouncementController',
        ]);
    }
}
