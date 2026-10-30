<?php

// add unit testing specific bootstrap code here

// Test files are loaded before the Yii2 module creates the application, so app\ classes
// used at load time (base classes, constants in data providers) need the alias already
Yii::setAlias('@app', dirname(__DIR__, 2));
