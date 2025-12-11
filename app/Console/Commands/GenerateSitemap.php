<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml';

    public function handle()
    {
        Sitemap::create(public_path('sitemap.xml'))
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(route('about'))->setPriority(0.8))
            ->add(Url::create(route('i-sys'))->setPriority(0.8)) // Internal System
            ->add(Url::create(route('join'))->setPriority(0.9))  // Join Us
            ->add(Url::create(route('teachers'))->setPriority(0.7))
            ->add(Url::create(route('subjects'))->setPriority(0.7))

            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }
}