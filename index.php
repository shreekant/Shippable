<html>
<head>
<title>WelCome - Count open issues in GitHub repository</title>
</head>
<body>
<form action="" method="POST">
<h2>Count open issues in GitHub repository</h2>
<input type="text" name="url" placeholder="Full URL of GitHub repository" size="60">
<input type="submit" name="submitButton">
</form>
</body>
</html>

<?php
if(isset($_POST['submitButton']))
{
    $input_url = $_POST['url'];
    $input_array_url =  explode('/',$input_url);     //Break the input url in array

    //Validate the input url
    if(strcmp($input_array_url[0],"https:")||strcmp($input_array_url[1],"")||strcmp($input_array_url[2],"github.com")||empty($input_array_url[3])||empty($input_array_url[4]))
    {
        die("</br>Invalid Url !!! Url should be in format <b>https://github.com/{org_name or username}/{repo_name}/</b><br>");
    }

    //url for github Api, $input_array_url[3] has the user/organisation name, put_url_array[3] contain repository name
    $url = "https://api.github.com/repos/".$input_array_url[3]."/".$input_array_url[4]; 
    $result = getResultApi($url); 	//call the function to get the result in array
    $total_open_issues = $result["open_issues_count"];     //Get total number of open issues using the array
    echo "<table style='width:30%' border='1'><tr><td>Total Open Issues</td><td>".$total_open_issues."</td></tr>";
    
    $time_last24hrs = date('Y-m-d\TH:i:s.Z\Z', strtotime('-1 day', time()));		//Date, Time : 1 day or 24 hours ago in ISO 8601 Format
    //url of GitHub Api with 'since' parameter to get issues from last 24hrs 
    $url = "https://api.github.com/repos/".$input_array_url[3]."/".$input_array_url[4]."/issues?since=".$time_last24hrs;     
    $result = getResultApi($url); 	//call the function to get the result in array
    $issues_last24hr = count($result);		//count of issues that were raised 24hrs ago
    echo "<tr><td>Number of open issues that were opened in the last 24 hours</td><td>".$issues_last24hr."</td></tr>";

    $time_7daysago = date('Y-m-d\TH:i:s.Z\Z', strtotime('-7 day', time())); 		// upto 7days
    //url of GitHub Api with 'since' parameter to get issues upto 7days  
    $url = "https://api.github.com/repos/".$input_array_url[3]."/".$input_array_url[4]."/issues?since=".$time_7daysago;
    $result = getResultApi($url);		//call the function to get the result in array
    $issues_last7days = count($result);			//count of issues that were raised in last 7days
    echo "<tr><td>Number of open issues that were opened more than 24 hours ago but less than 7 days ago</td><td>".($issues_last7days-$issues_last24hr)."</td></tr>";
    echo "<tr><td>Number of open issues that were opened more than 7 days ago</td><td>".($total_open_issues-$issues_last7days)."</td></tr></table>";
}       

function getResultApi($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url); 		//Set the url
    curl_setopt($ch, CURLOPT_USERAGENT, "anyusername"); 		//Set user Agent as username
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Accept: application/json'));    //Accept the response as json
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		//Returns the response
    $result=curl_exec($ch);
    curl_close($ch);    
    $new_result=json_decode($result,true);		//Decode the json in array
    return $new_result;
}
?>
