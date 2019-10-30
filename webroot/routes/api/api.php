<?php


function api_negotiateContent($data){
    $app = \Slim\Slim::getInstance();

    $urlParams = $app->router()->getCurrentRoute()->getParams();
    $urlParam = isset($urlParams['type']) ? $urlParams['type'] : null;
    $accept = $app->request->headers->get('Accept');
    $possibleFormats = [
        'json' => 'json', 'application/json' => 'json', 'text/json' => 'json',
        #'csv' => 'csv', 'application/csv' => 'csv', 'text/csv' => 'csv',
    ];

    // Url param takes precidence
    if($urlParam && array_key_exists($urlParam, $possibleFormats)){
        return api_convertToFormat($possibleFormats[$urlParam], $data);
    }

    // The Accept header
    if($accept && array_key_exists($accept, $possibleFormats)){
        return api_convertToFormat($possibleFormats[$accept], $data);
    }

    // json by default
    return api_convertToFormat('json', $data);
}

function api_convertToFormat($format, $data){
    $app = \Slim\Slim::getInstance();
    switch($format){
        case 'csv':
            ob_start();
            // use fputcsv for escaping
            {
                $out = fopen('php://output', 'w');
                // Check if it is a multidimensional array
                if(is_array($data[0])){
                    fputcsv($out, array_keys($data[0]));
                }else{
                    fputcsv($out, array_keys($data));
                }
                foreach ($data as $row) {
                    foreach ($row as $colKey => $col) {
                        if(is_array($col)){
                            foreach($col as $key => $a){
                                $row[$colKey . '.' . $key] = $a;
                            }
                            unset($row[$colKey]);
                        }
                        #$d['images.profile'] = (isset($d['images']) && isset($d['images']['profile'])) ? $d['images']['profile'] : null;
                    }
                    #unset($d['images']);
                    fputcsv($out, $row);
                }
                fclose($out);
                $out = ob_get_contents();
            }
            ob_end_clean();
            $app->response->headers->set('Content-Type', 'text/csv');
            return $out;
            break;


        case 'json':
        default:
            $app->response->headers->set('Content-Type', 'application/json');
            return json_encode($data);
    }
}
