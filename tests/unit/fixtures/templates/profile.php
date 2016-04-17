<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$gender = $faker->randomElement(['F', 'M']);
$firstName = !strcmp('M', $gender) ? $faker->firstNameMale : $faker->firstNameMale;

return [
    'first_name' => $firstName,
    'last_name' => $faker->lastName,
    'gender' => $gender,
    'avatar' => $faker->imageUrl(64,64,'people'),
];
