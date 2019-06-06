<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExtractController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
    * Calculate Transaction Fee
    *   
    */
    public function extractTransactionFee() {
        $contents =  file_get_contents("sample.txt");
        $searchArray= array('MASTERCARD SALES DISCOUNT','MASTERCARD DEBIT SALES','VISA DEBIT SALES DISCOUNT'
        ,'VISA SALES DISCOUNT','DISCOVER SALES DISCOUNT','DEBIT SALES DISCOUNT');
        foreach($searchArray as $k=> $searchfor){
                // escape special characters in the query
                $pattern = preg_quote($searchfor, '/');
                // finalise the regular expression, matching the whole line
                $pattern = "/^.*$pattern.*\$/m";
                // search, and store all matching occurences in $matches
                if(preg_match_all($pattern, $contents, $matches)){

                $string=implode("\n", $matches[0]);
                
                if(strstr($string, $searchfor) != NULL) {
                    if (($pos = strpos($string, ".")) !== FALSE) { 
                        $Next = substr($string, $pos); 
                    }
                    $n = strpos($Next, ' '); 
                     $discRate = substr($Next,0, $n);

                    if (($pos1 = strpos($string, "$")) !== FALSE) { 
                        $Next1 = substr($string, $pos1); 
                    }
                    $n1 = strpos($Next1, ' '); 
                    $amount = substr($Next1,1, $n1);


                    $name = explode(' ', $searchfor);
                    $a = $name[0].' '.$name[1];
                    $finalarray[$a] = array();;
                    $finalarray[$a]['discRate']=$discRate;
                    $finalarray[$a]['amount']=$amount;
                    
                   
                }
            } else {
                echo "No matches found";
            }
        }
        echo "<pre>";print_r($finalarray);

    }

    /**
    * starting with “VI-” and ending with “(PP)” or “(DB)
    *
    *starting with “MC-” and ending with “(DB)   
    */
    public function extractInterchnage() {
        $contents =  file_get_contents("sample.txt");
        //starting with “VI-” and ending with “(PP)” or “(DB)”
        $searchArray= array('VI-');
        foreach($searchArray as $k=> $searchfor){
                // escape special characters in the query
                $pattern = preg_quote($searchfor, '/');

                // finalise the regular expression, matching the whole line
                $pattern = "/^.*$pattern.*\$/m";
                // search, and store all matching occurences in $matches
                if(preg_match_all($pattern, $contents, $matches)){
                    //echo $string=implode("\n", $matches[0]);
                    $DBValue=0;
                    $PPValue=0;
                    $totalVi=0;
                    foreach($matches[0] as $val) {
                        if (($pos = strpos($val, "(DB)")) !== FALSE && strpos($val, "Interchange charges") !== FALSE) { 
                            if (($pos1 = strpos($val, "-$")) !== FALSE) { 
                              $DBValue  = substr($val,$pos1+2, 5);  
                                
                            } 
                        }
                        if (($pos = strpos($val, "(PP)")) !== FALSE &&  strpos($val, "Interchange charges") !== FALSE) { 
                           
                            if (($pos1 = strpos($val, "-$")) !== FALSE) {
                               $PPValue  = substr($val,$pos1+2, 5);
                           } 
                        }
                    $totalVi += $DBValue;
                    $totalVi += $PPValue;
                    }
                } else {
                    echo "No matches found";
                }
                echo "Total of starting with VI- and ending with (PP) or (DB)=<b>" .$totalVi.'</b>';
            }

            //starting with “MC-” and ending with “(DB)
            $searchArray= array('MC-');
            foreach($searchArray as $k=> $searchfor){
                    // escape special characters in the query
                    $pattern = preg_quote($searchfor, '/');
                    // finalise the regular expression, matching the whole line
                    $pattern = "/^.*$pattern.*\$/m";
                    // search, and store all matching occurences in $matches
                    if(preg_match_all($pattern, $contents, $matches)){
                        //echo "<pre>";print_r($matches[0]);
                        //echo $string=implode("\n", $matches[0]);
                        $DBValue=0;
                        foreach($matches[0] as $val) {
                            if (($pos = strpos($val, "(DB)")) !== FALSE &&  strpos($val, "Interchange charges") !== FALSE) { 
                                if (($pos1 = strpos($val, "-$")) !== FALSE ) { 
                                $DBValue  += substr($val,$pos1+2, 5);  
                                    
                                } 
                            }
                
                        }
                    } else {
                        echo "No matches found";
                    }
                    echo "<br>";
                    echo "<br>";
                    echo "Total of starting with MC- and ending with (DB)=<b>". $DBValue.'</b>';

            }



    }

   
}
