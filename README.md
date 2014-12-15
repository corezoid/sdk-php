# SDK-PHP

#Basic Usage

```
// API user settings
$api_login = '1234';
$api_secret = 'Iw5N12MYleOULxdfMH43SK9ZAmjPyFKtdhToiL38xIBz6ecdZxW';

// Init Middleware class
$MV = new Middleware($api_login, $api_secret);

// Add new task
$ref1  = time().'_'.rand();
$task1 = array('phone' => '+380989055434', 'nick' => 'Test Nick');
$MV->add_task($ref1, 1635, $task1);

// Send all tasks to Middleware
$res = $MV->send_tasks();
```