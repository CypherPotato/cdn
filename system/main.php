<?php
use Inphinit\Routing\Route;

Route::set('GET', '/{:.*:}', 'Home:fetch');