<?php

class TaskListController
{
    public function __construct()
    {
        Task::$connection = new mysqli("localhost", "root", "", "beegeetest");
    }

    public function show()
    {
        $data['template'] = "tasklist.php";

        if (isset($_GET['page'])) {
            $page = $_GET['page'] - 1;
        } else {
            $page = 0;
        }
        $offset = $page * 3;

        if (in_array($_GET['sort'], Task::$fieldTypesAllowedToSort)) {
            $sort = $_GET['sort'];
        } else {
            $sort = 'id';
        }
        if (in_array($_GET['order'], ['asc', 'desc'])) {
            $order = $_GET['order'];
        } else {
            $order = 'desc';
        }

        foreach (Task::$fieldTypesAllowedToSort as $key => $value) {
            $data['sort_types'][] = [
                'name' => Task::$fieldTypesAllowedToSortDescription[$key] . ' ↑',
                'href' => AppController::getInstance()->buildUrl(['sort', 'order'], [$value, 'asc'])
            ];
            $data['sort_types'][] = [
                'name' => Task::$fieldTypesAllowedToSortDescription[$key] . ' ↓',
                'href' => AppController::getInstance()->buildUrl(['sort', 'order'], [$value, 'desc'])
            ];
        }

        $data['task_list'] = Task::getList($offset, $sort, $order);

        foreach ($data['task_list'] as $key => $value) {
            if (AppController::getInstance()->userIsAdmin()) {
                $data['task_links'][$key] = $this->generateAdminTaskLinks($value);
            }
            ($data['task_list'][$key])->userName = htmlspecialchars($value->userName);
            ($data['task_list'][$key])->userEmail = htmlspecialchars($value->userEmail);
            ($data['task_list'][$key])->text = htmlspecialchars($value->text);
        }


        $data['add_href'] = "/add?backurl=" . base64_encode(AppController::getInstance()->buildUrl());

        $count = Task::getCount();
        $pages_count = intdiv($count, 3) + (($count % 3 == 0) ? 0 : 1);
        for ($i = 1; $i <= $pages_count; $i++) {
            $data['pages'][] = ['name' => $i, 'href' => AppController::getInstance()->buildUrl(['page'], [$i])];
        }

        AppController::getInstance()->includeTemplate($data);
    }

    public function add()
    {
        $data['backurl'] = $_REQUEST['backurl'];
        $data['template'] = "taskadd.php";
        $data['form_action'] = "/add";
        $data['values'] = $_POST;
        $data['errors'] = $this->validateFields($data['values']);
        if (empty($data['errors']) && !empty($data['values'])) {
            $task = new Task($_POST['username'], $_POST['useremail'], $_POST['text']);
            $task->save();
            AppController::getInstance()->redirect(base64_decode($data['backurl']));
        } else {
            AppController::getInstance()->includeTemplate($data);
        }
    }

    private function validateFields(array $values): array
    {
        $errors = [];
        foreach ($values as $key => $value) {
            if (in_array($key, ['usernamme', 'useremail', 'text'])) {
                $values[$key] = $value;
                if (empty(trim($value))) {
                    $errors[$key] = 'Пустое значение недопустимо';
                } else if (($key == 'useremail') && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$key] = 'Некорректный email';
                }
            }
        }
        return $errors;
    }

    public static function generateAdminTaskLinks(Task $task): array
    {
        $newStatus = ($task->status == 0) ? 1 : 0;
        return [
            'edit' => "/edit?id={$task->id}&backurl=" . base64_encode(AppController::getInstance()->buildUrl()),
            'mark' => "/mark?id={$task->id}&status={$newStatus}&backurl=" . base64_encode(AppController::getInstance()->buildUrl())
        ];
    }

    public function changeStatus()
    {
        $task_id = intval($_GET['id']);
        $task = Task::getById($task_id);
        $task->status = intval($_GET['status']);
        $task->save();
        AppController::getInstance()->redirect(base64_decode($_GET['backurl']));
    }

    public function edit()
    {
        $data['backurl'] = $_REQUEST['backurl'];
        $data['template'] = "taskadd.php";
        $data['form_action'] = "/edit";
        $data['edit_mode'] = true;
        $task = Task::getById(intval($_REQUEST['id']));
        if (empty($_POST)) {
            $data['values'] = [
                'id' => $task->id,
                'username' => $task->userName,
                'useremail' => $task->userEmail,
                'text' => $task->text
            ];
        } else {
            $data['values'] = $_POST;
        }
        $data['errors'] = $this->validateFields($data['values']);
        if (empty($data['errors']) && !empty($_POST)) {
            $task->text = $data['values']['text'];
            $task->edited = 1;
            $task->save();
            AppController::getInstance()->redirect(base64_decode($data['backurl']));
        } else {
            AppController::getInstance()->includeTemplate($data);
        }
    }
}