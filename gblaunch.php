<?PHP

/*
INSTRUCTIONS:
Download Gunbot zip file to this working dirctory.
Save Complete Gunbot config.js to this working directory.

WHAT THIS SCRIPT DOES:

Read config.js and extract all pairs, exchanges and strategies.
For each pair/exchange/strat combo create GUNBOT_5.0.4/exchange_PAIR_strat/ folder and unzip GB.
Write minimal config.js for the specific pair.

Launch pair with output logged via pm2

CONFIG:
*/
$debug=false;
$gbzip = "lin.zip"; //filename of the source .zip
//todo:

$basedir = dirname(__FILE__);

$config = json_clean_decode(file_get_contents($basedir.'/config.js'),true);

//dont change after here
$writeconfig = $writepm2 = $unzipgb = $createdirs = $createpm2 = $startbots = $stopbots = $delete = false;
$arg = (isset($argv[1]) && (NULL!==$argv[1])?$argv[1]:'help');

$exchangecodes = array('poloniex'=>'polo','bittrex'=>'trex','binance'=>'bina','kraken'=>'krak','cryptopia'=>'cryp','bitfinex'=>'bfin');

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
	case "reload":
		$writeconfig = true;
	break;
	case "update":
		$unzipgb = true;
		$writeconfig = true;
		$startbots = true;
	break;
	case "clean":
		$delete = true;
		$stopbots = true;
	break;
	default:
		echo "Usage instructions:".PHP_EOL.PHP_EOL."build [id]          build bots".PHP_EOL;
		echo "build               Build configs and start bots".PHP_EOL;
		echo "buildonly           Build configs only".PHP_EOL;
		echo "stop                Stop bots".PHP_EOL;
		echo "reload              Rebuild config in-place".PHP_EOL;
		echo "update              Extract new Gunbot, rebuild configs and restart all bots".PHP_EOL;
		echo "clean               Stop bots and delete everything".PHP_EOL;
		die();
	break;
}

$globalsettings = array();
$globalsettings['exchanges'] = $config['exchanges'];
$globalsettings['bot'] = $config['bot'];
$globalsettings['imap_listener'] = $config['imap_listener'];
$globalsettings['server'] = load_server_settings($config);

$strategies = $config['strategies'];
$pairs = $config['pairs'];

$paircount = 0;
foreach($pairs as $ex=>$prs){
	$paircount += count($prs);
}

if(!isset($pairs) || ($paircount==0)){
	die('No pairs selected - if specifying a server specific pair - are you on the right server?'.PHP_EOL);
}

//spread pairs/exchanges over instances
$instances = array();
$serverlimit = $globalsettings['server']['max_instances'];
$globalsettings['bot']['BOT_DELAY'] = $globalsettings['server']['bot_delay'];
$assignedpairs = 0;
$failedpairs = 0;
$error = 0;
$distributed = false;
$instindex = array();
$instlimithit = false;

