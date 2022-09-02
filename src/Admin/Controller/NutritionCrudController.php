<?php

namespace App\Admin\Controller;

use App\Entity\Nutrition;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NutritionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Nutrition::class;
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
