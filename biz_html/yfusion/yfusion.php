<?php

/**
 * Yelp Fusion API code sample.
 *
 * This program demonstrates the capability of the Yelp Fusion API
 * by using the Business Search API to query for businesses by a 
 * search term and location, and the Business API to query additional 
 * information about the top result from the search query.
 * 
 * Please refer to http://www.yelp.com/developers/v3/documentation 
 * for the API documentation.
 * 
 * Sample usage of the program:
 * `php sample.php --term="dinner" --location="San Francisco, CA"`
 */

$conf = parse_ini_file($_DOCUMENT_ROOT . '../conf/' . php_uname('n') .'.ini', true);

// OAuth credential placeholders that must be filled in by users.
// You can find them on
// https://www.yelp.com/developers/v3/manage_app
$CLIENT_ID      = $conf['fusion']['client_id'];
$CLIENT_SECRET  = $conf['fusion']['client_secret'];

// Complain if credentials haven't been filled out.
assert($CLIENT_ID, "Please supply your client_id.");
assert($CLIENT_SECRET, "Please supply your client_secret.");

// API constants, you shouldn't have to change these.
$API_HOST       = $conf['fusion']['api_host'];
$SEARCH_PATH    = $conf['fusion']['search_path'];
$BUSINESS_PATH  = $conf['fusion']['business_path'];  // Business ID will come after slash.
$TOKEN_PATH     = "/oauth2/token";
$GRANT_TYPE     = "client_credentials";

// Defaults for our simple example.
$DEFAULT_TERM           = "Gun Shops";
$DEFAULT_LOCATION       = "Nashville, TN";
$SEARCH_LIMIT           = 0;
$REVIEWS                = 0;

if(isset($_GET['term'])) $DEFAULT_TERM = $_GET['term'];
if(isset($_GET['loc']) && $_GET['loc'] !='') $DEFAULT_LOCATION = $_GET['loc'] ;
if(isset($_GET['limit'])) $SEARCH_LIMIT = $_GET['limit'];
if(isset($_GET['reviews'])) $REVIEWS = $_GET['reviews'];

// load info about search coordinates address, zipcode, etc.
$search_coords = get_zip_geocode($DEFAULT_LOCATION);


/**
 * Given a bearer token, send a GET request to the API.
 * 
 * @return   OAuth bearer token, obtained using client_id and client_secret.
 */

function obtain_bearer_token() {
    try {
        # Using the built-in cURL library for easiest installation.
        # Extension library HttpRequest would also work here.
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');

        $postfields = "client_id=" . $GLOBALS['CLIENT_ID'] .
            "&client_secret=" . $GLOBALS['CLIENT_SECRET'] .
            "&grant_type=" . $GLOBALS['GRANT_TYPE'];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $GLOBALS['API_HOST'] . $GLOBALS['TOKEN_PATH'],
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));

        $response = curl_exec($curl);

        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);

        curl_close($curl);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }

    $body = json_decode($response);
    $bearer_token = $body->access_token;
    return $bearer_token;
}


/** 
 * Makes a request to the Yelp API and returns the response
 * 
 * @param    $bearer_token   API bearer token from obtain_bearer_token
 * @param    $host    The domain host of the API 
 * @param    $path    The path of the API after the domain.
 * @param    $url_params    Array of query-string parameters.
 * @return   The JSON response from the request      
 */
function request($bearer_token, $host, $path, $url_params = array()) {
    // Send Yelp API Call
    try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');

        $url = $host . $path . "?" . http_build_query($url_params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $bearer_token,
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);

        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);

        curl_close($curl);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }

    return $response;
}

/**
 * Query the Search API by a search term and location 
 * 
 * @param    $bearer_token   API bearer token from obtain_bearer_token
 * @param    $term        The search term passed to the API 
 * @param    $location    The search location passed to the API 
 * @return   The JSON response from the request 
 */
function search($bearer_token, $term, $location) {
    $url_params = array();
    
    $url_params['term']     = $term;
    $url_params['location'] = $location;
    $url_params['limit']    = $GLOBALS['SEARCH_LIMIT'];
    
    return request($bearer_token, $GLOBALS['API_HOST'], $GLOBALS['SEARCH_PATH'], $url_params);
}

/**
 * Query the Business API by business_id
 * 
 * @param    $bearer_token   API bearer token from obtain_bearer_token
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request 
 */
function get_business($bearer_token, $business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id);
    return request($bearer_token, $GLOBALS['API_HOST'], $business_path);
}

function get_review($bearer_token, $business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] .urlencode($business_id) .'/reviews';
    return request($bearer_token, $GLOBALS['API_HOST'], $business_path);
}

/**
 * Queries the API by the input values from the user 
 * 
 * @param    $term        The search term to query
 * @param    $location    The location of the business to query
 */
function query_api($term, $location) {
    global $REVIEWS;
    global  $search_coords;

    $bearer_token   = obtain_bearer_token();
    $response       = json_decode(search($bearer_token, $term, $location));

    foreach($response->businesses as $business ){

       //print_r( json_decode( get_business($bearer_token, $business->id) ) );

        $cats = array();
        print "<div class='panel panel-default'>";
        print "<div class='panel-heading'>";
        print "<h4 class='panel-title'>". $business->name . "</h4>";
        print "</div><!-- panel-heading -->";
        print "<div class='panel-body'>";
        print "<p>Category: ";

        foreach($business->categories as $category){
           $cats[] = $category->title;
       }

        print implode(", ", $cats) . "</p>" ;

        print "<p>Overall Rating:" . $business->rating. " ";
        print "Total Reviews:" . $business->review_count . "</p>";
        print "<p>". $business->location->address1 . " ";
        print $business->location->city . ", ";
        print $business->location->state . " " . $business->location->zip_code. "</p>";

       // print "Lat:" . $business->coordinates->latitude . " Lng: " . $business->coordinates->longitude. "\n";
        print "<p>Phone: ". $business->display_phone . "</p>";
        print "<p>About " . distance($search_coords['location']['lat'], $search_coords['location']['lng'],
          $business->coordinates->latitude, $business->coordinates->longitude, "M") . " miles from " . $location. "</p>";

        print "</div><!-- panel-body -->";
        print "</div><!-- panel-default -->";

        if($REVIEWS > 0){

            $reviews = json_decode(  get_review($bearer_token, $business->id) );
            print "<div class='col-sm-offset-2'>";
            print "<p>" .count($reviews->reviews) ." reviews:";
            foreach($reviews->reviews as $review){
                print "<div class='row'>Rating:" .$review->rating . " stars - ";
                print $review->text ." : by ";
                print $review->user->name ."</div>";
            }
            print "</p><br/>";
        }


    }

}

function get_zip_geocode($location){
    $request = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($location));
    $json = json_decode($request, true);

    $out['formatted_address'] = $json['results'][0]['formatted_address'];
    $out['location']['lat']   = $json['results'][0]['geometry']['location']['lat'];
    $out['location']['lng']   = $json['results'][0]['geometry']['location']['lng'];

    return $out;

}

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return round($miles);
    }
}
/**
 * User input is handled here 
 */
$longopts  = array(
    "term::",
    "location::",
);
    
$options  = getopt("", $longopts);
$term     = $options['term'] ?: $GLOBALS['DEFAULT_TERM'];
$location = $options['location'] ? : $GLOBALS['DEFAULT_LOCATION'];

if($_GET['term'] ) {
    query_api($term, $location);


}


?>
