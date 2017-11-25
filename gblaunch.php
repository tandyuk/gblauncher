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
$gbzip = "Gunbot.XT.Edition.-.Linux.package.zip"; //filename of the source .zip
//todo:
$gb_md5sum = ""; //
$base_ws_port = 5001; //starting port to use for websockets
$base_wc_port = 3001; //starting port to use for webgui
$start_delay = 2;  //delay between starting bots in seconds

$basedir = dirname(__FILE__);

//methods to get config file
//original, config.js from directory we are run from
$config = json_clean_decode(file_get_contents($basedir.'/config.js'),true);

//pull from accessible server via sftp using pubkey auth
/*$session = ssh2_connect('192.168.66.220', 222);
ssh2_auth_pubkey_file($session, 'james', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa');
$session = ssh2_sftp($session);
$file = file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/config.js');
$config = json_clean_decode($file,true);
if(isset($argv[1]) && ($argv[1] == "su")){
	//self update cos im lazy
	file_put_contents($basedir.'/config.js',$file);
	file_put_contents($basedir.'/gblaunch.php',file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/gblaunch.php'));
	if(!file_exists($basedir.'/'.$gbzip)){
		file_put_contents($basedir.'/'.$gbzip,file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/'.$gbzip));
	}
	//update the rest while we're here  (You can get these from https://github.com/tandyuk/gunbotscripts)
	file_put_contents($basedir.'/profitcalc.php',file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/profitcalc.php'));
	file_put_contents($basedir.'/bittrex-functions.php',file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/bittrex-functions.php'));
	file_put_contents($basedir.'/bittrex-govenor.php',file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/bittrex-govenor.php'));
	die();
}*/

//dont change after here
$writeconfig = $writepm2 = $unzipgb = $createdirs = $createpm2 = $startbots = $stopbots = $delete = false;
$arg = (isset($argv[1]) && (NULL!==$argv[1])?$argv[1]:'help');

switch($arg){
	case "build":
		$createdirs = true;
		$unzipgb = true;
		$writeconfig = true;
		$writepm2 = true;
		$startbots = true;
	break;
	case "buildonly":
		$createdirs = true;
		$writeconfig = true;
		$writepm2 = true;
	break;
	case "start":
		$startbots = true;
	break;
	case "stop":
		$stopbots = true;
	break;
	case "restart":
		$startbots = true;
	break;
	case "reload":
		$writeconfig = true;
	break;
	case "update":
		$unzipgb2 = true;
		$writeconfig = true;
		$startbots = true;
	break;
	case "autoupdate":


	break;
	case "clean":
		$delete = true;
		$stopbots = true;

	break;
	case "exportlog":


	break;
	default:
		echo "Usage instructions:".PHP_EOL.PHP_EOL."build [id]          build bots".PHP_EOL;
		echo "start [id]          Start bot, optional id or all according to start_delay".PHP_EOL;
		echo "stop [id]           Stop bot, optional id or all, according to stop_delay".PHP_EOL;
		echo "restart [id]        Restart bot(s)".PHP_EOL;
		echo "reload [id]         Rebuild config in-place [for bot(s)]".PHP_EOL;
		echo "update              Extract new Gunbot, rebuild configs and restart all bots".PHP_EOL;
		echo "exportlog id file   Export logfile from specified bot to file".PHP_EOL;
		die();
	break;
}



$globalsettings = array();
$globalsettings['exchanges'] = $config['exchanges'];
$globalsettings['bot'] = $config['bot'];
$globalsettings['imap_listener'] = $config['imap_listener'];
$globalsettings['optionals'] = $config['optionals'];

$overrides = $config['overrides'];
$strategies = $config['strategies'];
//$config['pairs'];

