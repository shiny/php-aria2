php-aria2
=========
# Talk with aria2 using json-RPC

## Make sure aria2c is running
You can add this into /etc/rc.local
`/usr/local/bin/aria2c --enable-rpc --rpc-allow-origin-all -c -D`


> [Also See The Document of Aria2](https://aria2.github.io/manual/en/html/aria2c.html#rpc-interface)

## Usage
The php-aria2 is simple and just 45 lines.

Now it's on https://packagist.org/packages/daijie/aria2 

`composer require daijie/aria2`

> Thanks to [@Yuav](https://github.com/Yuav)

### Examples
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

Also See  [Manual of Aria2 RPC Interface](https://aria2.github.io/manual/en/html/aria2c.html#rpc-interface)

### Docker playground (require docker-compose)

#### init playground

```
git clone https://github.com/shiny/php-aria2/
cd php-aria2/playground
docker-compose up
docker-compose exec php composer require daijie/aria2
```

#### After that, the playground dir structure:

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

### Download File

	$aria2->addUri(
		['https://www.google.com.hk/images/srpr/logo3w.png'],
		['dir'=>'/tmp']
	);

[More Options Here](https://aria2.github.io/manual/en/html/aria2c.html#input-file)

### Returned Data
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
