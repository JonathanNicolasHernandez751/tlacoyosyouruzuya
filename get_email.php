<?php
// get_email.php
require_once 'corrreelectronico/vendor/autoload.php';
$faker = Faker\Factory::create('es_ES');

// Solo devolvemos el texto del email
echo $faker->email();
?>