<?php 
    require_once "function_responce.php";

    //
    // Авторизация пользователя
    //
    function authUser($connect, $dataUser){

        $getLogin = $dataUser['login'];
        $getPassword = $dataUser['password'];
        $loginCheck = mysqli_query($connect, "
        SELECT 
            u.*,
            r.name AS role_name,
            lw.name AS level_worker_name
        FROM 
            `user` u
            INNER JOIN role r ON u.id_role = r.id
            INNER JOIN level_worker lw ON u.id_level_worker = lw.id 
        WHERE `login`='".$getLogin."' AND `password`='".$getPassword."'");
        $responce = mysqli_fetch_assoc($loginCheck); 
        
        if ($responce == null) {
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Авторизация не получилась.",
                "link" => "https://easy4.team/"
            ];
            
            echo json_encode($responce);
        } else {
            
            http_response_code(200);
            $responce = [
                "status" => true,
                "description" => "Вы вошли в свой аккаунт.",
                "personal-data-user" => array(
                    "id" => $responce["id"],
                    "name" => $responce["name"],
                    "role" => $responce["role_name"],
                    "grade" => $responce["level_worker_name"]   
                ),
                "link" => "https://easy4.team/"
            ];
            
            echo json_encode($responce);
        }
    }

    //
    // Создать задачу
    //
    function createTask($connect, $dataUser) {
        $headTitle = $dataUser['title'];
        $unixDate = strtotime($dataUser['date']);
        $name = $dataUser['name-tolmut'];
         // Получение id из таблицы tolmut для данного name
        $tolmutResult = mysqli_query($connect, "SELECT `id` FROM `tolmut` WHERE `name` = '$name'");
    
        // Проверка на наличие соответствующего id
        if(mysqli_num_rows($tolmutResult) > 0) {
            $tolmutRow = mysqli_fetch_assoc($tolmutResult);
            $idTolmut = $tolmutRow['id'];

            // Проверка, существует ли уже задача с таким названием
            $taskResult = mysqli_query($connect, "SELECT `id` FROM `task` WHERE `title` = '$headTitle'");
            if(mysqli_num_rows($taskResult) == 0) {
                // Вставка новой задачи, если она уникальна
                $insertResult = mysqli_query($connect, "INSERT INTO `task` (`title`, `date_start_event`, `id_tolmut`) VALUES ('$headTitle', $unixDate, $idTolmut)");
                if($insertResult) {
                    http_response_code(201);
                    $response = [
                        "status" => true,
                        "task_id" => mysqli_insert_id($connect),
                        "description" => "Информация успешно добавлена."
                    ];
                } else {
                    http_response_code(500);
                    $response = [
                        "status" => false,
                        "description" => "Ошибка при добавлении задачи."
                    ];
                }
            } else {
                http_response_code(400);
                $response = [
                    "status" => false,
                    "description" => "Задача с таким названием уже существует."
                ];
            }
        } else {
            http_response_code(404);
            $response = [
                "status" => false,
                "description" => "Указанный name не найден в таблице tolmut."
            ];
        }

        echo json_encode($response);
    }

    //
    // Создать подзадачу
    //
    function createSubTask($connect, $data) {
        $title = $data['title'];
        $dateString = $data['date'];
        $dateUnix = strtotime($dateString);
    
        $query = "INSERT INTO `sub_task` (`title`, `date`) VALUES (?, ?)";
    
        if ($stmt = mysqli_prepare($connect, $query)) {
            mysqli_stmt_bind_param($stmt, "si", $title, $dateUnix);
    
            $success = mysqli_stmt_execute($stmt);
    
            if ($success) {
                http_response_code(201);
                echo json_encode(
                    [
                        "status" => true, 
                        "description" => "Задача успешно добавлена!",
                        "sub_task_id" => mysqli_insert_id($connect)
                    ]
                );
            } else {
                http_response_code(400);
                echo json_encode(
                    [
                        "status" => false, 
                        "description" => "Ошибка добавления подзадачи."
                    ]
                );
            }
    
            // Close the statement.
            mysqli_stmt_close($stmt);
        } else {
            http_response_code(500);
            echo json_encode(["status" => false, "description" => "Ошибка запроса."]);
        }
    }
    
?>