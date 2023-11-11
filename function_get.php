<?php 
    require_once "function_responce.php";
    ///
    /// Вывести всех пользователей
    ///
    function viewallusers($connect){
        $users = mysqli_query($connect, "
        SELECT 
            u.id AS user_id,
            u.login,
            u.name AS user_name,
            r.name AS role_name,
            lw.name AS level_name
        FROM 
            user u
            LEFT JOIN role r ON u.id_role = r.id
            LEFT JOIN level_worker lw ON u.id_level_worker = lw.id  

        ");
        if(mysqli_num_rows($users) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            $userList = array();
            while($user = mysqli_fetch_assoc($users)){
                $userList[] = $user;
            }

            echo json_encode($userList);
        }
    }

  
    

    function getNotification($connect) {
        // Подготовка массива для результатов
        $missingEntries = [];
    
        // Получение всех идентификаторов и имен из таблицы tolmut
        $tolmutIdsResult = mysqli_query($connect, "SELECT id, name, time_is_out FROM tolmut");
    
        // Получение всех идентификаторов и путей из таблицы address_list
        $addressListResult = mysqli_query($connect, "SELECT id, path, latitude, longitude FROM address_list");
        $addresses = [];
        
        while ($address = mysqli_fetch_assoc($addressListResult)) {
            $addresses[$address['id']] = [
                'id' => $address['id'],
                'path' => $address['path'],
                'latitude' => $address['latitude'],
                'longitude' => $address['longitude']
            ];
        }
        // Получение списка всех менеджеров
        $managerResult = mysqli_query($connect, "SELECT id, name FROM user");
        $managers = mysqli_fetch_all($managerResult, MYSQLI_ASSOC);
    
        while ($tolmut = mysqli_fetch_assoc($tolmutIdsResult)) {
            foreach ($addresses as $addressId => $addressPath) {
                // Проверка наличия записи в расписании
                $sqlCheckSchedule = "SELECT 1 FROM schedule WHERE id_tolmut = '{$tolmut['id']}' AND id_address_list = '{$addressId}'";
                $scheduleResult = mysqli_query($connect, $sqlCheckSchedule);
            
                if (mysqli_num_rows($scheduleResult) == 0) {
                    // Выбор случайного менеджера из списка для каждой записи в tolmut
                    $randomManagerKey = array_rand($managers);
                    $manager = $managers[$randomManagerKey];
    
                    // Добавление информации о менеджере и о записи
                    if (!isset($missingEntries[$addressId])) {
                        $missingEntries[$addressId] = [
                            'information-address' => $addressPath,
                            'notification' => 'По данному адресу прошел срок давности по выполнению задач.',
                            'missing-tolmuts' => []
                        ];
                    }
                    $missingEntries[$addressId]['missing-tolmuts'][] = [
                        'id' => $tolmut['id'],
                        'name' => $tolmut['name'],
                        'time' => $tolmut['time_is_out'],
                        'recomendation-courier' => $manager
                    ];
                }
            }
        }
    
        // Вывод результатов в формате JSON
        echo json_encode(array_values($missingEntries));
    }
    
    
    
                
    



    ///
    /// Вывести пользователя по ID
    ///
    function viewalluser($connect, $id){
        $user = mysqli_query($connect, "
        SELECT 
            u.id AS user_id,
            u.login,
            u.name AS user_name,
            r.name AS role_name,
            lw.name AS level_name
        FROM 
            user u
            LEFT JOIN role r ON u.id_role = r.id
            LEFT JOIN level_worker lw ON u.id_level_worker = lw.id
        WHERE u.id=".$id);
        if(mysqli_num_rows($user) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Пользователя нет.",
                "link" => "https://easy4.team/"
            ];
            
            echo json_encode($responce);
        }
        else{
            http_response_code(200);
            echo json_encode(mysqli_fetch_assoc($user));
        }
    }

    ///
    /// Вывести все роли
    ///
    function viewallroles($connect){
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            role
        ");
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                $list[] = $data;
            }

            echo json_encode($list);
        }
    }
    
    ///
    /// Вывести роль
    ///
    function viewallrole($connect, $id){
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            role
        WHERE id=
        ".$id);
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            echo json_encode(mysqli_fetch_assoc($request));
        }
    }

    ///
    /// Вывести все уровни (грейды) пользователей
    ///
    function viewalllevel($connect){
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            level_worker
        ");
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                $list[] = $data;
            }

            echo json_encode($list);
        }
    }

    ///
    /// Вывести конкретный уровень по ID
    ///
    function viewlevelworker($connect, $id) {
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            level_worker
        WHERE id=
        ".$id);
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            echo json_encode(mysqli_fetch_assoc($request));
        }
    }

    ///
    /// Вывести всю инфомрацию по описанию всех условий
    ///    
    function viewAllCondition($connect){
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            condition_tolmut
        ");
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                $list[] = $data;
            }

            echo json_encode($list);
        }
    }

    ///
    /// Вывести всю информацию по описанию условий зная ID
    ///
    function viewTolmutData($connect, $id) {
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            condition_tolmut
        WHERE id=
        ".$id);
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            echo json_encode(mysqli_fetch_assoc($request));
        }
    }

    ///
    /// Вывести всю инфомрацию по всем правилам
    ///
    function viewListTolmut($connect) {
        $request = mysqli_query($connect, "
        SELECT 
            t.id,
            t.name,
            GROUP_CONCAT(CONCAT(c.id, '#', c.name) SEPARATOR '; ') as conditions
        FROM 
            tolmut t
        LEFT JOIN condition_tolmut c ON t.id_continion = c.id
        GROUP BY t.name;    
        ");
        
        if (mysqli_num_rows($request) == 0) {
            http_response_code(404);
            $response = [
                "status" => false,
                "description" => "Таблица пуста."
            ];
            echo json_encode($response);
        } else {
            $list = array();
            while ($data = mysqli_fetch_assoc($request)) {
                // Разделяем условия, используя '; ' как разделитель
                $conditionPairs = explode('; ', $data['conditions']);
                $conditions = array();
                foreach ($conditionPairs as $pair) {
                    // Теперь разделяем id и name, используя '#' как разделитель
                    list($conditionId, $conditionName) = explode('#', $pair);
                    $conditions[] = [
                        'id' => $conditionId,
                        'condition_name' => $conditionName
                    ];
                }
                $list[] = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'conditions' => $conditions
                ];
            }
            echo json_encode($list);
        }  
    }

    function viewallSchedule($connect) {
        $request = mysqli_query($connect, "
        SELECT 
            sch.date,
            t.title AS task_title,
            sch.id_user,
            u.name AS name_user,
            t.id AS task_id,
            sch.id_user,
            GROUP_CONCAT(CONCAT_WS(':', st.id, st.title, sch.status_task) ORDER BY st.title ASC SEPARATOR ', ') AS sub_task_titles,
            tol.name AS tolmut_name,
            tol.id AS tolmut_id,
            a.path AS address_list,
            a.id AS address_id
        FROM 
            schedule sch
            LEFT JOIN task t ON sch.id_task = t.id
            LEFT JOIN sub_task st ON sch.id_sub_task = st.id
            LEFT JOIN tolmut tol ON sch.id_tolmut = tol.id
            LEFT JOIN address_list a ON sch.id_address_list = a.id
            LEFT JOIN user u ON sch.id_user = u.id
        GROUP BY 
            sch.date,
            t.title,
            t.id,
            tol.name,
            tol.id,
            a.path,
            a.id
        ORDER BY 
            t.id ASC, sch.date ASC    
        ");
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $response = [
                "status" => false,
                "description" => "Таблица пуста."
            ];
    
            echo json_encode($response);
        }
        else{
            http_response_code(200);
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                // Разделяем подзадачи и статусы
                $sub_task_entries = explode(', ', $data['sub_task_titles']);
                $sub_tasks = array_map(function($entry) {
                    list($id, $title, $status) = explode(':', $entry);
                    return ["id" => $id,"title" => $title, "status" => $status];
                }, $sub_task_entries);
    
                $list[] = [
                    "date" => $data['date'],
                    "user-courier-info" =>[
                        "id" => $data['id_user'],
                        "name" => $data['name_user']
                    ], 
                    "task" => [
                        "id" => $data['task_id'],
                        "title" => $data['task_title'],
                        "sub_task" => $sub_tasks
                    ],
                    "tolmut" => [
                        "id" => $data['tolmut_id'],
                        "name" => $data['tolmut_name']
                    ],
                    "address" => [
                        "id" => $data['address_id'],
                        "path" => $data['address_list']
                    ]
                ];
            }
    
            echo json_encode($list);
        } 
    }


    function getTask($connect, $id) {
        $request = mysqli_query($connect, " 
        SELECT 
            sch.date,
            t.title AS task_title,
            sch.id_user,
            u.name AS name_user,
            t.id AS task_id,
            sch.id_user,
            GROUP_CONCAT(CONCAT_WS(':', st.title, sch.status_task) ORDER BY st.title ASC SEPARATOR ', ') AS sub_task_titles,
            tol.name AS tolmut_name,
            tol.id AS tolmut_id,
            a.path AS address_list,
            a.id AS address_id
        FROM 
            schedule sch
            LEFT JOIN task t ON sch.id_task = t.id
            LEFT JOIN sub_task st ON sch.id_sub_task = st.id
            LEFT JOIN tolmut tol ON sch.id_tolmut = tol.id
            LEFT JOIN address_list a ON sch.id_address_list = a.id
            LEFT JOIN user u ON sch.id_user = u.id
        WHERE
            t.id=".$id."
        GROUP BY 
            sch.date,
            t.title,
            t.id,
            tol.name,
            tol.id,
            a.path,
            a.id
        ORDER BY 
            t.id ASC, sch.date ASC
        ");
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $response = [
                "status" => false,
                "description" => "Таблица пуста."
            ];
    
            echo json_encode($response);
        }
        else{
            http_response_code(200);
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                // Разделяем подзадачи и статусы
                $sub_task_entries = explode(', ', $data['sub_task_titles']);
                $sub_tasks = array_map(function($entry) {
                    list($title, $status) = explode(':', $entry);
                    return ["title" => $title, "status" => $status];
                }, $sub_task_entries);
    
                $list[] = [
                    "date" => $data['date'],
                    "user-courier-info" =>[
                        "id" => $data['id_user'],
                        "name" => $data['name_user']
                    ], 
                    "task" => [
                        "id" => $data['task_id'],
                        "title" => $data['task_title'],
                        "sub_task" => $sub_tasks
                    ],
                    "tolmut" => [
                        "id" => $data['tolmut_id'],
                        "name" => $data['tolmut_name']
                    ],
                    "address" => [
                        "id" => $data['address_id'],
                        "path" => $data['address_list']
                    ]
                ];
            }
    
            echo json_encode($list);
        } 
    }

    function getAllTask($connect) {
        $request = mysqli_query($connect, "
        SELECT 
            t.id AS id_task,
            t.title AS title,
            t.date_start_event AS start_event,
            lw.name AS level_task
        FROM 
            task t
            LEFT JOIN level_worker lw ON id_grade = lw.id
        ");
        
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                $list[] = $data;
            }

            echo json_encode($list);
        }
    }

    function getOneTask($connect, $id) {
        $request = mysqli_query($connect, "
        SELECT 
            t.id AS id,
            t.title AS title,
            t.date_start_event AS date,
            lw.name AS grade
        FROM 
            task t
                LEFT JOIN level_worker lw ON t.id_grade = lw.id
        WHERE t.id=".$id);
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            echo json_encode(mysqli_fetch_assoc($request));
        }
    }

    function getAllSubTasks($connect){
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            sub_task
        ");
        
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            $list = array();
            while($data = mysqli_fetch_assoc($request)){
                $list[] = $data;
            }

            echo json_encode($list);
        }
    }

    function getOneSubTask($connect, $id){
        $request = mysqli_query($connect, "
        SELECT 
            *
        FROM 
            sub_task
        WHERE id=
        ".$id);
        
        if(mysqli_num_rows($request) == 0){
            http_response_code(404);
            $responce = [
                "status" => false,
                "description" => "Таблица пуста."
            ];

            echo json_encode($responce);
        }
        else{
            
            echo json_encode(mysqli_fetch_assoc($request));
        }
    }

?>