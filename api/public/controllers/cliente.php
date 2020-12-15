<?php
    
    $app->post('/cliente', function ($request, $response, $args) {

        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $params = array( $data["deweCodigo"], $data["deweJSON"]);
        
        if(! isValidoCaptcha($data["deweCaptchaResponse"])){
            return;
        };

        $db = SQLSRV::connect();
    
        $stmt = sqlsrv_query($db,"INSERT INTO dbo.urso
                                (clienNombre, clienDireccion ,clienJSON)
                                VALUES
                                (GETDATE(),?,?)", $params);
        if($stmt === false) {
            SQLSRV::error(500, 'Error interno del servidor', $db);
        }
    
        $results= [];
    
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (isset($row)){
            $results["guardado"] = "OK";
            $results["fecha"] = date('Y-m-d\TH:i:s');        
        } else {
            $results["guardado"] = "ERROR";
        }
    
        sqlsrv_free_stmt($stmt);
        SQLSRV::close($db);
    
        $payload = json_encode($results);
    
        $response->getBody()->write($payload);
        return $response
                  ->withHeader('Content-Type', 'application/json');
    
    });

?>