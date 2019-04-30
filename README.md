# SL PHP SDK

Storstockholms Lokaltrafik (SL) offers real-time data about Stockholm's public transport. 
Using it you can show all departures and arrivals for a stop, or easily plan a route from A to B.
More information can be found at [the Trafiklab website](https://www.trafiklab.se/api/).

This repository contains a PHP SDK to easily use the SL departures and routeplanning APIs. This way you don't need to worry about making requests, caching, 
or parsing responses. All responses are parsed and returned as PHP classes.

**_Work in progress_**: _There is no 1.0.0 release available yet. If you want to get a sneak peak, 
you can get it by adding this repository manually to your projects composer file. 
However, keep in mind that breaking changes are still possible. A first release is estimated to arrive in one of the next months._

## Installation
Installing can be done by using Composer:

`composer require trafiklab/sl-php-sdk`

#### Versioning

This package follows [Semantic versioning](https://semver.org/):
> Given a version number MAJOR.MINOR.PATCH, we increment the:
> - MAJOR version when we make incompatible API changes,
> - MINOR version when we add functionality in a backwards-compatible manner, and
> - PATCH version when we make backwards-compatible bug fixes.

Additional labels for pre-release and build metadata are available as extensions to the MAJOR.MINOR.PATCH format.

## Requirements
The following software is required to use this SDK:

- PHP 7.1 or higher
- PHP Curl extension
- PHP JSON extension

## Usage

In order to use the SL Timetable and Routeplanning APIs, 
you need to obtain an API key from [Trafiklab](https://trafiklab.se) first.

### Getting Timetables (departures or arrivals from a stop)

#### Timetables
##### Request
The following code example illustrates how you can retrieve a timetable for a certain stop.

```
  $departuresRequest = new SlTimeTableRequest();
  $departuresRequest->setStopId("1000");

  $slWrapper = SlWrapper::getInstance();
  $slWrapper->registerUserAgent("<YOUR_USER_AGENT>");
  $slWrapper->registerTimeTablesApiKey("<YOUR_API_KEY>");
  $response = $slWrapper->getTimeTable($departuresRequest);
```
`<YOUR_API_KEY>` is obtained from [Trafiklab](https://trafiklab.se). `<YOUR_USER_AGENT>` is a string which identifies your application. 
While this is not enforced in any way, it is good practice to use a clear user agent. 
An example could be `MyDemoApp/1.0.0 (mail@example.com) `.
If you don't want to send a user agent, you can just leave out this line.

Detailed information about SL request parameters can be found at [the Trafiklab website](https://www.trafiklab.se/api/).
Only the most important/most used request parameters are implemented in the SDK, in order to reduce clutter, and to ensure that we can keep the SDK unchanged in case of changes to the API.
If you believe we have missed an important field, please create an issue so we can review this.  
##### Response

In order to use the data returned by your request, you can simply call getTimeTable() on the response object. 
This method returns an array of TimeTableEntry instances, each of which describes one departure or arrival. 
You can look at the code and PHPDoc in order to get up-to-date information on which fields are available. 
Detailed information about SL responses can be found at [the Trafiklab website](https://www.trafiklab.se/api/).

```
   $response->getTimetable()
```

#### Routeplanning
##### Request
The following code example illustrates how you can plan a route from A to B

```    
    // TODO: add example
```
##### Response

```
   // TODO: add example
```

## Contributing

We accept pull requests, but please create an issue first in order to discuss the addition or fix.
If you would like to see a new feature added, you can also create a feature request by creating an issue.

## Help

If you're stuck with a question, feel free to ask help through the Issue tracker.
- Need help with API keys? Please read [www.trafiklab.se/api-nycklar](https://www.trafiklab.se/api-nycklar) first.
- Do you want to check the current systems status? Service disruptions
 are published on the [Trafiklab homepage](https://www.trafiklab.se/)