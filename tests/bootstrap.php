<?php

use Illuminate\Foundation\Application;

use Illuminate\Support\ServiceProvider\ValidatorServiceProvider;

$loader = require 'vendor/autoload.php';

$loader->addPsr4('ModelForm\\Test\\', __DIR__);


