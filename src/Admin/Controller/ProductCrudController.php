<?php

namespace App\Admin\Controller;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Product Details'),
            IdField::new('id')->setDisabled(true),
            TextField::new('ean'),
            TextField::new('name'),
            TextField::new('brand'),

            FormField::addPanel('Nutritions Details'),
            AssociationField::new('nutrition')->hideOnIndex(),
        ];
    }
}