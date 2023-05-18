<?

spl_autoload_register(static function ($class) {
    $path_model = realpath(__DIR__."/../beegeetestcode/model/{$class}.php");
    $path_controller = realpath(__DIR__."/../beegeetestcode/controller/{$class}.php");
    if (is_file($path_model)) {
        require($path_model);
    } elseif (is_file($path_controller)) {
        require($path_controller);
    }
});

AppController::getInstance()->route();
