<?php

declare(strict_types = 1);

error_reporting(E_ALL);

include dirname(__DIR__) . "/vendor/autoload.php";

define("BASE_DIR", dirname(__DIR__));

// 程序配置
$config = [
    [
        "name" => "index",
        "command" => sprintf("php %s/script/default.php", BASE_DIR),
        "stop_signal" => SIGQUIT,
        "number" => 5,
        "priority" => 1,
        "auto_start" => true,
        "auto_restart" => true
    ]
];

// 实例化进程管理器
$monitor = \Plattry\Process\Monitor::getInstance();

// 注册程序
foreach ($config as $item) {
    $program = new \Plattry\Process\Program($item["name"], $item["command"]);
    $program->setStopSignal($item["stop_signal"]);
    $program->setNumber($item["number"]);
    $program->setPriority($item["priority"]);
    $program->setAutoStart($item["auto_start"]);
    $program->setAutoRestart($item["auto_restart"]);

    $monitor->setOpt($program);
}

// 启动进程管理
$monitor->run();
