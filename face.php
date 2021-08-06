<?php

# This is your API token
$TOKEN = "3a5ec8a9dc7e4037808a462aa3456911";

# Defining the people we want to recognize later
# You can also upload files from local storage instead of using URL
$PEOPLE = [
        [
                "name" => "Angelina Jolie",
                "photo" => "https://dashboard.luxand.cloud/img/angelina-jolie.jpg"
        ],
        [
                "name" => "Brad Pitt",
                "photo" => "https://dashboard.luxand.cloud/img/brad-pitt.jpg"
        ]
];

function request($method, $url, $data){
	global $TOKEN;

	$curl = curl_init();

	foreach ($data as $key => $value)
		if (($key == "photo") && (strpos($value, "http://") === false) && (strpos($value, "https://") === false) && (file_exists($value)))
			$data[$key] = curl_file_create($value);

	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $data, 
		CURLOPT_HTTPHEADER => [ "token: $TOKEN" ]
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
		die;
	};

	return json_decode($response);
}

foreach ($PEOPLE as $person){
	$response = request("POST", "https://api.luxand.cloud/subject", ["name" => $person["name"]]);
	request("POST", "https://api.luxand.cloud/subject/" . $response->id, ["photo" => $person["photo"]]);
}

# You can also upload files from local storage instead of using URL
$result = request("POST", "https://api.luxand.cloud/photo/search", ["photo" => "https://dashboard.luxand.cloud/img/angelina-and-brad.jpg"]);
print_r($result);