<?php

namespace App\DataFixtures;

use App\Entity\Announcement;
use App\Entity\AnnouncementClick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * Values for generating random announcements
     */
    const ANNOUNCEMENT_GENERATED = 25;
    const MAX_PARAGRAPH_IN_ANNOUNCEMENT = 8;
    const MIN_WORDS_IN_PARAGRAPH = 30;
    const MAX_WORDS_IN_PARAGRAPH = 120;

    const CLOSED_ANNOUNCEMENTS_ONE_OF = 11;
    const COMMA_AFTER_WORD_ON_OF = 9;
    const DOT_AFTER_WORD_ON_OF = 15;

    /**
     * Values for generating random clicks
     */
    const CLICKS_TO_GENERATE = 300;
    const CLICKS_PERIOD = "-1 month";

    /**
     * @var array
     */
    private $vocabulary = [];

    /**
     * @var string text for generating announcements
     */
    private $exampleText = 'Lorem ipsum dolor sit amet consectetur adipiscing elit Nulla luctus libero id dui
 vehicula porttitor Aenean vel varius massa Vivamus condimentum ex purus id
 scelerisque turpis cursus vel Suspendisse sagittis tellus quis pretium feugiat
 diam ligula varius turpis at luctus erat tellus eu odio Cras mattis pretium nunc
 a elementum neque porttitor nec Fusce sem turpis condimentum ut odio vel venenatis  
 pellentesque lacus Pellentesque rhoncus nibh lectus eget elementum nulla finibus 
 sit amet Ut porta varius odio ut pulvinar lorem Nunc pulvinar tempor dui finibus lacinia 
 Donec lobortis diam ipsum et convallis libero rhoncus nec Aliquam dapibus leo vel
 condimentum maximus elit ex cursus libero dignissim suscipit quam neque mollis est 
 Praesent dictum erat libero vitae commodo augue ornare ac';


    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->vocabulary = explode(' ', $this->exampleText);
    }

    public function load(ObjectManager $manager)
    {
        // Generate admin
        $adminUser = new User();
        $adminUser
            ->setEmail('admin@admin.lt')
            ->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, 'admin'));

        $manager->persist($adminUser);

        // Generate simple user
        $simpleUser = new User();
        $simpleUser
            ->setEmail('user@user.lt')
            ->setRoles(['ROLE_USER']);
        $simpleUser->setPassword($this->passwordEncoder->encodePassword($simpleUser, 'user'));

        $manager->persist($simpleUser);
        $announcements = [];

        // Generate announcements
        for ($i = 0; $i < self::ANNOUNCEMENT_GENERATED; $i++) {
            $announcement = new Announcement();
            $announcement
                ->setName("Announcment ". ($i + 1))
                ->setDescription($this->generateText())
                ->setPublishedAt(new \DateTime())
           ;
            if (rand(0, self::CLOSED_ANNOUNCEMENTS_ONE_OF) == 0) {
                $announcement->setClosedAt(new \DateTime());
            }

            if (rand(0, self::CLOSED_ANNOUNCEMENTS_ONE_OF) == 0) {
                $announcement->setIsActive(false);
            }

            $manager->persist($announcement);
            $announcements[] = $announcement;
        }

        // Generate clicks
        $maxTimeInterval = strtotime("now") - strtotime(self::CLICKS_PERIOD);
        for ($i = 0; $i < self::CLICKS_TO_GENERATE; $i++ ) {
            $announcement = $announcements[rand(0, self::ANNOUNCEMENT_GENERATED-1)];
            $announcementClick = new AnnouncementClick();
            $announcementClick->setAnnouncement($announcement);
            $date = new \DateTime();
            $date->setTimestamp(strtotime("now") - rand(0, $maxTimeInterval));
            $announcementClick->setClickedAt($date);

            $manager->persist($announcementClick);
        }

        $manager->flush();
    }

    private function generateText() {
        $str = '';
        $paragraphs = rand(1, self::MAX_PARAGRAPH_IN_ANNOUNCEMENT);
        for ($i = 0; $i < $paragraphs; $i++) {
            $str .= $this->generateParagraph()."\n";
        }

        return $str;
    }

    private function generateParagraph() {
        $wordsInVocabulary = count($this->vocabulary)-1;
        $paragraph = ucfirst($this->vocabulary[rand(0, $wordsInVocabulary)]);

        $noDot = false;
        $words = rand(self::MIN_WORDS_IN_PARAGRAPH, self::MAX_WORDS_IN_PARAGRAPH);
        for ($i = 0; $i < $words; $i++) {
            $newWord = trim(strtolower($this->vocabulary[rand(0, $wordsInVocabulary)]));
            if (empty($newWord)) { continue; }

            if (!$noDot && rand(0, self::COMMA_AFTER_WORD_ON_OF) == 0) {
                $paragraph .= ', '.$newWord;
                $noDot = true;
            } elseif(!$noDot && rand(0, self::DOT_AFTER_WORD_ON_OF) == 0) {
                $paragraph .= '. '.ucfirst($newWord);
                $noDot = true;
            } else {
                $paragraph .= ' ' . $newWord;
                $noDot = false;
            }
        }
        $paragraph .= '.';
        return $paragraph;
    }
}