//sort and filter pairs
//have we been given an id?
$id = (isset($argv[2]) && (NULL!==$argv[2])?$argv[2]:NULL);
if($id !== NULL){
	//yes, now work out what it is
	//options
	//BTC-BTS  or another currency pair
	//stepgain, customstepgain, and so on to match a particular strategy

	//so first off, is it a normal pair name?
	if(array_key_exists($id,$config['pairs']['bittrex'])){
		//yes, so set it and update the single pair
		$pairs = array("bittrex"=>array($id=>$config['pairs']['bittrex'][$id]));
	}
	elseif(array_key_exists(gethostname(),$config['servers']) && array_key_exists('pairs',$config['servers'][gethostname()]) && array_key_exists('bittrex',$config['servers'][gethostname()]['pairs']) && array_key_exists($id,$config['servers'][gethostname()]['pairs']['bittrex'])){
		//now check if its a server specific pair, and that we're on the right server
		$pairs = array("bittrex"=>array($id=>$config['servers'][gethostname()]['pairs']['bittrex'][$id]));

	}
	elseif(array_key_exists($id,$strategies)){
		//or check if it is a trading strategy
		global $mystrat;
		$mystrat = $id;
		$pairs = array_merge($config['pairs']['bittrex'],(array_key_exists(gethostname(),$config['servers']) && array_key_exists('pairs',$config['servers'][gethostname()])?$config['servers'][gethostname()]['pairs']['bittrex']:array()));
		$pairs = array('bittrex'=>array_filter($pairs,function($v){global $mystrat; return $v['strategy'] == $mystrat;}));
	}else{
		//weve been given an id, but cant work out what it is
	
	
	}

}else{
	//behave normally
	$pairs = $config['pairs'];
}

if(!isset($pairs) || (count($pairs['bittrex'])==0)){
	die('No pairs selected - if specifying a server specific pair - are you on the right server?'.PHP_EOL);
}

//spread pairs over servers
if(array_key_exists('servers',$config)){
	$iservers = count($config['servers']);
	$serverpairs = array();
	$serverlimits = array();
	foreach($config['servers'] as $s=>$d){
		if(array_key_exists('pairs',$d) && array_key_exists('bittrex',$d['pairs'])){
			$serverpairs[$s] = $d['pairs']['bittrex'];
		}else{
			$serverpairs[$s] = array();
		}
	
		if(array_key_exists('max_instances',$d) && array_key_exists('num_instances',$d)){
			$serverlimits[$s] = ($d['max_instances']>$d['num_instances']?$d['max_instances']:$d['num_instances']);
		}elseif(array_key_exists('max_instances',$d)){
			$serverlimits[$s]=$d['max_instances'];
		}elseif(array_key_exists('num_instances',$d)){
			$serverlimits[$s]=$d['num_instances'];
		}else{
			$serverlimits[$s]=9999999;
		}
		
		if($s == gethostname()){
			$globalsetting['ws']['hostname'] = $d['hostname'];
			//my custom settings
			if(array_key_exists('BOT_DELAY',$d)){
				$globalsettings['bot']['BOT_DELAY'] = $d['BOT_DELAY'];
			}
			if(array_key_exists('period_storage_ticker',$d)){
				$globalsettings['bot']['period_storage_ticker'] = $d['period_storage_ticker'];
			}
			if(array_key_exists('interval_ticker_update',$d)){
				$globalsettings['bot']['interval_ticker_update'] = $d['interval_ticker_update'];
			}
			if(array_key_exists('exchanges',$d) && array_key_exists('bittrex',$d['exchanges'])){
				if(array_key_exists('key',$d['exchanges']['bittrex']) && array_key_exists('secret',$d['exchanges']['bittrex'])){
					$globalsettings['exchanges']['bittrex'] = $d['exchanges']['bittrex'];
				}else{
					die("Attempting to specify custom server api/secret for bittrex, but only one of key/secret was provided.".PHP_EOL);		
				}
			}
		}
	}
	if(array_key_exists('pairs',$config) && array_key_exists('bittrex',$config['pairs']) && (count($config['pairs']['bittrex'])>0)){
		$totalpairs=count($config['pairs']['bittrex']);
		$pair=0;
		$error=0;
		$pairkeys = array_keys($config['pairs']['bittrex']);
		while($totalpairs>0){
			foreach($config['servers'] as $s=>$d){
				if($error>50){
					die('ERROR: Were stuck in a loop splitting up pairs'.PHP_EOL);
				}
				if(count($serverpairs[$s])<$serverlimits[$s]){
					$serverpairs[$s][$pairkeys[$pair]] = $config['pairs']['bittrex'][$pairkeys[$pair]];
					$pair++;
					$totalpairs--;
					$error=0;
				}else{
					$error++;
				}
			}

		}
	}
	
	$pairs = array('bittrex'=>$serverpairs[gethostname()]);

}

