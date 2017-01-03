php-aria2
=========
# Talking with [aria2](https://aria2.github.io/) through JSON-RPC

## Install

### 1. [Install aria2c](https://aria2.github.io/)

Make sure aria2c is running and rpc is enabled, You can add this into /etc/rc.local
`/usr/local/bin/aria2c --enable-rpc --rpc-allow-origin-all -c -D`
> [Also See The Document of Aria2](https://aria2.github.io/manual/en/html/aria2c.html#rpc-interface)

### 2. Require Aria2.php

The codes just 82 lines but support all RPC methods. Using php's magic method `__call`

#### 2.1 Install by composer
`composer require daijie/aria2`

> Thanks to [@Yuav](https://github.com/Yuav)

#### 2.2 Or copy Aria2.php

## Docker playground
#### require [docker-compose](https://docs.docker.com/compose/install/)

Docker playground: nginx (17 MB) + php7-fpm (82 MB) + aria2c (6 MB)

#### init playground


    git clone https://github.com/shiny/php-aria2/
    cd php-aria2/playground
    docker-compose up

then open another terminal and enter playground

    docker-compose exec php composer require daijie/aria2

for China user we suggest use the phpcomposer mirror


    docker-compose exec php composer config repo.packagist composer https://packagist.phpcomposer.com
    docker-compose exec php composer require daijie/aria2



#### After that, the playground structure:

```
├── aria2.conf # Aria2 conf file
├── data # Store downloaded file
├── docker-compose.yml
├── nginx.conf # nginx conf
└── www # Web dir
    ├── composer.json
    ├── composer.lock
    ├── index.php
    └── vendor
        ├── autoload.php
        ├── composer
        │   ├── ClassLoader.php
        │   ├── LICENSE
        │   ├── autoload_classmap.php
        │   ├── autoload_namespaces.php
        │   ├── autoload_psr4.php
        │   ├── autoload_real.php
        │   ├── autoload_static.php
        │   └── installed.json
        └── daijie
            └── aria2
                ├── Aria2.php
                ├── LICENSE.txt
                ├── README.md
                └── composer.json
```
Edit www/index.php and Open Browser To Visit http://127.0.0.1

## Class Aria2

```
Aria2 {
    __construct ( string $server [, string $token ] )
    __destruct ( void )
    __call(string $name, array $arg)
    public Object batch( [Callable $func ] )
    public bool inBatch( void )
    public array commit( void )
    protected string req ( array $data )
}
```

## Usage

	$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc'); 
    // http://127.0.0.1:6800/jsonrpc is the default value, 
    // equals to $aria2 = new Aria2
	$aria2->getGlobalStat();
	$aria2->tellActive();
	$aria2->tellWaiting(0,1000);
	$aria2->tellStopped(0,1000);
	$aria2->addUri(
		['https://www.google.com.hk/images/srpr/logo3w.png'],
		['dir'=>'/tmp']
	);
	$aria2->tellStatus('1');
	$aria2->removeDownloadResult('1');
	//and more ...

Also See  [Manual of Aria2 RPC Interface To Get The Method List](https://aria2.github.io/manual/en/html/aria2c.html#rpc-interface)

### Batch requests
 Now php-aria2 support [JSON-RPC 2.0 Specification Batch requests](https://aria2.github.io/manual/en/html/aria2c.html#system.multicall)
In v1.2.0 batch requests have been introduced.

- `Aria2::batch` - Start batch mode
- `Aria2::inBatch` - Detect batch mode
- `Aria2::commit` - End batch mode and commit commands

```
$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');
$aria2->batch()
      ->getGlobalStat()
      ->tellActive()
      ->tellWaiting(0,1000)
      ->tellStopped(0,1000)
      ->addUri(
			['https://www.google.com.hk/images/srpr/logo3w.png'],
			['dir'=>'/tmp']
		)
		->commit();
```	
Another ways is anonymous function, it also support method chaining. Don't forget commit.

```
    $aria2 = new Aria2('http://aria2:6800/jsonrpc', "token:e6c3778f-6361-4ed0-b126-f2cf8fca06db");
	$aria2->batch(function($aria2){
    	$aria2->getGlobalStat();
    	$aria2->system_listMethods();
	});
	$status = $aria2->commit();
```

### System methods

- system.multicall
- system.listMethods
- system.listNotifications

There are some system methods, you can call it using

- Aria2::system_multicall
- Aria2::system_listMethods
- Aria2::system_listNotifications

php-aria2 convert `_` to `.` automatically. If method name without a `_`,  php-aria2 will auto prepend a `aria2.`

```
$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');
$aria2->system_listMethods();
$aria2->getGlobalStat();
```


## Updates

### v1.2.0b

- Add system methods
- Add batch mode

### v1.1
Now support default token(secret) in php-aria2, compatible with v1.0

Before

```
  $aria2 = new Aria2('http://aria2:6800/jsonrpc');
  $aria2->addUri(
  		"token:e6c3778f-6361-4ed0-b126-f2cf8fca06db",
      ['https://www.docker.com/sites/default/files/moby.svg']
  );
  $aria2->getGlobalStat("token:e6c3778f-6361-4ed0-b126-f2cf8fca06db");
```

After

```
  $aria2 = new Aria2('http://aria2:6800/jsonrpc', "token:e6c3778f-6361-4ed0-b126-f2cf8fca06db");
  $aria2->addUri(
      ['https://www.docker.com/sites/default/files/moby.svg']
  );
  $status = $aria2->getGlobalStat();
```

### Example #1: Download File

	$aria2->addUri(
		['https://www.google.com.hk/images/srpr/logo3w.png'],
		['dir'=>'/tmp']
	);

[More Options Here](https://aria2.github.io/manual/en/html/aria2c.html#input-file)

#### Example #2: The Returned Data
#### Case: `Can't Download`

	array(3) {
	  ["id"]=>
	  string(1) "1"
	  ["jsonrpc"]=>
	  string(3) "2.0"
	  ["result"]=>
	  array(13) {
	    ["completedLength"]=>
	    string(1) "0"
	    ["connections"]=>
	    string(1) "0"
	    ["dir"]=>
	    string(4) "/tmp"
	    ["downloadSpeed"]=>
	    string(1) "0"
	    ["errorCode"]=>
	    string(1) "1"
	    ["files"]=>
	    array(1) {
	      [0]=>
	      array(6) {
	        ["completedLength"]=>
	        string(1) "0"
	        ["index"]=>
	        string(1) "1"
	        ["length"]=>
	        string(1) "0"
	        ["path"]=>
	        string(0) ""
	        ["selected"]=>
	        string(4) "true"
	        ["uris"]=>
	        array(1) {
	          [0]=>
	          array(2) {
	            ["status"]=>
	            string(4) "used"
	            ["uri"]=>
	            string(48) "https://www.google.com.hk/images/srpr/logo3w.png"
	          }
	        }
	      }
	    }
	    ["gid"]=>
	    string(1) "2"
	    ["numPieces"]=>
	    string(1) "0"
	    ["pieceLength"]=>
	    string(7) "1048576"
	    ["status"]=>
	    string(5) "error"
	    ["totalLength"]=>
	    string(1) "0"
	    ["uploadLength"]=>
	    string(1) "0"
	    ["uploadSpeed"]=>
	    string(1) "0"
	  }
	}

#### Case: `Downloading (Active)`

	array(3) {
	  ["id"]=>
	  string(1) "1"
	  ["jsonrpc"]=>
	  string(3) "2.0"
	  ["result"]=>
	  array(13) {
	    ["bitfield"]=>
	    string(8) "e0000000"
	    ["completedLength"]=>
	    string(7) "3932160"
	    ["connections"]=>
	    string(1) "1"
	    ["dir"]=>
	    string(18) "/data/files/lixian"
	    ["downloadSpeed"]=>
	    string(5) "75972"
	    ["files"]=>
	    array(1) {
	      [0]=>
	      array(6) {
	        ["completedLength"]=>
	        string(7) "3145728"
	        ["index"]=>
	        string(1) "1"
	        ["length"]=>
	        string(8) "31550548"
	        ["path"]=>
	        string(48) "/data/files/lixian/[茶经].陆羽.扫描版.pdf"
	        ["selected"]=>
	        string(4) "true"
	        ["uris"]=>
	        array(5) {
	          [0]=>
	          array(2) {
	            ["status"]=>
	            string(4) "used"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [1]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [2]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [3]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [4]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	        }
	      }
	    }
	    ["gid"]=>
	    string(1) "3"
	    ["numPieces"]=>
	    string(2) "31"
	    ["pieceLength"]=>
	    string(7) "1048576"
	    ["status"]=>
	    string(6) "active"
	    ["totalLength"]=>
	    string(8) "31550548"
	    ["uploadLength"]=>
	    string(1) "0"
	    ["uploadSpeed"]=>
	    string(1) "0"
	  }
	}

#### Case: `Downloaded`
	
	array(3) {
	  ["id"]=>
	  string(1) "1"
	  ["jsonrpc"]=>
	  string(3) "2.0"
	  ["result"]=>
	  array(14) {
	    ["bitfield"]=>
	    string(8) "fffffffe"
	    ["completedLength"]=>
	    string(8) "31550548"
	    ["connections"]=>
	    string(1) "0"
	    ["dir"]=>
	    string(18) "/data/files/lixian"
	    ["downloadSpeed"]=>
	    string(1) "0"
	    ["errorCode"]=>
	    string(1) "0"
	    ["files"]=>
	    array(1) {
	      [0]=>
	      array(6) {
	        ["completedLength"]=>
	        string(8) "31550548"
	        ["index"]=>
	        string(1) "1"
	        ["length"]=>
	        string(8) "31550548"
	        ["path"]=>
	        string(48) "/data/files/lixian/[茶经].陆羽.扫描版.pdf"
	        ["selected"]=>
	        string(4) "true"
	        ["uris"]=>
	        array(6) {
	          [0]=>
	          array(2) {
	            ["status"]=>
	            string(4) "used"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [1]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [2]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [3]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [4]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	          [5]=>
	          array(2) {
	            ["status"]=>
	            string(7) "waiting"
	            ["uri"]=>
	            string(417) "http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1"
	          }
	        }
	      }
	    }
	    ["gid"]=>
	    string(1) "3"
	    ["numPieces"]=>
	    string(2) "31"
	    ["pieceLength"]=>
	    string(7) "1048576"
	    ["status"]=>
	    string(8) "complete"
	    ["totalLength"]=>
	    string(8) "31550548"
	    ["uploadLength"]=>
	    string(1) "0"
	    ["uploadSpeed"]=>
	    string(1) "0"
	  }
	}

## Credits
Dai Jie <daijie@php.net>