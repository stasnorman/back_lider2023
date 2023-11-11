<?php
    require_once "function_responce.php";

    function updateTask($connect, $taskId, $dataUser) {
        $taskId = (int)$taskId; // Приводим к целому числу для безопасности
        $headTitle = mysqli_real_escape_string($connect, $dataUser['title']);
        $unixDate = strtotime($dataUser['date']);
        $grade = $dataUser['levworker'];
        $updateGrade = "";
        switch ($grade) {
            case 'Синьор':
                $updateGrade = 1;
                break;
            case 'Мидл':
                $updateGrade = 2;
                break;
            case 'Джун': 
                $updateGrade = 3;
                break;
            default:
                // Обработка ошибки, если grade не существует
                http_response_code(400);
                echo json_encode(["status" => false, "description" => "Уровень работника не определен."]);
                return;
        }

    
        // Обновляем данные только если такой id существует
        $query = "UPDATE `task` SET `title` = '$headTitle', `date_start_event` = $unixDate, `id_grade` = $updateGrade WHERE `id` = $taskId";
        
        if (mysqli_query($connect, $query)) {
            if (mysqli_affected_rows($connect) > 0) {
                http_response_code(200);
                $response = [
                    "status" => true,
                    "description" => "Информация успешно обновлена."
                ];
            } else {
                // Нет изменений в базе данных, возможно, id не существует
                http_response_code(404);
                $response = [
                    "status" => false,
                    "description" => "Задача с указанным ID не найдена или данные не изменены."
                ];
            }
        } else {
            // Ошибка выполнения запроса
            http_response_code(500);
            $response = [
                "status" => false,
                "description" => "Ошибка при обновлении записи: " . mysqli_error($connect)
            ];
        }
        
        echo json_encode($response);
    }
    
    function updateSubTask($connect, $subTaskId, $dataUser) {
        $id = $subTaskId;
        $title = $dataUser['title'];
        $dateString = $dataUser['date'];
        $dateUnix = strtotime($dateString);
    
        // Use placeholders for the values to be updated
        $query = "UPDATE `sub_task` SET `title` = ?, `date` = ? WHERE `id` = ?";
    
        // Prepare the SQL statement
        if ($stmt = mysqli_prepare($connect, $query)) {
            // Bind the variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sii", $title, $dateUnix, $id);
    
            // Execute the prepared statement
            $success = mysqli_stmt_execute($stmt);
    
            if ($success) {
                http_response_code(200);
                echo json_encode(
                    [
                        "status" => true, 
                        "description" => "Подзадача успешно обновлена!"
                    ]
                );
            } else {
                http_response_code(400);
                echo json_encode(
                    [
                        "status" => false, 
                        "description" => "Ошибка при обновлении подзадачи."
                    ]
                );
            }
    
            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // If the preparation fails, return a server error
            http_response_code(500);
            echo json_encode(["status" => false, "description" => "Ошибка запроса."]);
        }
    }
    
?>