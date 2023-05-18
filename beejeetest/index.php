<?

spl_autoload_register(static function ($class) {
    $path_model = realpath(__DIR__."/../beejeetestcode/model/{$class}.php");
    $path_controller = realpath(__DIR__."/../beejeetestcode/controller/{$class}.php");
    if (is_file($path_model)) {
        require($path_model);
    } elseif (is_file($path_controller)) {
        require($path_controller);
    }
});

AppController::getInstance()->route();
