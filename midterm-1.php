<?php 


// this displays the Select File and Upload buttons and reloads the page after pressing Upload
echo <<<_END
<html><head><title>PHP Form Upload</title></head><body>
<form method='post' action='midterm.php' enctype='multipart/form-data'> Select File: <input type='file' name='filename' size='10'>
<input type='submit' value='Upload'>
</form>
</body></html>
_END;

   /** This if statement checks/validates the extension of the file 
    */
    if ($_FILES){
        // sanitizing the name of the file and getting the file info from path_parts (testing purposes)
        // $path_parts = pathinfo($_FILES['filename']['name']);
        $name = htmlentities($_FILES['filename']['name']);
    
        // checking if the uploaded file has the correct extension, else ending program
        switch($_FILES['filename']['type']) {
            case 'text/plain' : $ext = 'txt'; break;
            default : $ext = ''; break;
        }
        if($ext){
            echo "<h3>File uploaded sucessfully!</h3>";    
        }
        else {
            echo "Invalid input file.";
            die;
        }
    
        // getting the input from the text file 
        if ($_FILES) {
            // saving and sanitizing the input from the file
            // file_get_contents automatically opens and closes file
            $string = htmlentities(file_get_contents($_FILES['filename']['tmp_name']));
            // ignoring all the tabs, spaces, and new lines
            $string = preg_replace('/\s\s+/', "", $string);
            // setting the width of the 2D array
            $array_width = 20;
            // calling a function that creates the 20x20 grid
            $array = gridStr($string);
        }
        // calling a function that calculates all the multiples
        findMultiples($array, $array_width);
        tester();

    }


    // this function accepts a string of characters and returns a 2D array $s
     function gridStr($str){

        $string_length = strlen($str);

        //checking if there are more than 400 characters --> cutting the string short
        if($string_length > 400){
            $string_length = 400;
            echo "<br><b>The file was not formatted correctly (more than 400 characters). Setting length to 400 characters...</b><br>";
        }
        // if there are less than 400 characters --> filling the string array with 0s 
        else if($string_length < 400){
            $str = str_pad($str, 400, "0", STR_PAD_RIGHT);
            echo "<br><b>The file was not formatted correctly (less than 400 characters). Setting length to 400 characters...</b><br>";
        }

        $string_iterator = 0;
        $row = 20;
        $column = 20;    

        // fill the new 2D array with zeros 
        $s = array_fill(0, $row, array_fill(0, $column, 0));

        // convert the string into 20x20 grid
        for ($i = 0; $i < $row; $i++)
        {
            for ($j = 0; $j < $column; $j++)
            {
                // check if the current character is numeric --> if not, convert it to 0
                if(!is_numeric($str[$string_iterator])){
                    $str[$string_iterator] = 0;
                }
                else {
                    $s[$i][$j] = $str[$string_iterator];
                }
                $string_iterator++;
                // print out the grid (for testing purposes)
                // echo $s[$i][$j];
            }
        }
        return $s;
    }

    // this function accepts a 2D array and the width of the array (20 columns) and calculates the largest 
    // multiple of 4 numbers in horizontal, vertical, diagonal, and antidiagonal direction
    // does not return any value
    function findMultiples($s, $n){

        // renaming array, setting overall max and current value iterators 
        $array = $s;
        $max = 0;     
        $current = 0;

        $horizontalMax = 0;
        $horizontalIterator = 0;

        $verticalMax = 0;
        $verticalIterator = 0;

        $diagonalMax1 = 0;
        $diagonalIterator1 = 0;

        $diagonalMax2 = 0;
        $diagonalIterator2 = 0;

        // iterate the rows
        for ( $i = 0; $i < $n; $i++)
        {
            // iterate the columns.
            for ( $j = 0; $j < $n; $j++)
            {
                // this if statement checks horizontal multiples
                if (($j - 3) >= 0){
                    $horizontalMax = horizontalMultiples($array, $horizontalIterator, $horizontalMax, $i, $j);
                }
                
                // this if statement checks vertical multiples
                if (($i - 3) >= 0){
                    $verticalMax = verticalMultiples($array, $verticalIterator, $verticalMax, $i, $j);
                }
    
                // this if statement checks diagonal multiples
                // diagonal starting at top left going down right
                if (($i - 3) >= 0 and ($j - 3) >= 0){
                    $diagonalMax1 = diagonalMultiples($array, $diagonalIterator1, $diagonalMax1, $i, $j);
                }
                
                // this if statement checks antidiagonal multiples
                // --> from bottom left to right
                if (($i - 3) >= 0 and ($j + 3) <= 19){
                    $diagonalMax2 = antidiagonalMultiples($array, $diagonalIterator2, $diagonalMax2, $i, $j);
                }            
            }
        }
        

        // these statements print maxima for every direction
        echo "horizontal max is: $horizontalMax<br>";
        echo "vertical max is: $verticalMax<br>";
        echo "diagonal max (top left to bottom right) is: $diagonalMax1 <br>";
        echo "antidiagonal max (bottom left to top right) is: $diagonalMax2 <br>";


        // this statement compares all max values and chooses what is the absolute maximum
        $absoluteMax = $verticalMax; 
        if ($horizontalMax > $absoluteMax){
            $absoluteMax = $horizontalMax;
        }
        else if($diagonalMax1 > $absoluteMax){
            $absoluteMax = $diagonalMax1;
        }
        else if($diagonalMax2 > $absoluteMax){
            $absoluteMax = $diagonalMax2;
        }
        echo "max result is: $absoluteMax <hr>";
    }

    // helper functions 
    function horizontalMultiples($array, $horizontalIterator, $horizontalMax, $i, $j){
        $horizontalIterator = $array[$i][$j] *
                                $array[$i][$j - 1] *
                                $array[$i][$j - 2] *
                                $array[$i][$j - 3];

        if ($horizontalMax < $horizontalIterator)
                $horizontalMax = $horizontalIterator;
        return $horizontalMax;
    }
    function verticalMultiples($array, $verticalIterator, $verticalMax, $i, $j){
        $verticalIterator = $array[$i][$j] *
                                $array[$i - 1][$j] *
                                $array[$i - 2][$j] *
                                $array[$i - 3][$j];

            if ($verticalMax < $verticalIterator)
                $verticalMax = $verticalIterator;
        return $verticalMax;
    }

    function diagonalMultiples($array, $diagonalIterator1, $diagonalMax1, $i, $j){
        $diagonalIterator1 = $array[$i][$j] *
                            $array[$i - 1][$j - 1] *
                            $array[$i - 2][$j - 2] *
                            $array[$i - 3][$j - 3];
                    
            if ($diagonalMax1 < $diagonalIterator1)
                $diagonalMax1 = $diagonalIterator1;
        return $diagonalMax1;
    }

    function antidiagonalMultiples($array, $diagonalIterator2, $diagonalMax2, $i, $j){
        $diagonalIterator2 = $array[$i][$j] *
                            $array[$i - 1][$j + 1] *
                            $array[$i - 2][$j + 2] *
                            $array[$i - 3][$j + 3];
                    
            if ($diagonalMax2 < $diagonalIterator2)
                $diagonalMax2 = $diagonalIterator2;
        return $diagonalMax2;
    }


    function tester() {

        echo "<h2>Tester function</h2>";
        //exactly 400 numbers
        $input1 = "7163626956188267042885861560789112949495657273330010533678815258490771167055601353697817977846174064839722413756570560578216637048440319989096983520312774506326125406987471585238636689664895044524452305886116467109405077164271714799244429281786645835912456652924219022671055626321071984038509624554448458015616609791913362229893423380308135731671765313306249193035890729629049156070172427121883998797";
        // spaces, letters, symbols and new lines included 
        $input2 =   "123 a - 4a1
                    1242
                    2f13f123f13";
        // empty string/file
        $input3 = "";
        // less than 400 characters, only letters
        $input4 = "aaa";
        // more than 400 numbers
        $input5 = "7249759272402840254972427429428463626956188267042885861560789112949495657273330010533678815258490771167055601353697817977846174064839722413756570560578216637048440319989096983520312774506326125406987471585238636689664895044524452305886116467109405077164271714799244429281786645835912456652924219022671055626321071984038509624554448458015616609791913362229893423380308135731671765313306249193035890729629049156070172427121883998797";

        echo "String with exactly 400 numbers:<br>";
        $a1 = gridStr($input1);
        findMultiples($a1, 20);

        echo "String with less than 400 characters, spaces, letters, symbols, and new lines included:<br>";
        $a2 = gridStr($input2);
        findMultiples($a2, 20);

        echo "Empty string/file:<br>";
        $a3 = gridStr($input3);
        findMultiples($a3, 20);

        echo "less than 400 characters, only letters:<br>";
        $a4 = gridStr($input4);
        findMultiples($a4, 20);

        echo "String with more than 400 numbers:<br>";
        $a5 = gridStr($input5);
        findMultiples($a5, 20);
    }
?>