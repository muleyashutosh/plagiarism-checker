<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Syne+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Staatliches&display=swap" rel="stylesheet">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if($_POST['fullText']) echo "Results"; else echo "Home"; ?></title>
    <script src="https://kit.fontawesome.com/d591665a2d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css" media="screen, print">
    <?php 
        if($_POST['fullText']) 
            echo '<link rel="icon" href="https://ik.imagekit.io/milyzn5unt/results_BJjHw8XeCI.svg" type="image/svg">'; 
        else 
            echo '<link rel="icon" href="https://ik.imagekit.io/milyzn5unt/home_i7eMYJwxV.png" type="image/png">';
    ?>

</head>
<body>
    <div class = 'head'>Plagiarism Checker</div>
    <div class = 'main'>
        <div class = 'container'>
            <form id = 'myForm' action="" method = "POST">
                <div id = 'formDiv'>
                    <textarea name="fullText" id ='fullText' class = "input" required><?php $data = $_POST['fullText'];echo "$data";?></textarea>
                    <label class = 'inputMsg'>Enter Text to Run Check</label><br>
                    <input class = 'no-print' onclick='myFunc()' id = 'sub-btn' type="submit" align='center' value= 'Check'>
                    <?php
                        if($_POST['fullText']) {
                            echo "<script>document.getElementById('fullText').disabled = true;";
                            echo "document.getElementById('sub-btn').disabled = true;";
                            echo "</script>";

                        }
                        else {
                            echo "<script>document.getElementById('fullText').enabled = 'true';";
                            echo "document.getElementById('sub-btn').enabled = 'true';</script>";
                        }
                    ?>
                </div>
            </form> 
            <div id = 'image' class = 'image'></div>
        </div>
        
        <?php
            // getting the form input and preparing the results block
            require 'simple_html_dom.php';
            $data = $_POST['fullText'];
            $not_printed = true;
            $data_arr = explode(PHP_EOL, $data);
            for($i = 0; $i < count($data_arr) ; $i++) {
                $data_arr[$i] = trim($data_arr[$i]);
            }
            $data_arr = array_filter($data_arr);
            $totalSentences = 0;
            $plagUrls = [];
            $plagCount = 0;
            foreach($data_arr as $line) {
                $line = explode(". ",$line);
                for($i = 0; $i < count($line) ; $i++) {
                    $line[$i] = trim($line[$i], ' ";.,“”');
                    $line[$i] = trim($line[$i], "'");
                }
                $line = array_filter($line);
                if(count($line) > 0 && $not_printed) {
                    echo "<div class = 'results' id='result'><h4>Results</h4>";
                    $not_printed = false;
                }

                $totalSentences += count($line);
                // print_r($data);
                
                foreach($line as $sentence) {
                    if(strlen($sentence) < 2) {
                        continue;
                    }
                    $s1 = "";
                    $n = strlen($sentence);
                    $sentence = str_replace('“','"',$sentence);
                    $sentence = str_replace('”','"',$sentence);
                    $sentence = str_replace('‘',"'",$sentence);
                    $sentence = str_replace('’',"'",$sentence);

                    $s1 = "";
                    $n = strlen($sentence);
                    for($i = 0; $i < $n; $i++) {
                        // echo $sentence[$i]."<br>";
                        if($sentence[$i] == ' ' 
                            && $i + 1 < $n 
                            && ($sentence[$i+1] != '.')
                            && ($sentence[$i+1] != '"')
                            && ($sentence[$i+1] != "'")
                            && !($sentence[$i+1] >= 0 && $sentence[$i+1] <= 9 )
                            && !($sentence[$i+1] >= 'a' && $sentence[$i+1] <= 'z') 
                            && !($sentence[$i+1] >= 'A' && $sentence[$i+1] <= 'Z')) {
                            continue;
                        }
                        if($sentence[$i] != '.' 
                            && $sentence[$i] != ' '
                            && ($sentence[$i] != '"')
                            && ($sentence[$i] != "'")
                            && !($sentence[$i] >= 0 && $sentence[$i] <= 9 )
                            && !($sentence[$i] >= 'a' && $sentence[$i] <= 'z') 
                            && !($sentence[$i] >= 'A' && $sentence[$i] <= 'Z')) {
                            
                            if($i + 1 < $n && $sentence[$i + 1] != ' ') {
                                $s1 .= $sentence[$i].' ';
                            }
                            else {
                                $s1 .= $sentence[$i];
                            }

                        }
                        else {
                            $s1 .= $sentence[$i];
                        }
                        
                    }
                    $sentence = $s1;
                    $query = urlencode($sentence);
                    $url = 'https://www.google.com/search?q='.$query;
                    // echo $url;
                    $result = file_get_html($url);
                    $found = false;
                    foreach($result->find('#main div .ZINbbc.xpd.O9g5cc.uUPGi') as $entry) {
                        // echo $entry;
                        $text = $entry->find('.kCrYT div .BNeawe.s3v9rd.AP7Wnd div div (.BNeawe.s3v9rd.AP7Wnd)');
                        $text = $text[0]->plaintext;
                        if(strlen($text) < 2) {
                            continue;
                        }
                        $text = str_replace('&#8220;','"',$text);
                        $text = str_replace('&#8221;','"',$text);
                        $text = str_replace('&#8216;',"'",$text);
                        $text = str_replace('&#8217;',"'",$text);
                        $s1 = "";
                        $n = strlen($text);
                        for($i = 0; $i < $n; $i++) {
                            if($text[$i] == ' '
                                && $i + 1 < $n 
                                && ($text[$i+1]!= '.')  
                                && ($text[$i+1] != '"')
                                && ($text[$i+1] != "'")
                                && !($text[$i+1] >= 0 && $text[$i+1] <= 9 )
                                && !($text[$i+1] >= 'a' && $text[$i+1] <= 'z') 
                                && !($text[$i+1] >= 'A' && $text[$i+1] <= 'Z')) {
                                continue;
                            }
                            if($text[$i] != '.' 
                                && $text[$i] != ' '
                                && ($text[$i] != '"')
                                && ($text[$i] != "'")
                                && !($text[$i] >= 0 && $text[$i] <= 9 ) 
                                && !($text[$i] >= 'a' && $text[$i] <= 'z') 
                                && !($text[$i] >= 'A' && $text[$i] <= 'Z')) {
                                
                                if($i + 1 < $n && $text[$i + 1] != ' ') {
                                    $s1 .= $text[$i].' ';
                                }
                                else {
                                    $s1 .= $text[$i];
                                }
                
                            }
                            else {
                                $s1 .= $text[$i];
                            }
                            
                        }
                        $text = $s1;

                        $urlstring = $entry->find('.kCrYT a');
                        $urlstring = $urlstring[0]->href;
                        $pattern = "/q=https:\/\/(.+?)\//";
                        preg_match($pattern, $urlstring, $matches);
                        $urlstring = $matches[1];
                        // echo "$urlstring<br>$text<br><br>";
                        // $arr[$urlstring] = $text;
                        if (strpos($text, $sentence) !== false) {
                            $plagCount++;
                            $plagUrls[$urlstring]++;
                            echo "<div class = 'sent'>
                                    <div class = 'plag'>plagiarised</div>$sentence - <strong>$urlstring</strong>
                                </div><br>";
                            $found = true;
                            break;
                        }
                    }
                    if($found === false) {
                        $url = 'https://www.google.com/search?q='.$query;
                        $url .= '&cr=countryIN';
                        $result = file_get_html($url);
                        foreach($result->find('#main div .ZINbbc.xpd.O9g5cc.uUPGi') as $entry) {
                            // echo $entry;
                            $text = $entry->find('.kCrYT div .BNeawe.s3v9rd.AP7Wnd div div (.BNeawe.s3v9rd.AP7Wnd)');
                            $text = $text[0]->plaintext;
                            if(strlen($text) < 2) {
                                continue;
                            }
                            $text = str_replace('&#8220;','"',$text);
                            $text = str_replace('&#8221;','"',$text);
                            $text = str_replace('&#8216;',"'",$text);
                            $text = str_replace('&#8217;',"'",$text);
                            $s1 = "";
                            $n = strlen($text);
                            for($i = 0; $i < $n; $i++) {
                                if($text[$i] == ' '
                                    && $i + 1 < $n 
                                    && ($text[$i+1]!= '.')  
                                    && ($text[$i+1] != '"')
                                    && ($text[$i+1] != "'")
                                    && !($text[$i+1] >= 0 && $text[$i+1] <= 9 )
                                    && !($text[$i+1] >= 'a' && $text[$i+1] <= 'z') 
                                    && !($text[$i+1] >= 'A' && $text[$i+1] <= 'Z')) {
                                    continue;
                                }
                                if($text[$i] != '.' 
                                    && $text[$i] != ' '
                                    && ($text[$i] != '"')
                                    && ($text[$i] != "'")
                                    && !($text[$i] >= 0 && $text[$i] <= 9 ) 
                                    && !($text[$i] >= 'a' && $text[$i] <= 'z') 
                                    && !($text[$i] >= 'A' && $text[$i] <= 'Z')) {
                                    
                                    if($i + 1 < $n && $text[$i + 1] != ' ') {
                                        $s1 .= $text[$i].' ';
                                    }
                                    else {
                                        $s1 .= $text[$i];
                                    }
                    
                                }
                                else {
                                    $s1 .= $text[$i];
                                }
                                
                            }
                            $text = $s1;

                            $urlstring = $entry->find('.kCrYT a');
                            $urlstring = $urlstring[0]->href;
                            $pattern = "/q=https:\/\/(.+?)\//";
                            preg_match($pattern, $urlstring, $matches);
                            $urlstring = $matches[1];
                            // echo "$urlstring<br>$text<br><br>";
                            // $arr[$urlstring] = $text;
                            if (strpos($text, $sentence) !== false) {
                                $plagCount++;
                                $plagUrls[$urlstring]++;
                                echo "<div class = 'sent'>
                                        <div class = 'plag'>plagiarised</div>$sentence - <strong>$urlstring</strong>
                                    </div><br>";
                                $found = true;
                                break;
                            }
                
                        }
                        if($found === false) {
                            echo "<div class = 'sent'>
                                        <div class = 'uni'>unique</div>$sentence
                                    </div><br>";
                        }
                    }
                }
            }
            echo "</div>";
            // print_r($plagUrls);
            $plagPercent = ($plagCount / $totalSentences) * 100;
            $unPercent = 100 - $plagPercent;

        ?>
        
        <?php
            // printing the top sources block
            if(count($plagUrls) > 0) {
                echo "<div class = 'topSource'>";
                echo "<h4>Top Sources</h4><br>";
                foreach($plagUrls as $key => $value) {
                    echo "<div class = 'sent'>";
                        echo "$key - <strong style='color:rgb(245, 59, 59)'> ";
                        $x = ($value / $plagCount) * 100;
                        echo number_format((float)$x, 2, '.', '');
                        echo "% </strong>";
                    echo "</div><br>";
                }
                echo " </div>";
            }
            else {
                echo "<script>
                        document.getElementById('result').style.width = '96%';
                        document.getElementById('result').style.margin = '2% 2% 3% 2%';
                    </script>";
            }
        ?>
        <?php
            // printing the overview block
                if(count($data) > 0) {
                    echo "<div class = 'overview' align='center'>";
                        echo "<h4>Overview</h4>";
                        echo "<div class = box-contain>";
                            echo "<div class = 'box1'>";
                                echo "<div><strong>";
                                    echo number_format((float)$plagPercent, 2, '.', '')."%";
                                echo "</strong></div>";
                                echo "<div>";
                                    echo "plagiarised";
                                echo "</div>";
                            echo "</div>";
                            echo "<div class = 'box2'>";
                                echo "<div><strong>";
                                    echo number_format((float)$unPercent, 2, '.', '')."%";
                                echo "</strong></div>";
                                echo "<div>";
                                    echo "unique";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                        echo "<br>";
                    echo "</div>";
                }
        
        ?>
        <?php
            // printing the Download button and Check New button
            if($_POST['fullText']) {
                echo "
                <div class = 'boxes' align='center'>
                    <div class = 'downR' onclick='window.print()'>
                        <i class='fas fa-download'></i>   Download Report
                    </div>
                        <div class = 'CheckNew' type='reset' onclick='checkNew()'>
                    <i class='fas fa-sync-alt'></i>   Check New
                </div>
                </div>";
            }
            
        ?>
    </div>
    <div id = 'attr' align='center'>
        <a href="https://www.freepik.com/vectors/people" target="_blank">Image Vector created by freepik - www.freepik.com</a>
    </div>

    

</body>
<script>
    function checkNew() {
       document.getElementById('myForm').reset();
       window.location.replace(location.pathname);
    }
</script>
</html>
