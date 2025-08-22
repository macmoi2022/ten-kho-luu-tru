if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'generate_qr') {
        header('Content-Type: application/json');

        $clientId = '456b6df3-1ccf-4e4c-a114-c4609ee2abdb';
        $apiKey = 'd854ee9e-2d4f-4581-b25e-eeb112709f81';

        $data = [
            'acqId' => $input['bin'],
            'accountNo' => $input['accountNumber'],
            'accountName' => $input['accountName'],
            'amount' => $input['amount'],
            'addInfo' => $input['description'],
            'format' => 'text',
            'template' => 'qr_only'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.vietqr.io/v2/generate',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'x-client-id: ' . $clientId,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(['code' => '99', 'desc' => 'cURL Error: ' . $err]);
        } else {
            echo $response;
        }
        exit();
    }
}