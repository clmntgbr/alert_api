<?php

namespace App\Admin\Controller;

use App\Entity\Item;
use App\Entity\Product;
use App\Entity\ProductNutrition;
use App\Entity\Store;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    /** @param User $user */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)->setName($user->getEmail());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Api Docs', 'fas fa-map-marker-alt', '/api/docs');
        yield MenuItem::linkToCrud('Item', 'fas fa-list', Item::class);
        yield MenuItem::linkToCrud('Product', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('ProductNutrition', 'fas fa-list', ProductNutrition::class);
        yield MenuItem::linkToCrud('Store', 'fas fa-list', Store::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
    }
}
