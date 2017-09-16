<?PHP

/*
INSTRUCTIONS:

Download Gunbot zip file to this working dirctory.

Save Complete Gunbot config.js to this working dorectory.


WHAT THIS SCRIPT DOES:
(eg for GB 5.0.4 bittrex BTC-ARK and BTC-BCC)

Read config.js and extract all pairs, exchanges and strategies.
For each pair/exchange/strat combo create GUNBOT_5.0.4/exchange_PAIR_strat/ folder and unzip GB.
Write minimal config.js for the specific pair.

Launch pair with output logged


CONFIG:
*/
$debug=false;

$gbver = "5_0_2"; //version only - do not rename source .zip

//todo:
$gb_md5sum = ""; //


$base_ws_port = 5001; //starting port to use for websockets

$start_delay = 4;  //delay between starting bots in seconds









//dont change after here

$arg = (isset($argv[1]) && (NULL!==$argv[1])?$argv[1]:'help');

switch($arg){
	case "build":


	break;


	case "help":
	case "--help":
	case "-help":
	case "-h":
	case "-?":
	case "/?":
	default:
		echo "Usage instructions:".PHP_EOL.PHP_EOL."build               build bots dirs".PHP_EOL;

		die();
	break;
}



$basedir = dirname(__FILE__);

$config = json_decode(file_get_contents($basedir.'/config.js'),true);
$globalsettings = array();
$globalsettings['exchanges'] = $config['exchanges'];
$globalsettings['bot'] = $config['bot'];
//$globalsettings['ws'] = $config['ws'];  we need to override this
$globalsettings['imap_listener'] = $config['imap_listener'];
$globalsettings['optionals'] = $config['optionals'];

$strategies = $config['strategies'];
$pairs = $config['pairs'];

//var_dump($pairs);

//start looping over the pairs
foreach($pairs as $exchange=>$pa){
$e = strtolower(substr($exchange,0,1));
	foreach($pa as $pair=>$opts){
	$n = $e.'_'.$pair.'_'.$opts['strategy'];
	$p = $basedir.'/gunbot_launcher/'.$n.'/';
		//make folder structure
if($debug)		echo "mkdir: " .$p.'/tulind/lib/binding/Release/node-v57-linux-x64/'.PHP_EOL;
@		mkdir($p.'tulind/lib/binding/Release/node-v57-linux-x64/',0777,true);
		//extract gunbot files
if($debug)		echo "exec: " .'unzip -j '.$basedir.'/GUNBOT_V'.$gbver.'.zip GUNBOT_V'.$gbver.'/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node -d '.$p.'tulind/lib/binding/Release/node-v57-linux-x64'.PHP_EOL;
		exec('unzip -o -qq -j '.$basedir.'/GUNBOT_V'.$gbver.'.zip GUNBOT_V'.$gbver.'/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node -d '.$p.'tulind/lib/binding/Release/node-v57-linux-x64');
if($debug)		echo "exec: " .'unzip -j '.$basedir.'/GUNBOT_V'.$gbver.'.zip GUNBOT_V'.$gbver.'/gunthy-linx64 -d '.$p.PHP_EOL;
		exec('unzip -o -qq -j '.$basedir.'/GUNBOT_V'.$gbver.'.zip GUNBOT_V'.$gbver.'/gunthy-linx64 -d '.$p);
sleep(2);
if($debug)		echo "chmod +x :" .$p.'gunthy-linx64'.PHP_EOL;
		exec('chmod +x '.$p.'gunthy-linx64');
//create config
$config = $globalsettings;
$config['pairs']=array($exchange=>array($pair=>$opts));
$config['ws']['port'] = $base_ws_port++;
$config['strategies'][$opts['strategy']] = $strategies[$opts['strategy']];

//write config
file_put_contents($p.'config.js',json_encode($config, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));


//write pm2 process file
$f = 'module.exports = {
  apps : [{
    name   : "'.$n.'",
    cwd: "'.$p .'",
    script: "gunthy-linx64",
    watch: true,
    args: "--color"
  }]
}';

file_put_contents($p.$n.'.config.js',$f);

//launch bot
if($debug) echo "exec: ". 'pm2 start '.$p.$n.'.config.js';
exec('pm2 start '.$p.$n.'.config.js');
sleep(1);
echo exec('pm2 show '.$n) . PHP_EOL.PHP_EOL;
sleep($start_delay);

	}
}



//mkdir -p GUNBOT_V5_0_0/b_BTC-ARK-stepgain/tulind/lib/binding/Release/node-v57-linux-x64/
//unzip -j GUNBOT_V5_0_0.zip GUNBOT_V5_0_2/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node -d GUNBOT_V5_0_0/b_BTC-ARK-stepgain/tulind/lib/binding/Release/node-v57-linux-x64
//unzip -j GUNBOT_V5_0_0.zip GUNBOT_V5_0_2/gunthy-linx64   -d GUNBOT_V5_0_0/b_BTC-ARK-stepgain/
//chmod +x GUNBOT_V5_0_0/b_BTC-ARK-stepgain/gunthy-linx64



?>
