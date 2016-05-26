<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'text' => $faker->realText(),
    'is_new' => 0,
    'timestamp' => $faker->dateTimeBetween('-1year', 'now')->getTimestamp()
];