//extract gunbot
$sourcepath = $basedir.'/gunbot_launcher/source/';

		//extract gunbot files
		if($unzipgb){
			if($debug)		echo "mkdir: " .$sourcepath.'/tulind/lib/binding/Release/node-v57-linux-x64/'.PHP_EOL;
			@		mkdir($sourcepath.'tulind/lib/binding/Release/node-v57-linux-x64/',0777,true);
			if($debug)		echo "mkdir: " .$sourcepath.'/node_modules/sqlite3/lib/binding/node-v57-linux-x64/'.PHP_EOL;
			@		mkdir($sourcepath.'/node_modules/sqlite3/lib/binding/node-v57-linux-x64/',0777,true);
			if($debug)		echo "exec: " .'unzip -j '.$basedir.'/'.$gbzip.' "Gunbot XT Edition - Linux package/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node" -d '.$sourcepath.'tulind/lib/binding/Release/node-v57-linux-x64'.PHP_EOL;
			exec('unzip -o -qq -j '.$basedir.'/'.$gbzip.' "Gunbot XT Edition - Linux package/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node" -d '.$sourcepath.'tulind/lib/binding/Release/node-v57-linux-x64');
			if($debug)		echo "exec: " .'unzip -j '.$basedir.'/'.$gbzip.' "Gunbot XT Edition - Linux package/node_modules/sqlite3/lib/binding/node-v57-linux-x64/node_sqlite3.node" -d '.$sourcepath.'node_modules/sqlite3/lib/binding/node-v57-linux-x64'.PHP_EOL;
			exec('unzip -o -qq -j '.$basedir.'/'.$gbzip.' "Gunbot XT Edition - Linux package/node_modules/sqlite3/lib/binding/node-v57-linux-x64/node_sqlite3.node" -d '.$sourcepath.'node_modules/sqlite3/lib/binding/node-v57-linux-x64');
			if($debug)		echo "exec: " .'unzip -j '.$basedir.'/'.$gbzip.' "Gunbot XT Edition - Linux package/gunthy-linx64" -d '.$sourcepath.PHP_EOL;
			exec('unzip -o -qq -j '.$basedir.'/'.$gbzip.' "Gunbot XT Edition - Linux package/gunthy-linx64" -d '.$sourcepath);
			//sleep(2);

			//update to 505 beta?
//			if(array_key_exists('gb_ver',$config['servers'][gethostname()])){
				//pull updated gunbot exe via sftp
//				file_put_contents($p.'/gunthy-linx64',file_get_contents('ssh2.sftp://'.$session.'/data/management/gunbot/gunthy-linx64'));
//			}

			if($debug)		echo "chmod +x :" .$sourcepath.'gunthy-linx64'.PHP_EOL;
			exec('chmod +x '.$sourcepath.'gunthy-linx64');
			
			
		}