while(!$distributed){
	$lastassignedpairs = $assignedpairs;
	$gotpair=false;
	while(!$gotpair){
		$mypair = false;
		$myex = false;
		if(count($pairs)==0){
			//processed all exchanges
			$gotpair=true;
			continue;
		}
		$es = array_keys($pairs);
		$myex = $es[0];
		if(count($pairs[$myex]) == 0){
			//processed all pairs on this exchange
			unset($pairs[$myex]);
			continue;
		}else{
			//pop the last pair
			$keys = array_keys($pairs[$myex]);
			$mypair = $keys[0];
			$gotpair = true;
		}
	}
	$assignedok = false;
	if($mypair){
		//only execute this if we have a pair to dish out
		if(array_key_exists($myex,$instindex)){
//		echo "myex: ".var_export($myex,true).PHP_EOL."ndexes: ".var_export($instindex,true).PHP_EOL;
			$instpairs = 0;
			foreach($instances[$instindex[$myex]] as $et=>$pt){
				$instpairs+=count($pt);
			}
			if( !$globalsettings['server']['instance_per_pair'] && (count($instances[$instindex[$myex]])>0) && ($instpairs<$globalsettings['server']['pairs_per_instance'])){
				//found my instance
//				echo "1: $myex $mypair >> $instindex[$myex]".PHP_EOL;
				$instances[$instindex[$myex]][$myex][$mypair] = $pairs[$myex][$mypair];
				$assignedok = true;
			}else{
				if(count($instances)<$serverlimit){
					//create a new one
					$index = count($instances);
//				echo "2: $myex $mypair >> $index".PHP_EOL;
					$instances[$index] = array($myex=>array($mypair=>$pairs[$myex][$mypair]));
					$instindex[$myex] = $index;
					$assignedok = true;
				}
				else{
					if(!$globalsettings['server']['instance_per_exchange']){
						//try to find an instance with spare slots.
						$instpairs = array();
						$candidate = false;
						$candidateid = 0;
						foreach($instances as $ik=>$idata){
							$instpairs[$ik] = 0;
							foreach($idata as $ex=>$expairs){
								$instpairs[$ik] += count($expairs);
							}
							if(!$globalsettings['server']['instance_per_pair'] && ($instpairs[$ik] < $globalsettings['server']['pairs_per_instance'])){
								$candidate = true;
								$candidateid = $ik;
							}
						}
						if($candidate){
							if(array_key_exists($myex,$instances[$ik])){
//				echo "3: $myex $mypair >> $candidateid".PHP_EOL;
								$instances[$candidateid][$myex][$mypair] = $pairs[$myex][$mypair];
								$assignedok = true;
							}
							else{
//				echo "4: $myex $mypair >> $candidateid".PHP_EOL;
								$instances[$candidateid][$myex] = array($mypair=>$pairs[$myex][$mypair]);
								$assignedok = true;
							}
						}else{
							$instlimithit=true;
						}
					}else{
						$instlimithit=true;
					}
				}
			}
		}else{
			//no current instance
			if(count($instances)<$serverlimit){
				//create a new one
				$index = count($instances);
//				echo "5: $myex $mypair >> $index".PHP_EOL;
				$instances[$index] = array($myex=>array($mypair=>$pairs[$myex][$mypair]));
				$instindex[$myex] = $index;
				$assignedok = true;
			}elseif(!$globalsettings['server']['instance_per_exchange']){
				//try to find an instance with spare slots.
				$instpairs = array();
				$candidate = false;
				$candidateid = 0;
				foreach($instances as $ik=>$idata){
					foreach($idata as $ex=>$expairs){
						$instpairs[$ik] += count($expairs);
					}
					if(!$globalsettings['server']['instance_per_pair'] && ($instpairs[$ik] < $globalsettings['server']['pairs_per_instance'])){
						$candidate = true;
						$candidateid = $ik;
					}
				}
				if($candidate){
					if(array_key_exists($myex,$instances[$ik])){
//				echo "6: $myex $mypair >> $candidateid".PHP_EOL;
						$instances[$candidateid][$myex][$mypair] = $pairs[$myex][$mypair];
						$assignedok = true;
					}
					else{
//				echo "7: $myex $mypair >> $candidateid".PHP_EOL;
						$instances[$candidateid][$myex] = array($mypair=>$pairs[$myex][$mypair]);
						$assignedok = true;
					}
				}else{
					$instlimithit=true;
				}
			}else{
				$instlimithit=true;				
			}
		}
	}
	if($assignedok && $mypair){
		//pair assigned
		unset($pairs[$myex][$mypair]);
		$assignedpairs++;
	}elseif($mypair && !$assignedok){
		//pair failed to assign
		unset($pairs[$myex][$mypair]);
		$failedpairs++;
	}else{
		//no pair to assign
	}
	//check if theres anything else left
	if(($assignedpairs+$failedpairs) == $paircount){
		//all pairs distributed, continue
		$distributed = true;
		continue;
	}elseif(count($instances) == $serverlimit){
		//throw a warning and continue
		echo "Hit max_instances, may skip any remaining pairs".PHP_EOL;
		continue;
	}elseif($lastassignedpairs == $assignedpairs){
		$error++;  //increment error count as no pair was assigned to an instance
		if($error > 50){
			//bail out
			die("Error distributing pairs among instances");
		}
	}
}
//finished distributing pairs/instances

