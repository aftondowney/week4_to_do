<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Task.php";
    require_once "src/Category.php";

    $server = 'mysql:host=localhost;dbname=to_do_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class TaskTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Task::deleteAll();
            Category::deleteAll();
        }

        function testGetDescription()
        {
            //Arrange
            $description = "Do dishes.";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date);

            //Act
            $result = $test_task->getDescription();

            //Assert
            $this->assertEquals($description, $result);
        }

        function testSetDescription()
        {
            //Arrange
            $description = "Do dishes.";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date);

            //Act
            $test_task->setDescription("Drink coffee.");
            $result = $test_task->getDescription();

            //Assert
            $this->assertEquals("Drink coffee.", $result);
        }

        function testGetId()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);

            //Act
            $result = $test_task->getId();

            //Assert
            $this->assertEquals(1, $result);
        }

        function testSave()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);

            //Act
            $test_task->save();

            //Assert
            $result = Task::getAll();
            $this->assertEquals($test_task, $result[0]);
        }

        function testSaveSetsId()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);

            //Act
            $test_task->save();

            //Assert
            $this->assertEquals(true, is_numeric($test_task->getId()));
        }

        function testGetAll()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();


            $description2 = "Water the lawn";
            $completed = true;
            $id2 = 2;
            $due_date2 = "2016-03-03";
            $test_task2 = new Task($description2, $completed, $due_date2, $id2);
            $test_task2->save();

            //Act
            $result = Task::getAll();

            //Assert
            $this->assertEquals([$test_task, $test_task2], $result);
        }

        function testDeleteAll()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();

            $description2 = "Water the lawn";
            $completed = true;
            $due_date2 = "2016-03-05";
            $id2 = 2;
            $test_task2 = new Task($description2, $completed, $id2, $due_date2);
            $test_task2->save();

            //Act
            Task::deleteAll();

            //Assert
            $result = Task::getAll();
            $this->assertEquals([], $result);
        }

        function testFind()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();

            $description2 = "Water the lawn";
            $completed = true;
            $due_date2 = "2016-03-05";
            $id2 = 2;
            $test_task2 = new Task($description2, $completed, $due_date2, $id2 );
            $test_task2->save();

            //Act
            $result = Task::find($test_task->getId());

            //Assert
            $this->assertEquals($test_task, $result);
        }

        function testUpdate()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();

            $new_description = "Clean the dog";

            //Act
            $test_task->update($new_description);

            //Assert
            $this->assertEquals("Clean the dog", $test_task->getDescription());
        }

        function testDeleteTask()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();

            $description2 = "Water the lawn";
            $completed = true;
            $id2 = 2;
            $due_date2 = "2016-03-09";
            $test_task2 = new Task($description2, $completed, $due_date2, $id2);
            $test_task2->save();


            //Act
            $test_task->delete();

            //Assert
            $this->assertEquals([$test_task2], Task::getAll());
        }

        function testDelete()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "File reports";
            $id2 = 2;
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id2);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);
            $test_task->delete();

            //Assert
            $this->assertEquals([], $test_category->getTasks());
        }

        function testAddCategory()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "File reports";
            $id2 = 2;
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category]);
        }

        function testGetCategories()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $name2 = "Volunteer stuff";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();

            $description = "File reports";
            $id2 = 2;
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id2);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);
            $test_task->addCategory($test_category2);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category, $test_category2]);
        }

        function testsetCompleted()
        {
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "File reports";
            $completed = true;
            $id2 = 2;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id2);
            $test_task->save();

            $test_task->setCompleted(false);
            $result = $test_task->getCompleted();

            $this->assertEquals(false, $result);
        }

        function testGetCompleted()
        {
            //Arrange
            $name = "Work stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "File reports";
            $completed = true;
            $id2 = 2;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id2);
            $test_task->save();

            //Act
            $result = $test_task->getCompleted();

            //Assert
            $this->assertEquals($completed, $result);
        }

        function testfindCompleted()
        {
            //Arrange
            $id = 1;
            $description = "Wash the dog";
            $completed = true;
            $due_date = "2016-02-23";
            $test_task = new Task($description, $completed, $due_date, $id);
            $test_task->save();


            $description2 = "Water the lawn";
            $completed2 = false;
            $id2 = 2;
            $due_date2 = "2016-03-09";
            $test_task2 = new Task($description2, $completed2, $due_date2, $id2);
            $test_task2->save();

            //Act
            $result = Task::findCompleted($test_task->getCompleted());

            //Assert
            $this->assertEquals([$test_task], $result);
        }


    }
?>