//start looping over the pairs
foreach($pairs as $exchange=>$pa){
	$e = strtolower(substr($exchange,0,1));
	foreach($pa as $pair=>$opts){
		$n = $e.'_'.$pair.'_'.((array_key_exists($opts['strategy'],$strategies) && array_key_exists('REQUIRES',$strategies[$opts['strategy']]))?$strategies[$opts['strategy']]['REQUIRES']:$opts['strategy']);
		echo "Processing ".$n.PHP_EOL;
		$p = $basedir.'/gunbot_launcher/'.$n.'/';
		//make folder structure
if($createdirs){ mkdir($p,0777,true); }
//symlinks!
if($unzipgb){
symlink($sourcepath.'gunthy-linx64',$p.'gunthy-linx64');
symlink($sourcepath.'node_modules',$p.'node_modules');
symlink($sourcepath.'tulind',$p.'tulind');
}
		//create config
		if($writeconfig){
			$myconfig = $globalsettings;
			if(array_key_exists('override',$opts) && array_key_exists('CUSTOM',$opts['override'])){
				//custom overrides method
				if(!array_key_exists($opts['override']['CUSTOM'],$overrides)){
					echo "ERROR: $n trying to use non-existant custom overrides. BAILING OUT.".PHP_EOL;
					continue;
				}
				$c = $overrides[$opts['override']['CUSTOM']]['REQUIRES'];
				if($c !== $opts['strategy']){
					echo "ERROR: $n trying to use " . $c . " custom overrides on ". $opts['strategy']." BAILING OUT.".PHP_EOL;
					continue;
				}
				$myconfig['pairs']=array($exchange=>array($pair=>$opts));
				foreach($overrides[$opts['override']['CUSTOM']] as $key=>$val){
					if($key == "REQUIRES")continue;
					$myconfig['pairs'][$exchange][$pair]['override'][$key] = $val;
				}
				unset($myconfig['pairs'][$exchange][$pair]['override']['CUSTOM']);
				$myconfig['strategies'][$opts['strategy']] = $strategies[$opts['strategy']];
			}
			elseif(array_key_exists($opts['strategy'],$strategies) && array_key_exists('REQUIRES',$strategies[$opts['strategy']])){
				//custom strategy method
				$myconfig['pairs']=array($exchange=>array($pair=>$opts));
				$s = array();
				foreach($strategies[$opts['strategy']] as $key=>$val){
					switch($key){
						case'REQUIRES':
							$t = $val;
							$myconfig['pairs'][$exchange][$pair]['strategy']=$t;
						break;
						case "BOT_DELAY":
						case "period_storage_ticker":
						case "interval_ticker_update":
							$config['bot'][$key]=$val;
						break;
						default:
							$s[$key]=$val;
						break;
					}
				}
				$myconfig['strategies'][$t] = $s;
			}
			else{
				$myconfig['pairs']=array($exchange=>array($pair=>$opts));
				$myconfig['strategies'][$opts['strategy']] = $strategies[$opts['strategy']];
			}
			$myconfig['ws']['port'] = $base_ws_port++;
			$myconfig['ws']['clientport'] = $base_wc_port++;


			//write config
			file_put_contents($p.'config.js',json_encode($myconfig, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
			if(!$startbots)sleep($start_delay);
		}

		if($writepm2){
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
		}

		if($startbots){
			//launch bot
			if($debug) echo "exec: ". 'pm2 restart '.$p.$n.'.config.js';
			exec('pm2 start --no-autorestart '.$p.$n.'.config.js');
			sleep($start_delay);
		}

		if($stopbots){
			//launch bot
			if($debug) echo "exec: ". 'pm2 stop '.$n;
			exec('pm2 stop '.$n);
			if(!$delete)sleep($start_delay);
		}

		if($delete){
			exec('pm2 delete '.$n);
			exec('rm -rf '.$p);
		}
	}
}


if($delete){
	exec('pm2 delete all');
	exec('rm -rf '.$basedir.'/gunbot_launcher/');
}






// Helper Functions

function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if(version_compare(phpversion(), '5.4.0', '>=')) {
        $json = json_decode($json, $assoc, $depth, $options);
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) {
        $json = json_decode($json, $assoc, $depth);
    }
    else {
        $json = json_decode($json, $assoc);
    }
    return $json;
}



?>
