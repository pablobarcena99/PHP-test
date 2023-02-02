<?php
// Enter your code here, enjoy!
$cities = ['Logroño', 'Zaragoza', 'Teruel', 'Madrid', 'Lleida', 'Alicante', 'Castellón', 'Segovia', 'Ciudad Real'];
$connections = [
  [0, 4, 6, 8, 0, 0, 0, 0, 0],
  [4, 0, 2, 0, 2, 0, 0, 0, 0],
  [6, 2, 0, 3, 5, 7, 0, 0, 0],
  [8, 0, 3, 0, 0, 0, 0, 0, 0],
  [0, 2, 5, 0, 0, 0, 4, 8, 0],
  [0, 0, 7, 0, 0, 0, 3, 0, 7],
  [0, 0, 0, 0, 4, 3, 0, 0, 6],
  [0, 0, 0, 0, 8, 0, 0, 0, 4],
  [0, 0, 0, 0, 0, 7, 6, 4, 0]
];

//Asks user for a departure and checks if it's valid
$departureInput = ucwords((string)readline('Enter a departure: '));
if (in_array($departureInput, $cities)) {
  $departureInput = array_search($departureInput, $cities);
  $mode = (float)readline("Enter 1 if you want to know the cheapest route to one destination.Enter 2 if you want to know the cheapest route to every destination.");
  $departure = $departureInput;
  $route = [$departure];
} else {
  return print("\nNot a valid departure");
}
//Intialize the variable where the possible routes are stored
$possibleRoutes = [];

//Asks user for the program mode
if ($mode == 1) {
  //This is the path to find the route of one destination
  $destinationInput = ucwords((string)readline('Enter a destination: '));

  if (in_array($destinationInput, $cities)) {
    $destinationInput = array_search($destinationInput, $cities);
    $destination = $destinationInput;
    getPossibleRoutes($connections, $route, $destination);
    getCheapestRouteToDestination($possibleRoutes, $cities, $departure, $destination);
  } else {
    return print("\nNot a valid destination");
  }
} elseif ($mode == 2) {
  //This is the path to find the route for all destinations
  $destinationCities = $cities;
  unset($destinationCities[$departure]);
  foreach ($destinationCities as $city => $name) {
    getPossibleRoutes($connections, $route, $city);
    getCheapestRouteToDestination($possibleRoutes, $cities, $departure, $city);
  }
} else {
  return print("\nInvalid mode");
}


//Tries every route and stores the ones that ends on destination.
function getPossibleRoutes($connections, array $route, $destination, $originPrice = 0)
{
  $origin = end($route);
  $originConnections = array_filter($connections[$origin]);
  foreach ($originConnections as $connection => $price) {
    $totalPrice = $originPrice + $price;
    if ($connection == $destination) {
      $finalRoute = $route;
      array_push($finalRoute, $connection);
      array_push($GLOBALS['possibleRoutes'], ["route" => $finalRoute, "price" => $totalPrice]);
    } else {
      $newConnections = [];
      foreach ($connections as $link) {
        $link[$origin] = 0;
        array_push($newConnections, $link);
      }
      if (!empty($newConnections)) {
        $finalRoute = $route;
        array_push($finalRoute, $connection);
        getPossibleRoutes($newConnections, $finalRoute, $destination, $totalPrice);
      }
    }
  }
}

//It takes all possible routes and returns the cheapest one
function getCheapestRouteToDestination($possibleRoutes, $cities, $departure, $destination)
{
  $routesToDestination = array_filter($possibleRoutes, function ($value) use ($destination) {
    return (end($value["route"]) == $destination);
  });

  $sortedRoutes = sortRoutesByPrice($routesToDestination);
  $citiesOfRoute = [];

  foreach ($sortedRoutes[0]["route"] as $city) {
    array_push($citiesOfRoute, $cities[$city]);
  }

  $citiesOfRoute = implode(', ', $citiesOfRoute);
  $routePrice = $sortedRoutes[0]["price"];

  echo "\n The cheapest route to go from $cities[$departure] to $cities[$destination] is $citiesOfRoute and the cost is $routePrice.";
}

//This function takes the possible routes and sorts them by it's price.
function sortRoutesByPrice($possibleRoutes)
{
  usort($possibleRoutes, function ($a, $b) {
    return $a['price'] <=> $b['price'];
  });

  return $possibleRoutes;
}
