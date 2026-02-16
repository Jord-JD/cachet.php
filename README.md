# cachet.php

[![Packagist Downloads](https://img.shields.io/packagist/dt/jord-jd/cachetphp?label=Packagist%20Downloads)](https://packagist.org/packages/jord-jd/cachetphp)
[![Tests](https://github.com/Jord-JD/cachet.php/actions/workflows/tests.yml/badge.svg)](https://github.com/Jord-JD/cachet.php/actions/workflows/tests.yml)

cachet.php is a PHP client library for the Cachet status page (https://cachethq.io/).

This library could be useful for displaying details from your Cachet status page in other systems, such as a monitoring dashboard, a client ticketing system or any other web applications.

If you want to grab cachet.php with Composer, take a look on Packagist: https://packagist.org/packages/jord-jd/cachetphp

# Quick Start

Before starting, install the cachet.php library via Composer.

## Create CachetInstance object

Now, you need to create a CachetInstance object that represents your installation of Cachet. You can do this like so:

```php
require_once 'vendor/autoload.php';

use \JordJD\CachetPHP\Factories\CachetInstanceFactory;

// The API token for the demo instance is 9yMHsdioQosnyVK4iCVR.
$cachetInstance = CachetInstanceFactory::create('https://demo.cachethq.io/api/v1/', '9yMHsdioQosnyVK4iCVR');
```

## Retrieving Cachet elements

Retrieving data from the various elements of your Cachet instance is easy. Just call the appropriate getter method on your ```$cachetInstance``` object. The Cachet install will be contacted and an array of request appropriate objects be returned.

```php
$components = $cachetInstance->getAllComponents();         // Components
$incidents = $cachetInstance->getAllIncidents();           // Incidents
$incidentUpdates = $incidents[0]->getAllIncidentUpdates(); // Incident Updates (Cachet 2.4.0 or above required)
$metrics = $cachetInstance->getAllMetrics();               // Metrics
$metricPoints = $metrics[0]->getAllMetricPoints();         // Metric Points
$subscribers = $cachetInstance->getAllSubscribers();       // Subscribers
```

To retrieve a single Cachet element by its ID, you can use code similar to the following method.

```php
// Get incident by id
$incident = $cachetInstance->getIncidentById($incidentId);
```

### Sorting Cachet elements

If you wish to sort your results, you can use the following syntax. This works for components, incidents, metrics, metric points and subscribers.

```php
// Get components sorted by name ascending
$components = $cachetInstance->getAllComponents('name', 'asc');
```

`getAll*` methods automatically follow paginated responses from Cachet and return a single combined result set.

## Reading from Cachet element objects

Reading data from retrieved Cachet element objects is easy. Just access their public member variables.

Here's an example of outputting the id, name, description and status of a Cachet component.

```php
// Get components
$components = $cachetInstance->getAllComponents();

// Display components
foreach ($components as $component) {
    echo $component->id.' - '.$component->name.' - '.$component->description.' - '.$component->status;
    echo "<br/>";
}
```

See the [official Cachet documentation](https://docs.cachethq.io/docs) for information on the variables available for each type of Cachet element.

## Creating Cachet elements

You can create a Cachet element, such as a component or incident very easily using the cachet.php library. This involves creating an associative array of the details for the Cachet elements and then passing this to the relevant creation method.

The following example shows you how to make a simple test component.

```php
$componentDetails = ['name' => 'Test Component '.rand(1, 99999), 'status' => 1];

$component = $cachetInstance->createComponent($componentDetails);

echo $component->id.' - '.$component->name.' - '.$component->description.' - '.$component->status;
```

The more comprehensive example below shows how to create an incident and add an incident update to it. 
Please note that Cachet 2.4.0 or above required if you wish to use incident updates.

```php
$incidentDetails = ['name' => 'Test Incident '.rand(1, 99999), 'message' => 'Incident message '.rand(1, 99999), 'status' => 1, 'visible' => 1];

$incident = $cachetInstance->createIncident($incidentDetails);

$incidentUpdateDetails = ['status' => 2, 'message' => 'Test incident update '.rand(1, 99999)];

$incidentUpdate = $incident->createIncidentUpdate($incidentUpdateDetails);

echo $incidentUpdate->id.' - '.$incidentUpdate->incident_id.' - '.$incidentUpdate->status.' - '.$incidentUpdate->message;
```

## Updating Cachet elements

The cachet.php allows you to make changes to a Cachet element and saving those changes back to your Cachet install. This is done by directly changing the Cachet element's public member variables, and then calling the object's `save()` method.

The following example shows how to change the name and status of a component, then save the changes.

```php
// Get components
$components = $cachetInstance->getAllComponents();

// Change component details
$component[0]->name = 'My awesome component';
$component[0]->status = 1;
$component[0]->save();
```

### Updating Component Status via Incident and Incident Updates

If you create an incident with a `component_id`, you can also include a `component_status` to change the
component's status at the same time as creating the incident. See the example below.

```php
$incidentDetails = ['name' => 'Test Incident '.rand(1, 99999), 'message' => 'Incident message '.rand(1, 99999), 'status' => 1, 'visible' => 1,
    'component_id' => 1, 'component_status' => 1];

$incident = $cachetInstance->createIncident($incidentDetails);
```

When creating incident updates, you can also specify a `component_status` to change the component's status when the
incident update is created. See the example below.

```php
$incidentUpdateDetails = ['status' => 2, 'message' => 'Test incident update '.rand(1, 99999), 'component_status' => 2];

$incidentUpdate = $incident->createIncidentUpdate($incidentUpdateDetails);
```

You can also change a component status via an incident or incident update by changing the `component_status` on the 
related object and saving, as shown below in the exmaples below. Note that this will only work if the incident was
created with an assoicated `component_id`.

```php
$incident->component_status = 2;
$incident->save();
```

```php
$incidentUpdate->component_status = 3;
$incidentUpdate->save();
```

## Deleting Cachet elements

To delete a Cachet element from your Cachet install, you simply need to call the `delete()` method on the appropriate Cachet element object.

For example, to delete an incident you could do the following.

```php
// Get incidents
$incidents = $cachetInstance->getAllIncidents();

// Delete the first one
$incidents[0]->delete();
```

# Contact

If you've found a bug or a new feature, please [report it as a GitHub issue](https://github.com/Jord-JD/cachet.php/issues).

For other queries, I'm available on Twitter ([Jordan Hall](https://twitter.com/divineomega)).
