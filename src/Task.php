<?php
    class Task
    {
        private $description;
        private $completed;
        private $due_date;
        private $id;


        function __construct($description, $completed, $due_date, $id = null)
        {
            $this->description = $description;
            $this->completed = $completed;
            $this->due_date = $due_date;
            $this->id = $id;
        }

        function setDescription($new_description)
        {
            $this->description = (string) $new_description;
        }

        function getDescription()
        {
            return $this->description;
        }

        function setCompleted($new_completed)
        {
            $this->completed = (boolean) $new_completed;
        }

        function getCompleted()
        {
            return $this->completed;
        }

        function getId()
        {
            return $this->id;
        }

        function setDueDate($new_due_date)
        {
            $this->due_date = $new_due_date;
        }
        function getDueDate()
        {
            return $this->due_date;
        }

        function save()
        {
              $GLOBALS['DB']->exec("INSERT INTO tasks (description, completed, due_date)  VALUES ('{$this->getDescription()}', {$this->getCompleted()}, '{$this->getDueDate()}');");
              $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks;");
            $tasks = array();
            foreach($returned_tasks as $task) {
                $description = $task['description'];
                $completed = $task['completed'];
                $due_date = $task['due_date'];
                $id = $task['id'];
                $new_task = new Task($description, $completed, $due_date, $id);
                array_push($tasks, $new_task);
            }
            return $tasks;
        }

        static function deleteAll()
        {
          $GLOBALS['DB']->exec("DELETE FROM tasks;");
        }

        static function find($search_id)
        {
            $found_task = null;
            $tasks = Task::getAll();
            foreach($tasks as $task) {
                $task_id = $task->getId();
                if ($task_id == $search_id) {
                  $found_task = $task;
                }
            }
            return $found_task;
        }

        function update($new_description, $new_completed, $new_due_date)
        {
            $GLOBALS['DB']->exec("UPDATE tasks SET description = '{$new_description}', completed = {$new_completed}, '{$new_due_date}' WHERE id = {$this->getId()};");
            $this->setDescription($new_description);
            $this->setCompleted($new_completed);
            $this->setDueDate($new_due_date);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
        }

        function addCategory($category)
        {
            $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$category->getId()}, {$this->getId()});");
        }

        function getCategories()
        {
            $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
            $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);

            $categories = array();
            foreach($category_ids as $id) {
                $category_id = $id['category_id'];
                $result = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");
                $returned_category = $result->fetchAll(PDO::FETCH_ASSOC);

                $name = $returned_category[0]['name'];
                $id = $returned_category[0]['id'];
                $new_category = new Category($name, $id);
                array_push($categories, $new_category);
            }
            return $categories;
        }


    }
?>
