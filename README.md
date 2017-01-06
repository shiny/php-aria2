
php-aria2
=========
Talking with [aria2](https://aria2.github.io/) through JSON-RPC

1. [Install](#install)
2. [Class Aria2](#class-aria2)
   1. [Usage](#usage)
   2. [Batch Requests](#batch-requests)
   3. [System Methods](#system-methods)
   4. [Example #1: Download File](#example-1-download-file)
   5. [Example #2: The Returned Data](#example-2-the-returned-data)
      1. [Can't Download](#case-cant-download)
      2. [Downloading (Active)](#case-downloading-active)
      3. [Downloaded](#case-downloaded)
3. [Docker Playground](#docker-playground)
4. [Updates](#updates)
5. [Contributors](#contributors)

## Install

### 1. [Install aria2c](https://aria2.github.io/)

Make sure aria2c is running and rpc is enabled, You can add this into /etc/rc.local
`/usr/local/bin/aria2c --enable-rpc --rpc-allow-origin-all -c -D`
> [Also See The Document of Aria2](https://aria2.github.io/manual/en/html/aria2c.html#rpc-interface)

### 2. Require Aria2.php

The codes just 82 lines but support all RPC methods. Using php's magic method `__call`

#### 2.1 Install by composer
`composer require daijie/aria2`

#### 2.2 Or copy Aria2.php

## Class Aria2

```php
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

```php
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
```

#### Also See  [Manual of Aria2 RPC Interface To Get The Method List](https://aria2.github.io/manual/en/html/aria2c.html#methods)

>  .i.e, It's the example from Aria2 manual wrote in Python:

```python
>>> import urllib2, json, base64
>>> metalink = base64.b64encode(open('file.meta4').read())
>>> jsonreq = json.dumps({'jsonrpc':'2.0', 'id':'qwer',
...                       'method':'aria2.addMetalink',
...                       'params':[metalink]})
>>> c = urllib2.urlopen('http://localhost:6800/jsonrpc', jsonreq)
>>> c.read()
'{"id":"qwer","jsonrpc":"2.0","result":["2089b05ecca3d829"]}'
```

If you are using php with php-aria2:

```php
<?php
require 'vendor/autoload.php';
$metalink = file_get_contents('file.meta4');
$aria2 = new Aria2('http://localhost:6800/jsonrpc');
$c = $aria2->addMetalink($metalink);
#It means the method is aria2.addMetalink
print_r($c); 
```


### Batch requests

 Now php-aria2 support [JSON-RPC 2.0 Specification Batch requests](https://aria2.github.io/manual/en/html/aria2c.html#system.multicall)
In v1.2.0 batch requests have been introduced.

- `Aria2::batch` - Start batch mode
- `Aria2::inBatch` - Detect batch mode
- `Aria2::commit` - End batch mode and commit commands

```php
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

```php
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

```php
$aria2 = new Aria2('http://127.0.0.1:6800/jsonrpc');
$aria2->system_listMethods();
$aria2->getGlobalStat();
```

### Example #1: Download File

```php
$aria2->addUri(
	['https://www.google.com.hk/images/srpr/logo3w.png'],
	['dir'=>'/tmp']
);
```

[More Options Here](https://aria2.github.io/manual/en/html/aria2c.html#input-file)

#### Example #2: The Returned Data
#### Case: `Can't Download`

```php
Array
(
    [id] => 1
    [jsonrpc] => 2.0
    [result] => Array
        (
            [completedLength] => 0
            [connections] => 0
            [dir] => /tmp
            [downloadSpeed] => 0
            [errorCode] => 1
            [files] => Array
                (
                    [0] => Array
                        (
                            [completedLength] => 0
                            [index] => 1
                            [length] => 0
                            [path] => 
                            [selected] => true
                            [uris] => Array
                                (
                                    [0] => Array
                                        (
                                            [status] => used
                                            [uri] => https://www.google.com.hk/images/srpr/logo3w.png
                                        )

                                )

                        )

                )
            [gid] => 2
            [numPieces] => 0
            [pieceLength] => 1048576
            [status] => error
            [totalLength] => 0
            [uploadLength] => 0
            [uploadSpeed] => 0
        )

)
```

#### Case: `Downloading (Active)`

```php
Array
(
    [id] => 1
    [jsonrpc] => 2.0
    [result] => Array
        (
            [bitfield] => e0000000
            [completedLength] => 3932160
            [connections] => 1
            [dir] => /data/files/lixian
            [downloadSpeed] => 75972
            [files] => Array
                (
                    [0] => Array
                        (
                            [completedLength] => 3145728
                            [index] => 1
                            [length] => 31550548
                            [path] => /data/files/lixian/茶经.陆羽.扫描版.pdf
                            [selected] => true
                            [uris] => Array
                                (
                                    [0] => Array
                                        (
                                            [status] => used
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [1] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [2] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [3] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [4] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                )

                        )

                )
            [gid] => 3
            [numPieces] => 31
            [pieceLength] => 1048576
            [status] => active
            [totalLength] => 31550548
            [uploadLength] => 0
            [uploadSpeed] => 0
        )

)
```

#### Case: `Downloaded`

```php
Array
(
    [id] => 1
    [jsonrpc] => 2.0
    [result] => Array
        (
            [bitfield] => fffffffe
            [completedLength] => 31550548
            [connections] => 0
            [dir] => /data/files/lixian
            [downloadSpeed] => 0
            [errorCode] => 0
            [files] => Array
                (
                    [0] => Array
                        (
                            [completedLength] => 31550548
                            [index] => 1
                            [length] => 31550548
                            [path] => /data/files/lixian/茶经.陆羽.扫描版.pdf
                            [selected] => true
                            [uris] => Array
                                (
                                    [0] => Array
                                        (
                                            [status] => used
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [1] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [2] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [3] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [4] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                    [5] => Array
                                        (
                                            [status] => waiting
                                            [uri] => http://gdl.lixian.vip.xunlei.com/download?fid=zKHWI/O2IbQ07pi/0hPYP1OLwrBUbOEBAAAAACaqKvQbmfR7K7JcbWGT3XQBlDzs&mid=666&threshold=150&tid=3018BA81C31480902DC937770AC2734F&srcid=4&verno=1&g=26AA2AF41B99F47B2BB25C6D6193DD7401943CEC&scn=c7&i=0D2B59F64D6CCBB5A1507A03C3B685BC&t=4&ui=222151634&ti=106821253185&s=31550548&m=0&n=013A830CE1AD5D2EC2DCE21471C9A8C3E8D1D7CA2F64660000&ff=0&co=33BB9833AB0EE7AAEA94105B64C8013F&cm=1
                                        )

                                )

                        )

                )
            [gid] => 3
            [numPieces] => 31
            [pieceLength] => 1048576
            [status] => complete
            [totalLength] => 31550548
            [uploadLength] => 0
            [uploadSpeed] => 0
        )

)
```

## Docker Playground
#### require [docker-compose](https://docs.docker.com/compose/install/)

Docker playground: nginx (17 MB) + php7-fpm (82 MB) + aria2c (6 MB)

#### init playground


```shell
git clone https://github.com/shiny/php-aria2/
cd php-aria2/playground
docker-compose up
```

then open another terminal and enter playground

```shell
docker-compose exec php composer require daijie/aria2
```

for China user we suggest use the phpcomposer mirror


```shell
docker-compose exec php composer config repo.packagist composer https://packagist.phpcomposer.com
docker-compose exec php composer require daijie/aria2
```



#### After that, the playground structure:

```shell
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
Edit www/index.php and Open Browser To Visit http://127.0.0.1:8080

## Updates

### v1.2.1b
- add batch anonymous function

### v1.2.0b

- Add system methods
- Add batch mode

### v1.1
Now support default token(secret) in php-aria2, compatible with v1.0

Before

```php
$aria2 = new Aria2('http://aria2:6800/jsonrpc');
$aria2->addUri(
    "token:e6c3778f-6361-4ed0-b126-f2cf8fca06db",
    ['https://www.docker.com/sites/default/files/moby.svg']
);
$aria2->getGlobalStat("token:e6c3778f-6361-4ed0-b126-f2cf8fca06db");
```

After

```php
$aria2 = new Aria2('http://aria2:6800/jsonrpc', "token:e6c3778f-6361-4ed0-b126-f2cf8fca06db");
$aria2->addUri(
    ['https://www.docker.com/sites/default/files/moby.svg']
);
$status = $aria2->getGlobalStat();
```

## Contributors
- Dai Jie <daijie@php.net>
- [Jon Skarpeteig](https://github.com/Yuav)