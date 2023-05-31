<?php

namespace App\Admin\Controller;

use App\Entity\ProductNutrition;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductNutritionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductNutrition::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
