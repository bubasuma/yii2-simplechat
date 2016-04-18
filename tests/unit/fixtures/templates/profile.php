<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$avatarPath =  __DIR__ . '/../../../../assets/img/avatars';
return [
    'first_name' => $faker->firstNameMale,
    'last_name' => $faker->lastName,
    'gender' => 'M',
    'avatar' => $faker->randomElement(\yii\helpers\FileHelper::findFiles($avatarPath)),
];
