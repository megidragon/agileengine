<?php

namespace App\Console\Commands;

use App\Models\Images;
use Illuminate\Console\Command;
use ImporterModule\Helpers\AgileServer;

class ImporterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importer:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download images information and cache it in the server';

    /**
     * @var AgileServer
     */
    private $agileServer;
    private $page = 1;
    private $totalPages;

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
     * @return int
     */
    public function handle()
    {
        $this->agileServer = new AgileServer;

        do {
            if (!$this->import())
            {
                break;
            }

        } while ($this->page <= $this->totalPages);

        return true;
    }

    private function import()
    {
        $response = $this->agileServer->listEndpoint($this->page);

        if (empty($response['pictures']))
        {
            return false;
        }

        $this->totalPages = $this->totalPages ?: $response['pageCount'];

        $this->fetchData($response['pictures']);

        $this->page += 1;

        return $response['hasMore'];
    }

    private function fetchData($pictures)
    {
        foreach ($pictures as $picture)
        {
            $row = $this->agileServer->showEndpoint($picture['id']);
            $data = [
                'remote_id' => $row['id'],
                'author' => $this->ifNotEmpty($row, 'author'),
                'camera' => $this->ifNotEmpty($row, 'camera'),
                'tags' => $this->ifNotEmpty($row, 'tags'),
                'cropped_picture' => $this->ifNotEmpty($row, 'cropped_picture'),
                'full_picture' => $this->ifNotEmpty($row, 'full_picture'),
            ];

            //TODO: There's a lot of better ways to take this part but no time for improve here
            Images::updateOrCreate(
                ['remote_id' => $row['id']],
                $data
            );
        }
    }

    private function ifNotEmpty($arr, $var)
    {
        return isset($arr[$var]) ? $arr[$var] : null;
    }
}
