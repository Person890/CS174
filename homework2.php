<?php

// initiating an instance of a class
$primesInstance = new Primes;

// this displays the Select File and Upload buttons and reloads the page after pressing Upload
echo <<<_END
<html><head><title>PHP Form Upload</title></head><body>
<form method='post' action='homework2.php' enctype='multipart/form-data'> Select File: <input type='file' name='filename' size='10'>
<input type='submit' value='Upload'>
</form>
</body></html>
_END;


/** This if statement checks/validates the extension of the file 
*/
if ($_FILES)
{
// sanitizing the name of the file and getting the file info
// $path_parts = pathinfo($_FILES['filename']['name']);
$name = htmlentities($_FILES['filename']['name']);

// checking if the uploaded file has the correct extension, else ending program
switch($_FILES['filename']['type']) {
    case 'multipart/form-data' : $ext = 'txt'; break;
    case 'text/css' : $ext = 'css'; break;
    case'text/html' :$ext='html';break;
    case 'text/plain' : $ext = 'asm'; break;
    default : $ext = ''; break;
}
if($ext){
    echo "<h3>File uploaded sucessfully!</h3>";    

}
else {
    echo "Invalid input file.";
    die;
}

// opening the file or ending the program
$fh = fopen($name, 'r') or die("Failed to open file<br>");

// getting the input from the text file line by line and passing the value(s) to the primenumber function right away
if ($fh) {

    // getting the values from the file line by line, sanitizing it, and passing to the primenumber() function 
    // to calculate the values in range
    while ($line = htmlentities(fgets($fh))) {
        [$a, $b] = explode(" ", $line);
        $primesInstance->primenumber(trim($a), trim($b));
        echo "<br/>";
    }
    // calling testerFunction() only if file uploaded successfuly
    $primesInstance->testerFunction();
}
fclose($fh);

}



// the definition of a class with its functions
class Primes{
    
    // constructor for the file
    public function __construct(){
    }

    /*
    Given two numerical parameters in input, this function checks the two input values for corectness 
    and then calls the calculatePrimes() function to compute and print the primes
    */
    public function primenumber($a, $b) {

    echo "<b>Prime numbers between ".$a." and ".$b." are:</b> \n";

    // checking the input values and continuing the program if invalid input
    if ($a < 0 || $b < 0){
        echo "Error: input value is a negative number. Both numbers must be positive integers.";
        return 0;
    }
    else if (is_numeric($a) == '0' || is_numeric($b) == '0'){
        echo "Error: input value is not a number. Both input values must be integers.";
        return 0;
    }
    else if (!$a || !$b){
        echo "Error: invalid number of inputs. There must be two inputs.";
        return 0;
    }
    else if ($a > $b){
        echo "Error: Invalid order of inputs. First input must be a larger value than the second input.";
        return 0;
    }
    

    return $this->calculatePrimes($a, $b);
   
    }

    // private helper function that calculates primes in the range (inclusive)
    private function calculatePrimes($a, $b){
        // logic to check if a number between $a and $b is a prime number and printing it
        for($i = $a; $i < $b + 1; $i++) {
            $n = 0;

        for($j = 2; $j < ($i/2+1); $j++) {
            if($i % $j == 0){
            $n++;
            break;
            }
        }
        
        if ($n == 0){
            echo $i." ";
        } 
        }
    }

    // helper tester function which contains the correct output for every input fed to the primenumber() function
    public function testerFunction(){

    echo "<hr>";
    echo "Function output of the first input should be f(3 40) = 3 5 7 11 13 17 19 23 29 31 37<br>";
    echo "Function output of the second input should be an error f(1)<br>";
    echo "Function output of the third input should be f(5 9) = 5 7<br>";
    echo "Function output of the fifth input should be f(30 90) = 31 37 41 43 47 53 59 61 67 71 73 79 83 89<br>";
    echo "Function output of the sixth input should be f(4 x) an error<br>";
    echo "Function output of the seventh input should be f(13 76) = 13 17 19 23 29 31 37 41 43 47 53 59 61 67 71 73<br>";
    echo "Function output of the eight input should be f(42 90) = 43 47 53 59 61 67 71 73 79 83 89<br>";
    echo "Function output of the ninth input should be f(3 -90) an error<br>";
    echo "Function output of the tenth input should be f(90 140) = 97 101 103 107 109 113 127 131 137 139<br>";
    echo "Function output of the eleventh input should be f(2 40) = 2 3 5 7 11 13 17 19 23 29 31 37<br>";
    echo "Function output of the twelfth input should be f(124 280) = 127 131 137 139 149 151 157 163 167 173 179 181 191 193 197 199 211 223 227 229 233 239 241 251 257 263 269 271 277<br>";
    }
    }
?>