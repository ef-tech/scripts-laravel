<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ExtractHeadings extends Command
{
    protected $signature = 'extract:headings {url}';

    protected $description = 'Extract headings from a URL';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = $this->argument('url');

        $client = new Client();
        $response = $client->get($url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $headings = $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function (Crawler $node, $i) {
            $indent = $this->convertToIndent($node->nodeName());

            return Str::repeat("\t", $indent).$node->text();
        });

        $this->info('Headings:');
        foreach ($headings as $heading) {
            $this->line($heading);
        }
    }

    private function convertToIndent($nodeName): int
    {
        return ((int) Str::remove('h', $nodeName)) - 1;
    }
}
