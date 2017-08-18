<head>
    <title>Amazon Affiliate Link Generator</title>
</head>
<h2>Enter the Amazon link you want to convert, then click the "Make Affiliate Link" button.</h2>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
    <p>
        <input type="text" name="link">
        <input type="submit" value="Make Affiliate Link!">
    </p>
</form>
<?php

/* Decode short URL */
function decode_short($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $a = curl_exec($ch); // $a will contain all headers
    $info = curl_getinfo($ch);

    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

    return $url; // Voila
}

/* Decode a URL to the final URL */
/* Code from: https://gist.github.com/abailiss/1196916 */
function decode_short2($url)
{
	$headers = get_headers($url);
	$headers = array_reverse($headers);
	foreach($headers as $header)
	{
		if (strpos($header, 'Location: ') === 0)
		{
			$url = str_replace('Location: ', '', $header);
			break;
		}
	}

	return $url;
}

/* make a URL small */
function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
{
    //create the URL
    $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;

    //get the url
    //could also use cURL here
    $response = file_get_contents($bitly);

    //parse depending on desired format
    if(strtolower($format) == 'json')
    {
        $json = @json_decode($response,true);
        return $json['results'][$url]['shortUrl'];
    }
    else //xml
    {
        $xml = simplexml_load_string($response);
        return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
    }
}


// Replace with your TAG
// You can get this from generaitng a amazon link and drop the tag=
$affiliate = "?ie=UTF8&tag=rhcpdeals-20";

$link = $_GET['link'];

if (isset($_GET['link']))
{
    // Short Amazon Reff link from amazon.com
    if (strstr($link, 'amzn.to') || (strstr($link, 'slickdeals') ))
    {
        $link = decode_short2($link);
    }

    // Get the PID (which is the product code)
    $pid = basename((false !== strpos($link, '/ref='))
    ? pathinfo($link, PATHINFO_DIRNAME)
    : parse_url($link, PHP_URL_PATH));

    echo "<h4>Here's your new Amazon Affiliate link: </h4>";
    $link = "http://www.amazon.com/gp/product/".$pid.$affiliate;
    echo "SHORT AMAZON LINK<br>";
    
    // create one of those http://amzn.to/ Links
    // You will need to signup for bit.ly and get your API key
    $short = make_bitly_url($link,'Username','API_Key','json');
    
    echo "<H4><A HREF='$short'>$short</A></H4>";
    echo "LONG AMAZON LINK<br>";
    echo "<h4><a href=http://www.amazon.com/gp/product/", $pid, $affiliate, ">http://www.amazon.com/gp/product/", $pid, $affiliate, "</a></h4>"; // This line makes a "clickable" link.
}
?>
<br>
