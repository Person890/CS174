<?php

/* This code implements following functions: 
Given 2 numerical parameter in input, outputs:
1. If the two numbers are co-primes (relatively prime to each other), and
2. The divisors of the numbers to prove such conclusion
*/

$a = 10; $b = 20;
echo "The user input numbers $a and $b <br>";
are_co_primes($a, $b);
printDivisors($a);
printDivisors($b);

$c = 2;
$d = 3;
are_co_primes($c, $d);
printDivisors($c);
printDivisors($d);
tester($a, $b, $c, $d);



// this function determines if the two numbers are coprime by recursively calling greatest common divisor function
function are_co_primes($a, $b)
  {
      if (__gcd($a, $b) == 1)
          echo "The numbers are coprime <br>Proof:<br>";
      else
          echo "The numbers are not coprime <br>Proof:<br>";
  }

// this function prints out divisors of the numbers to prove that they are/aren't coprime
function printDivisors($num1) {
    echo "divisors of $num1 are: ";
    for($i = 1; $i <= $num1; $i++) {
      if($num1%$i == 0)
        echo "$i ";
    }
    echo "<br>";
  }

//PHP's greatest common divisor function
function __gcd($a, $b){
        
        // if either of the numbers are negative
        if ($a < 0 || $b < 0){
            echo "Please enter positive numbers";
        }
        // if either of the numbers are zero
        if ($a == 0 || $b == 0)
            return 0;
 
        // if the numbers are equal
        if ($a == $b)
            return $a;
 
        // if a is greater than b
        if ($a > $b)
            return __gcd($a - $b, $b);
 
        // else
        return __gcd($a, $b - $a);
    }


// tester function which contains hardcoded result of the are_co_primes function
function tester($a, $b, $c, $d){
    echo "is output from are_co_primes($a, $b) equal to 'are not coprimes'? If yes, test passed <br>";
    echo "is output from are_co_primes($c, $d) equal to 'are coprimes'? If yes, test passed <br>";
}
?> 
