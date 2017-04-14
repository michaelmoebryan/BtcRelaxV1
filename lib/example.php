<?php
// Example of usage of Semaphore class
include "semaphore.php";

// Processing of double submit click
// Lock the register process till the registration end

$s = new Semaphore($companyFullName);
if ($s->lock()) {
  // Write company to database here
  $s->unlock();
  echo "Company succesfully registered";
}
else {
  echo "ERROR: You pressed submit button twice";
  exit;
}
?>