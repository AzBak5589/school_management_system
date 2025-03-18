<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\PackageManifest;

class PackageDiscover extends Command
{
    protected $signature = 'package:discover';
    
    protected $description = 'Rebuild the cached package manifest';
    
    public function handle(PackageManifest $manifest): void
    {
        $manifest->build();
        
        $this->info('Package manifest generated successfully.');
    }
}