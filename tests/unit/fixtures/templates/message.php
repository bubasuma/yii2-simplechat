<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'text' => $faker->realText(),
    'timestamp' => $faker->dateTimeBetween('-1year','now')->getTimestamp()
];
