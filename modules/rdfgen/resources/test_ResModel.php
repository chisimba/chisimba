<?PHP

//change the RDFAPI_INCLUDE_DIR to your local settings
define("RDFAPI_INCLUDE_DIR", "/var/www/rdfapi-php/api/"); 
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");


// Some definitions
define('VCARD_NS', 'http://www.w3.org/2001/vcard-rdf/3.0#');
$personURI = "http://somewhere/JohnSmith";
$fullName = "John Smith";

// Create an empty Model 
$model = ModelFactory::getResModel(MEMMODEL); 

// Create the resources
$fullNameLiteral = $model->createLiteral($fullName);
$johnSmith = $model->createResource($personURI);
$vcard_FN= $model->createProperty(VCARD_NS.'FN');
$vcard_NICKNAME= $model->createProperty(VCARD_NS.'NICKNAME');

// Add the property 
$johnSmith->addProperty($vcard_FN, $fullNameLiteral); 

// Retrieve the John Smith vcard resource from the model 
$vCard = $model->createResource($personURI); 


var_dump($vCard);
// Retrieve the value of the FN property 
$statement = $vCard->getProperty($vcard_FN);
$value = $statement->getObject(); 

// Add two nickname properties to vcard
$literal1 = $model->createLiteral("Smithy");
$literal2 = $model->createLiteral("Adman"); 
$vCard->addProperty($vcard_NICKNAME, $literal1);
$vCard->addProperty($vcard_NICKNAME, $literal2); 


// List the nicknames
echo '<b>Known nicknames for '.$fullNameLiteral->getLabel().':</b><BR>';
foreach ($vCard->listProperties($vcard_NICKNAME) as $currentResource) 
{ 
     echo $currentResource->getLabelObject().'<BR>';
}; 


echo '<BR><b>Iterate over all subjects which having FN property:</b><br>'; 
// Iterate over all subjects which having FN property
$iter = $model->listSubjectsWithProperty($vcard_FN); 
for ($iter->rewind(); $iter->valid(); $iter->next())
{
$currentResource=$iter->current();
 echo $currentResource->getLabel().'<BR>';
}; 


// Create a bag 
$bag_smiths = $model->createBag(); 

$beckySmith = $model->createResource('http://somewhere/BeckySmith');
$beckySmithFN = $model->createLiteral('Becky Smith');
$beckySmith->addProperty($vcard_FN,$beckySmithFN );

// Add persons to bag
$bag_smiths->add($beckySmith);
$bag_smiths->add($johnSmith);


// Print out the full names of the members of the bag
echo '<BR><BR><b>Print out the full names of the members of the bag:</b></BR>'; 
foreach ($bag_smiths->getMembers() as $resResource)
{
    // Retrieve the value of the FN property 
	$statement = $resResource->getProperty($vcard_FN);
	echo $statement->getLabelObject().'<BR>'; 
};


echo '<BR><BR>All Statements as HTML table';
$model->writeAsHTMLTable();


var_dump($model);
?>
