<?php
require_once __DIR__.'/config/config.php';
echo "<pre>";
echo "PHP ".PHP_VERSION."\n";
echo "Ext PDO: ".(extension_loaded('pdo')?'OK':'MISSING')."\n";
echo "Ext PDO MySQL: ".(extension_loaded('pdo_mysql')?'OK':'MISSING')."\n";
try{
  require_once __DIR__.'/core/Database.php';
  $pdo = Database::get()->pdo();
  echo "Conexión PDO: OK\n";
  $st=$pdo->query("SHOW TABLES");
  $tables=$st->fetchAll(PDO::FETCH_COLUMN);
  echo "Tablas (".count($tables)."):\n - ".implode("\n - ",$tables)."\n";
}catch(Throwable $e){
  echo "Error PDO: ".$e->getMessage()."\n";
}
echo "</pre>";