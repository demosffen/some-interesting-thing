<?php

class Task
{
    public int $id;
    public string $userName;
    public string $userEmail;
    public string $text;
    public int $status;
    public int $edited;

    public static $connection;

    public static array $fieldTypesAllowedToSort = [
        'username',
        'useremail',
        'status'
    ];
    public static array $fieldTypesAllowedToSortDescription = [
        'По имени',
        'По email',
        'По статусу'
    ];
    private static string $queryGetTaskList = "SELECT id, username, useremail, text, status, edited FROM task ORDER BY !sf !so LIMIT 3 OFFSET !o;";
    private static string $queryGetTasksCount = "SELECT count(*) as count FROM task;";
    private static string $queryGetById = "SELECT id, username, useremail, text, status, edited FROM task WHERE id = ?;";
    private static string $queryInsertTask = "INSERT INTO task (username, useremail, text) VALUES (?, ?, ?);";
    private static string $queryUpdateTask = "UPDATE task SET username = ?, useremail = ?, text = ?, status = ?, edited = ? WHERE id = ?;";

    public function __construct(string $userName, string $userEmail, string $text, int $id = 0, int $status = 0, int $edited = 0)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->text = $text;
        $this->status = $status;
        $this->edited = $edited;
    }

    public static function getCount(): int
    {
        $statement = Task::$connection->prepare(Task::$queryGetTasksCount);
        $statement->execute();
        $result = $statement->get_result();
        $count = ($result->fetch_assoc())['count'];
        $result->free();
        return $count;
    }

    public static function getById(int $id): Task
    {
        $statement = Task::$connection->prepare(Task::$queryGetById);
        $statement->bind_param('i', $id);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        return Task::createByTableRow($row);
    }

    public static function getList(int $offset, string $sortField = "id", string $sortOrder = "desc"): array
    {
        $list = [];
        $sortField = Task::$connection->real_escape_string($sortField);
        $sortOrder = Task::$connection->real_escape_string($sortOrder);
        $preparedQueryString = preg_replace(['/!sf/', '/!so/', '/!o/'], [$sortField, $sortOrder, $offset], Task::$queryGetTaskList);
        $statement = Task::$connection->prepare($preparedQueryString);
        $statement->execute();
        $result = $statement->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = Task::createByTableRow($row);
        }
        $result->free();
        return $list;
    }


    private function insert(): self
    {
        $statement = Task::$connection->prepare(Task::$queryInsertTask);
        $statement->bind_param('sss', $this->userName, $this->userEmail, $this->text);
        $statement->execute();
        $this->id = $statement->insert_id;
        return $this;
    }

    private function update(): self
    {
        $statement = Task::$connection->prepare(Task::$queryUpdateTask);
        $statement->bind_param('sssiii', $this->userName, $this->userEmail, $this->text, $this->status, $this->edited, $this->id);
        $statement->execute();
        return $this;
    }

    public function save(): self
    {
        if ($this->id == 0) {
            $this->insert();
        } else {
            $this->update();
        }
        return $this;
    }

    private static function createByTableRow(array $row): Task
    {
        return new Task($row['username'], $row['useremail'], $row['text'], $row['id'], $row['status'], $row['edited']);
    }
}