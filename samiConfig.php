<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir = __DIR__.DIRECTORY_SEPARATOR.'src');

$versions = GitVersionCollection::create($dir)
    ->addFromTags('v*')
    ->add('master', 'master branch');

return new Sami($dir, array(
    //'theme'                => 'symfony',
    'versions'             => $versions,
    'title'                => 'Amber/Collection API',
    'build_dir'            => __DIR__.'/tmp/build/%version%',
    'cache_dir'            => __DIR__.'/tmp/cache/sami/%version%',
    'remote_repository'    => new GitHubRemoteRepository('systemson/collection', dirname($dir)),
    'default_opened_level' => 10,
));