if($globalsettings['server']['symlink_bots'] == 'user'){
	//extract gunbot
	$sourcepath = $basedir.'/gunbot_launcher/source/';
	//extract gunbot files
	if($unzipgb){
		if($debug)		echo "mkdir: " .$sourcepath.'/tulind/lib/binding/Release/node-v57-linux-x64/'.PHP_EOL;
		@		mkdir($sourcepath.'tulind/lib/binding/Release/node-v57-linux-x64/',0777,true);
		if($debug)		echo "mkdir: " .$sourcepath.'/node_modules/sqlite3/lib/binding/node-v57-linux-x64/'.PHP_EOL;
		@		mkdir($sourcepath.'/node_modules/sqlite3/lib/binding/node-v57-linux-x64/',0777,true);
		if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node" -d '.$sourcepath.'tulind/lib/binding/Release/node-v57-linux-x64'.PHP_EOL;
		exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node" -d '.$sourcepath.'tulind/lib/binding/Release/node-v57-linux-x64');
		if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/node_modules/sqlite3/lib/binding/node-v57-linux-x64/node_sqlite3.node" -d '.$sourcepath.'node_modules/sqlite3/lib/binding/node-v57-linux-x64'.PHP_EOL;
		exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/node_modules/sqlite3/lib/binding/node-v57-linux-x64/node_sqlite3.node" -d '.$sourcepath.'node_modules/sqlite3/lib/binding/node-v57-linux-x64');
		if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/node_sqlite3.node" -d '.$sourcepath.PHP_EOL;
		exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/node_sqlite3.node" -d '.$sourcepath);
		if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/gunthy-linx64" -d '.$sourcepath.PHP_EOL;
		exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/gunthy-linx64" -d '.$sourcepath);
		//sleep(2);
		if($debug)		echo "chmod +x :" .$sourcepath.'gunthy-linx64'.PHP_EOL;
		exec('chmod +x '.$sourcepath.'gunthy-linx64');
	}
}

