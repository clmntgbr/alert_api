<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\UserNotificationTimerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-notifications',
    description: 'Add a short description for your command',
)]
class CreateNotificationsCommand extends Command
{
    public function __construct(
        private readonly UserNotificationTimerRepository $userNotificationTimerRepository,
        private readonly ItemRepository $itemRepository,
        private readonly EntityManagerInterface $em,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $timers = $this->userNotificationTimerRepository->findAll();

        foreach ($timers as $timer) {
            $items = $this->itemRepository->findItemsByStoreAndExpireDate($timer->getUser(), $timer->getValueBeforeNotificationInHours());
            $notification = $this->createNotification($timer->getUser());
            foreach ($items as $item) {
                $notification->addItem($item);
            }
            $this->em->persist($notification);
            $this->em->flush();
        }

        return Command::SUCCESS;
    }

    private function createNotification(User $user): Notification
    {
        $notification = new Notification();
        $notification
            ->setStatus(Notification::PENDING)
            ->setInitStatuses([Notification::PENDING])
            ->setUser($user)
            ->setType('item')
        ;

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }
}
