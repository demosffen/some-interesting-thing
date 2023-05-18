<?php

final class AppController
{

    private const ADMIN_ID = '21232f297a57a5a743894a0e4a801fc3';
    private const USER_ID = 'ee11cbb19052e40b07aac0ca060c23ee';

    private static ?AppController $instance = null;

    public static function getInstance(): AppController
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function route()
    {
        $route = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $route = str_replace($route, '', $_SERVER['REDIRECT_URL']);
        if (empty(str_replace('/', '', $route))) {
            $this->redirect('list/');
            exit;
        }

        $this->authCheck();

        if (method_exists($this, $route)) {
            $this->$route();
        } else {
            $this->error404($route);
        }
    }

    private function authCheck()
    {
        session_start();
        if (empty($_SESSION)) {
            $_SESSION['userid'] = AppController::USER_ID;
        }
    }

    private function list()
    {
        (new TaskListController())->show();
    }
    private function add()
    {
        (new TaskListController())->add();
    }

    private function error404(string $route)
    {
        http_response_code(404);
    }

    public function buildUrl(array $params = [], array $values = [])
    {
        $get_copy = $_GET;
        $url = $_SERVER['REDIRECT_URL'];
        $queryStringsArray = [];
        foreach ($params as $key => $paramValue) {
            $get_copy[$paramValue] = $values[$key];
        }
        foreach ($get_copy as $key => $value) {
            $queryStringsArray[] = "{$key}={$value}";
        }
        return $url . '?' . implode("&", $queryStringsArray);
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    public function includeTemplate(array $data)
    {
        $data['auth_href'] = '/auth?backurl=' . base64_encode(AppController::getInstance()->buildUrl());
        $data['logout_href'] = '/logout?backurl=' . base64_encode(AppController::getInstance()->buildUrl());

        $data['is_admin'] = $this->userIsAdmin();

        $templatePath = realpath(__DIR__ . '/../view/header.php');
        include $templatePath;
    }

    private function auth()
    {
        $data['template'] = "authpage.php";
        $data['backurl'] = $_REQUEST['backurl'];
        unset($_POST['backurl']);
        $data['values'] = $_POST;
        $data['errors'] = $this->validateAuthFields($data['values']);
        if (empty($data['errors']) && !empty($data['values'])) {
            session_regenerate_id();
            $_SESSION['userid'] = AppController::ADMIN_ID;
            AppController::getInstance()->redirect(base64_decode($data['backurl']));
        } else {
            AppController::getInstance()->includeTemplate($data);
        }
    }

    private function logout()
    {

        session_regenerate_id();
        $_SESSION['userid'] = AppController::USER_ID;
        AppController::getInstance()->redirect(base64_decode($_GET['backurl']));
    }

    public function userIsAdmin()
    {

        return $_SESSION['userid'] == AppController::ADMIN_ID;
    }

    private function validateAuthFields(array $values): array
    {
        $errors = [];
        foreach ($values as $key => $value) {
            if (in_array($key, ['login', 'password'])) {
                if (empty(trim($value))) {
                    $errors[$key] = "Пустое значение недопустимо";
                }
            }
        }
        if (!empty($values) && ($values['login'] != 'admin') && ($values['password'] != '123')) {
            $errors['form'] = "Некорректные данные для входа";
        }
        return $errors;
    }

    private function mark()
    {
        if ($this->userIsAdmin()) {
            (new TaskListController)->changeStatus();
        } else {
            $this->redirect('/auth?backurl=' . $_GET['backurl']);
        }
    }
    private function edit()
    {
        if ($this->userIsAdmin()) {
            (new TaskListController)->edit();
        } else {
            $this->redirect('/auth?backurl=' . $_GET['backurl']);
        }
    }


}