foreach($instances as $instance){
	//set instance name
	$es = array_keys($instance);
	$e = $exchangecodes[$es[0]];
	$eps = array_keys($instance[$es[0]]);
	$pair = $eps[0];
	if(countsubkeys($instance)>1){
		$n = 'multi_'.$e.'_'.$pair;
	}else{
	//single pair
		$n = $e.'_'.$pair;
	}

	echo "Processing ".$n.PHP_EOL;
	$p = $basedir.'/gunbot_launcher/'.$n.'/';
	//extract/symlink
	//make folder structure
	if($createdirs){ mkdir($p,0777,true); }
	//symlinks!
	if($unzipgb){
		if($globalsettings['server']['symlink_bots'] === false){
			if($debug)		echo "mkdir: " .$p.'/tulind/lib/binding/Release/node-v57-linux-x64/'.PHP_EOL;
			@		mkdir($p.'tulind/lib/binding/Release/node-v57-linux-x64/',0777,true);
			if($debug)		echo "mkdir: " .$p.'/node_modules/sqlite3/lib/binding/node-v57-linux-x64/'.PHP_EOL;
			@		mkdir($p.'/node_modules/sqlite3/lib/binding/node-v57-linux-x64/',0777,true);
			if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node" -d '.$p.'tulind/lib/binding/Release/node-v57-linux-x64'.PHP_EOL;
			exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/tulind/lib/binding/Release/node-v57-linux-x64/tulind.node" -d '.$p.'tulind/lib/binding/Release/node-v57-linux-x64');
			if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/node_modules/sqlite3/lib/binding/node-v57-linux-x64/node_sqlite3.node" -d '.$p.'node_modules/sqlite3/lib/binding/node-v57-linux-x64'.PHP_EOL;
			exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/node_modules/sqlite3/lib/binding/node-v57-linux-x64/node_sqlite3.node" -d '.$p.'node_modules/sqlite3/lib/binding/node-v57-linux-x64');
			if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/node_sqlite3.node" -d '.$p.PHP_EOL;
			exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/node_sqlite3.node" -d '.$p);
			if($debug)		echo "exec: " .'unzip -j "'.$basedir.'/'.$gbzip.'" "lin/gunthy-linx64" -d '.$p.PHP_EOL;
			exec('unzip -o -qq -j "'.$basedir.'/'.$gbzip.'" "lin/gunthy-linx64" -d '.$p);
			//sleep(2);
			if($debug)		echo "chmod +x :" .$p.'gunthy-linx64'.PHP_EOL;
			exec('chmod +x '.$p.'gunthy-linx64');
		}elseif($globalsettings['server']['symlink_bots'] == "user"){
			symlink($sourcepath.'gunthy-linx64',$p.'gunthy-linx64');
			symlink($sourcepath.'node_sqlite3.node',$p.'node_sqlite3.node');
			symlink($sourcepath.'node_modules',$p.'node_modules');
			symlink($sourcepath.'tulind',$p.'tulind');
		}else{
			//global symlink or error
		}
	}
	//write config
	if($writeconfig){
		$myconfig = $globalsettings;
		foreach($instance as $exchange=>$pairs){
			foreach($pairs as $pair=>$opts){
				if(array_key_exists($opts['strategy'],$strategies) && array_key_exists('REQUIRES',$strategies[$opts['strategy']])){
					//custom strategy method
					if(!array_key_exists('pairs',$myconfig)){
						$myconfig['pairs'] = array();
					}
					if(!array_key_exists($exchange,$myconfig['pairs'])){
						$myconfig['pairs'][$exchange] = array();
					}
					$myconfig['pairs'][$exchange][$pair]=$opts;
					$s = array();
					foreach($strategies[$opts['strategy']] as $key=>$val){
						switch($key){
							case'REQUIRES':
								$t = $val;
								$s['NAME'] = $val;
								$myconfig['pairs'][$exchange][$pair]['strategy']=$t;
							break;
							default:
								$s[$key]=$val;
							break;
						}
					}
					$myconfig['strategies'][$t] = $s;
				}else{
					if(!array_key_exists('pairs',$myconfig)){
						$myconfig['pairs'] = array();
					}
					if(!array_key_exists($exchange,$myconfig['pairs'])){
						$myconfig['pairs'][$exchange] = array();
					}
					$myconfig['pairs'][$exchange][$pair]=$opts;
					$myconfig['strategies'][$opts['strategy']] = $strategies[$opts['strategy']];
				}
			}
		}
		$myconfig['ws']['port'] = $globalsettings['server']['ws_baseserverport']++;
		$myconfig['ws']['clientport'] = $globalsettings['server']['ws_baseclientport']++;
		$myconfig['ws']['hostname'] = $globalsettings['server']['ws_hostname'];
		unset($myconfig['server']);
		//write config
		file_put_contents($p.'config.js',json_encode($myconfig, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
		if(!$startbots)sleep($globalsettings['server']['start_delay']);
	}
	//write pm2 config
	if($writepm2){
		//write pm2 process file
$f = 'module.exports = {
  apps : [{
    name   : "'.$n.'",
    cwd: "'.$p .'",
    script: "gunthy-linx64",
    watch: false,
    args: "--color"
  }]
}';
		file_put_contents($p.$n.'.config.js',$f);
	}
	//start
	if($startbots){
		//launch bot
		if($debug) echo "exec: ". 'pm2 restart '.$p.$n.'.config.js';
		exec('pm2 start --no-autorestart '.$p.$n.'.config.js');
		sleep($globalsettings['server']['start_delay']);
	}
	if($stopbots){
		//launch bot
		if($debug) echo "exec: ". 'pm2 stop '.$n;
		exec('pm2 stop '.$n);
		if(!$delete)sleep($globalsettings['server']['start_delay']);
	}
	if($delete){
		exec('pm2 delete '.$n);
		exec('rm -rf '.$p);
	}
}

if($delete){
	exec('pm2 delete all');
	exec('rm -rf '.$basedir.'/gunbot_launcher/');
}





// Helper Functions


function load_server_settings($config){
	//define global defaults
	$serversettings = array(
	"instance_per_exchange" => true,
	"instance_per_pair" => false,
	"max_instances" => 64,
	"pairs_per_instance" => 10,
	"bot_delay" => 0,
	"start_delay" => 5,
	"ws_baseserverport" => 3000,
	"ws_baseclientport" => 5000,
	"ws_hostname" => "127.0.0.1",
	"gb_ver" => "latest",
	"symlink_bots" => "user",
	"validate_config" => true);

	$ds=$ps=$is=false;
	if(array_key_exists('servers',$config) && is_array($config['servers'])){
		if(array_key_exists('default',$config['servers'])){
			//load overridden defaults
			foreach($config['servers']['default'] as $k=>$v){
				if($k=="bot_delay"){$ds=true;}
				if($k=="pairs_per_instance"){$ps=true;}
				if($k=="instance_per_pair"){$is=true;}
				$serversettings[$k]=$v;
			}
		}
		if(array_key_exists(gethostname(),$config['servers'])){
			//load custom settings
			foreach($config['servers'][gethostname()] as $k=>$v){
				if($k=="bot_delay"){$ds=true;}
				if($k=="pairs_per_instance"){$ps=true;}
				if($k=="instance_per_pair"){$is=true;}
				$serversettings[$k]=$v;
			}
		}
	}
	if($ds||$ps||$is){
		//sanity check bot_delay
		if($serversettings['instance_per_pair'] || ($serversettings['pairs_per_instance']==1)){
			if($serversettings['bot_delay']<20){
				$serversettings['bot_delay']=20;
			}
		}
	}
	return $serversettings;
}

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


function countsubkeys($array,$depth=1){
	$count = 0;
	foreach($array as $key=>$arr){
		$count += count($arr);
	}
	return $count;
}
?>
