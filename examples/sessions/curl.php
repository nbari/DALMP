<?php
/**
 * This code can help to 'benchmark' your sessions engine besides checking a site status (up/down)
 */
$url = 'http://dalmp.localbox.org/sessions.php';

require_once '../mplt.php';
$timer = new mplt();

function checkSite($url,$page=null)
{
  $cookie = '/tmp/cookie.txt';
  $agent = '-( DALMP )-';
  $ch=curl_init();
  curl_setopt ($ch, CURLOPT_URL,$url );
  curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_VERBOSE,false);
  curl_setopt ($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
  $page=curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if (isset($page)) {
    return $page;
  } else {
    return ($httpcode>=200 && $httpcode<300) ? true :false;
  }
}

/**
 * benchmark for sessions
 */
for ($i=0; $i < 100; $i++) {
  for ($j=0; $j < 30; $j++) {
    echo checkSite($url,1).PHP_EOL;
  }
  @unlink('/tmp/cookie.txt');
}

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
