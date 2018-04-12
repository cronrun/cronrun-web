<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Swoole\Server;

class CronRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:run
    {-t|test=false :debug mode}
    {-s|signal=start :send signal to cronrun server: start, stop, restart}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CronRun server';

    protected $clients = [];

    /**
     * @var \Swoole\Server
     */
    protected $server;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = new Server('0.0.0.0', 8192, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $server->set(['task_worker_num' => 1]);
        $this->tcpServer($server);
        $this->manageServer($server);
        $this->server = $server;
        $server->start();
        echo "exit" . PHP_EOL;
    }

    /**
     * @param \Swoole\Server $server
     */
    protected function tcpServer(Server $server)
    {
        $server->on("connect", function (Server $server, $fd) {
            echo "connected" . PHP_EOL;
            $this->clients[$fd] = [];
        });
        $server->on("receive", function (Server $server, $fd, $from_id, $data) {
            echo "{$fd} received: {$data}" . PHP_EOL;
            $message = json_decode($data);
            if (!isset($message->action)) {
                echo $data . PHP_EOL;
                return;
            }
            switch ($message->action) {
                case 'register':
                    $this->clients[$fd]             = [
                        'token'      => $message->token,
                        'connection' => $server
                    ];
                    $this->clients[$message->token] = [
                        'fd'         => $fd,
                        'connection' => $server
                    ];
                    $server->task([
                        'fd'    => $fd,
                        'token' => $message->token,
                    ]);
                    break;

                case 'log':
                    $this->saveLog($server, $fd, $data);
                    break;

                case 'alive':
                    $this->heartBeat($server, $fd);
                    break;

                default:
                    return;
            }
        });
        $server->on('task', function (Server $server, $task_id, $from_id, $task) {
            $this->registerServer($server, $task);
        });
        $server->on('finish', function (Server $server, $fd, $from_id) {

        });
        $server->on('close', function (Server $server, $fd) {
            echo "{$fd} closed" . PHP_EOL;
            $token = $this->clients[$fd]['token'];
            unset($this->clients[$fd]);
            unset($this->clients[$token]);
        });
    }

    protected function heartBeat(Server $server, $fd)
    {
        $server->send($fd, json_encode(['action' => 'alive', 'status' => 'got']));
        echo "{$fd} is alive" . PHP_EOL;
    }

    /**
     * @param \Swoole\Server $server
     * @param                $task
     */
    protected function registerServer(Server $server, $task)
    {
        while (true) {
            if (file_exists('/tmp/command.txt')) {
                $command = trim(file_get_contents('/tmp/command.txt'));
                if ($command) {
                    $response = json_encode([
                        'action'  => 'command',
                        'command' => $command
                    ]);
                    dump($this->server->connection_info($task['fd']));
                    $this->server->send($task['fd'], $response);
                    echo "sending command {$response}" . PHP_EOL;
                } else {
                    echo "no command" . PHP_EOL;
                }
            }
            sleep(1);
        }
    }

    protected function saveLog(Server $server, $fd, $message)
    {
        echo "client command log: {$message}" . PHP_EOL;
    }

    /**
     * @param \Swoole\Server $server
     */
    protected function manageServer(Server $server)
    {
        $manageServer = $server->listen('0.0.0.0', 16384, SWOOLE_SOCK_TCP);
        $manageServer->on('receive', function (Server $server, $fd, $from_id, $data) {

        });
    }
}
