<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\AnnouncementClick;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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

        $announcementClick = new AnnouncementClick();
        $announcementClick->setAnnouncement($announcement);
        $em->persist($announcementClick);
        $em->flush();

        return $this->render('Web/announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * @Route("/tops", name="all_tops")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function tops(EntityManagerInterface $em, Request $request)
    {
        /**
         * Fix values to default if
         */
        try {
            $topDateStart = new \DateTime($request->query->get('top_date_start'));
        } catch (\Exception $exception) {
            $topDateStart = new \DateTime("-1 month");
        }
        try {
            $topDateEnd = new \DateTime($request->query->get('top_date_end'));
        } catch (\Exception $exception) {
            $topDateEnd = new \DateTime("+1 day");
        }

        $query = $em->createQuery("
            SELECT a
                FROM App\Entity\AnnouncementClick ac
                JOIN App\Entity\Announcement a WITH a = ac.announcement
            WHERE 
                ac.clicked_at BETWEEN :top_date_start AND :top_date_end
            GROUP BY a.id
            ORDER BY COUNT(ac.id) DESC
        ")
            ->setParameter('top_date_start', $topDateStart)
            ->setParameter('top_date_end', $topDateEnd)
            ->setMaxResults(10)

        ;

        $announcements = $query->getResult();

        return $this->render('Web/announcement/top.html.twig', [
            'announcements' => $announcements,
            'top_date_start' => $topDateStart->format('Y-m-d'),
            'top_date_end' => $topDateEnd->format('Y-m-d'),
            'controller_name' => 'AnnouncementController',
        ]);
    }
}
