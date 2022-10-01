<?php

namespace App\Admin\Controller;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('product')
            ->add('id');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')->setDisabled(true),
            AssociationField::new('product'),
            AssociationField::new('store'),
            Field::new('expirationDate'),
            Field::new('isLiked'),
        ];
    }
}
