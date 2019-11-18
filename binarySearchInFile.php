<?php

function binarySearchInFile($file, $ip, $debug = false)
{

    if(file_exists($file))
    {
        if(is_readable($file))
        {
            $fileSize = stat($file)['size'];//bytes

            if($fileSize > 0)
            {
                $h = fopen($file, 'r');
                $eol_char = "\n";
                $eol_size = 1;

                $longip = ip2long($ip);

                $low = 0;
                $high = $fileSize;

                if($debug)
                {
                    echo "start low is $low bytes<br>";
                    echo "start high is $high bytes<br>";
                    echo '<hr>';
                }


                $temp = 0;


                while ($low < $high) {

                    if($debug)
                    {
                        echo "high is $high bytes<br>";
                        echo "low is $low bytes<br>";
                        echo 'ftell: '.ftell($h).' bytes<br>';
                    }


                    if($temp > 100) die('salvation from infinity loop');

                    // compute aprox middle bytes
                    $mid = floor(($low + $high) / 2);

                    fseek($h, $mid, SEEK_SET);

                    //search newline
                    do{
                        fseek($h, -1, SEEK_CUR);
                        $eol = fgetc($h);
                        fseek($h, -$eol_size, SEEK_CUR);

                    }while ($eol != $eol_char && ftell($h)>0 );

                    $position = ftell($h);

                    if ($position != 0)
                        fseek($h, $eol_size, SEEK_CUR);


                    ///////////////////////
                    /// busy logic
                    $str = fgets($h);

                    $iso = getIso($str,$longip);

                    if(isset($iso['iso'])) return $iso;

                    ///////////////////////////////
                    if($debug) echo '<hr>';

                    if ($longip < $iso['start']) {
                        if($debug) echo 'search the left side file<br>';
                        $high = $position;

                    }
                    else {
                        if($debug) echo 'search the right side of the file<br>';
                        $low = ftell($h);
                    }

                    if(feof($h))
                    {
                        return 'file is eof';
                    }

                    $temp++;

                }

                // If we reach here element x doesnt exist
                //return false;
                return "If we reach here element $ip doesnt exist";

            }
            else
            {
                return 'file is empty';
            }
        }
        else
        {
            return 'file not readable';//return false;
        }
    }
    else
    {
        return 'file not found';
    }
}

function getIso($str, $longip)
{
    $out = [];

    preg_match('%start="(\d{1,})"%', $str, $m);
    $start = $m[1];
    $out['start'] = $start;

    preg_match('%end="(\d{1,})"%', $str, $m);
    $end = $m[1];
    $out['end'] = $end;

    if($longip >= $start AND $longip <= $end)
    {
        preg_match('%>([A-Z]{1,3})<%', $str, $m);
        $out['iso'] = $m[1];
    }
    return $out;
}

var_dump(binarySearchInFile('GeoIP-105_20191015.xml', '123.123.123.123'));




