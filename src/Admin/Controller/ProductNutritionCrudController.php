<?php

namespace App\Admin\Controller;

use App\Entity\ProductNutrition;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

class ProductNutritionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductNutrition::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('ecoscoreGrade'),
            Field::new('ecoscoreScore'),
            Field::new('nutriscoreGrade'),
            Field::new('nutriscoreScore'),
            Field::new('ingredientsText')->hideOnIndex(),
            Field::new('createdBy')->hideWhenUpdating(),
            Field::new('updatedBy')->hideWhenUpdating(),
            Field::new('createdAt')->hideWhenUpdating(),
            Field::new('updatedAt')->hideWhenUpdating(),
        ];
    }
}
