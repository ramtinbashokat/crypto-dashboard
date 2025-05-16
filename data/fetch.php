<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

$parameters = [
  'start' => '1',
  'limit' => '10',
  'convert' => 'USD'
];

$headers = [
  'Accepts: application/json',
  'X-CMC_PRO_API_KEY: c8252038-e0d3-4757-9f51-62d50e1a9beb'
];

$qs = http_build_query($parameters); 
$request = "{$url}?{$qs}"; 

$curl = curl_init(); 
curl_setopt_array($curl, [
    CURLOPT_URL => $request,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);

file_put_contents(__DIR__ . "/debug.txt", $response);

if ($err) {
    echo json_encode(['error' => 'cURL Error: ' . $err]);
    exit;
}

if ($httpcode !== 200) {
    echo json_encode(['error' => 'HTTP Error: ' . $httpcode, 'response' => $response]);
    exit;
}

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON Decode Error: ' . json_last_error_msg()]);
    exit;
}

if (!isset($data['data']) || !is_array($data['data'])) {
    echo json_encode(['error' => 'API response invalid', 'raw_response' => $data]);
    exit;
}

$cleanData = array_map(function ($coin) {
    return [
        'name' => $coin['name'],
        'symbol' => $coin['symbol'],
        'price' => round($coin['quote']['USD']['price'], 2),
        'percent_change_1h' => round($coin['quote']['USD']['percent_change_1h'], 2),
        'percent_change_24h' => round($coin['quote']['USD']['percent_change_24h'], 2),
        'percent_change_7d' => round($coin['quote']['USD']['percent_change_7d'], 2),
        'sparkline' => generateDummySparkline()
    ];
}, array_slice($data['data'], 0, 10));

echo json_encode($cleanData);

function generateDummySparkline() {
    $values = [];
    $base = rand(100, 300);
    for ($i = 0; $i < 30; $i++) {
        $values[] = $base + rand(-10, 10) + $i * 0.5;
    }
    return $values;
}
