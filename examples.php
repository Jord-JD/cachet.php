<?php

require_once 'vendor/autoload.php';

use \JordJD\CachetPHP\Factories\CachetInstanceFactory;

$cachetInstance = CachetInstanceFactory::create('http://cachet.test/api/v1/', 'mm634LcVtLDHlsRWARm0');

// Check if Cachet instance is working correctly
if ($cachetInstance->isWorking()) {
    echo "\n";
    echo '*** Cachet instance working fine! ***';
    echo "\n";
}

// Add component
echo "\n";
echo '*** Add Component ***';
echo "\n";
$componentDetails = ['name' => 'Test Component '.rand(1, 99999), 'status' => 1];

$component = $cachetInstance->createComponent($componentDetails);

echo $component->id.' - '.$component->name.' - '.$component->description.' - '.$component->status;
echo "\n";

// Add incident
echo "\n";
echo '*** Add Incident ***';
echo "\n";
$incidentDetails = ['name' => 'Test Incident '.rand(1, 99999), 'message' => 'Incident message '.rand(1, 99999), 'status' => 1, 'visible' => 1,
    'component_id' => $component->id, 'component_status' => 1];

$incident = $cachetInstance->createIncident($incidentDetails);

echo $incident->id.' - '.$incident->name.' - '.$incident->status.' - '.$incident->visible;
echo "\n";

// Get incident by id
$incident = $cachetInstance->getIncidentById($incident->id);

// Add incident update
echo "\n";
echo '*** Add Incident Update (Cachet 2.4.0 or above required) ***';
echo "\n";
$incidentUpdateDetails = ['status' => 2, 'message' => 'Test incident update '.rand(1, 99999), 'component_status' => 2];

$incidentUpdate = $incident->createIncidentUpdate($incidentUpdateDetails);

echo $incidentUpdate->id.' - '.$incidentUpdate->incident_id.' - '.$incidentUpdate->status.' - '.$incidentUpdate->message;
echo "\n";

// Update incident update
echo "\n";
echo '*** Update Component Status via Incident Update (Cachet 2.4.0 or above required) ***';
echo "\n";
$incidentUpdate->component_status = 3;
$incidentUpdate->save();

// Get components
$components = $cachetInstance->getAllComponents();

// Display components
echo "\n";
echo '*** Components ***';
echo "\n";
foreach ($components as $component) {
    echo $component->id.' - '.$component->name.' - '.$component->description.' - '.$component->status;
    echo "\n";
}

// Get components sorted by name ascending
$components = $cachetInstance->getAllComponents('name', 'asc');

// Display components sorted by name ascending
echo "\n";
echo '*** Components (sorted by name ascending) ***';
echo "\n";
foreach ($components as $component) {
    echo $component->id.' - '.$component->name.' - '.$component->description.' - '.$component->status;
    echo "\n";
}

// Get components sorted by name descending
$components = $cachetInstance->getAllComponents('name', 'desc');

// Display components sorted by name descending
echo "\n";
echo '*** Components (sorted by name descending) ***';
echo "\n";
foreach ($components as $component) {
    echo $component->id.' - '.$component->name.' - '.$component->description.' - '.$component->status;
    echo "\n";
}

// Get incidents
$incidents = $cachetInstance->getAllIncidents();

// Display incidents
echo "\n";
echo '*** Incidents ***';
echo "\n";
foreach ($incidents as $incident) {
    echo $incident->id.' - '.$incident->name.' - '.$incident->message.' - '.$incident->human_status;
    echo "\n";
}

// Get incident updates for first incident (Cachet 2.4.0 or above required)
$incidentUpdates = $incidents[0]->getAllIncidentUpdates();

// Display incident updates
echo "\n";
echo '*** Incident updates (for Incident ID '.$incidentUpdates[0]->id.') ***';
echo "\n";
foreach ($incidentUpdates as $incidentUpdate) {
    echo $incidentUpdate->id.' - '.$incidentUpdate->message;
    echo "\n";
}

// Get metrics
$metrics = $cachetInstance->getAllMetrics();

// Display metrics
echo "\n";
echo '*** Metrics ***';
echo "\n";
foreach ($metrics as $metric) {
    echo $metric->id.' - '.$metric->name;
    echo "\n";
}

// Get metric points for first metric
$metricPoints = $metrics[0]->getAllMetricPoints();

// Display metric points
echo "\n";
echo '*** Metric points (for Metric ID '.$metrics[0]->id.') ***';
echo "\n";
foreach ($metricPoints as $metricPoint) {
    echo $metricPoint->id.' - Created at: '.$metricPoint->created_at.' - Updated at: '.$metricPoint->updated_at.' - Value: '.$metricPoint->value;
    echo "\n";
}

// Get subscribers
$subscribers = $cachetInstance->getAllSubscribers();

// Display subscribers
echo "\n";
echo '*** Subscribers ***';
echo "\n";
foreach ($subscribers as $subscriber) {
    echo $subscriber->id.' - '.$subscriber->email;
    echo "\n";
}
