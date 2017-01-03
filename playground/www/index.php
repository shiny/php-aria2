<?php
  require 'vendor/autoload.php';
  $aria2 = new Aria2('http://aria2:6800/jsonrpc');
  $status = $aria2->addUri(
      "token:e6c3778f-6361-4ed0-b126-f2cf8fca06db",
      ['https://www.docker.com/sites/default/files/moby.svg']
  );
?>
<code>
  <?=var_export($status, 1);?>
</code>