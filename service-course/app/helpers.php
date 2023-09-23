<?php

use Illuminate\Support\Facades\Http;

// get a user by id
function getUser($userId)
{
  $url = env('SERVICE_USER_URL') . 'users/' . $userId;

  try {
    $response = Http::timeout(10)->get($url);
    $data = $response->json();
    $data['http_code'] = $response->getStatusCode();

    return $data;
  } catch (\Throwable $th) {
    return [
      'status' => "Error",
      'http_code' => 500,
      'message' => 'Service user unavailable'
    ];
  }
}

// get more than 1 user
function getUserByIds($userIds = [])
{
  $url = env('SERVICE_USER_URL') . 'users/';

  try {
    if (count($userIds) === 0) {
      return [
        'status' => "Success",
        'http_code' => 200,
        'data' => []
      ];
    }

    $response = Http::timeout(10)->get($url, ['user_ids[]' => $userIds]);
    $data = $response->json();
    $data['http_code'] = $response->getStatusCode();

    return $data;
  } catch (\Throwable $th) {
    return [
      'status' => "Error",
      'http_code' => 500,
      'message' => 'Service user unavailable'
    ];
  }
}

function postOrder($params)
{
  $url = env('SERVICE_ORDER_PAYMENT_URL') . 'api/orders';

  try {
    $response = Http::post($url, $params);
    $data = $response->json();

    $data['http_code'] = $response->getStatusCode();

    return $data;
  } catch (\Throwable $th) {
    return [
      'status' => "Error",
      'http_code' => 500,
      'message' => 'Service order payment unavailable'
    ];
  }
}
