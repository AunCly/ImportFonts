<?php 

$bdd_host = '';
$bdd_name = '';
$bdd_user = '';
$bdd_password = '';
$ApiKey = 'your_api_key';

try {
    $bdd = new PDO('mysql:host='.$bdd_host.';dbname='.$bdd_name, $bdd_user, $bdd_password);
} 
catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

$fonts = file_get_contents("https://www.googleapis.com/webfonts/v1/webfonts?key=$ApiKey", true);
$fonts = json_decode($fonts, true);

$i = 0;
while ($i < count($fonts['items'])) {
	echo '<pre>';print_r($fonts['items'][$i]);echo '</pre>';
	$family = $fonts['items'][$i]['family'];
	$category = $fonts['items'][$i]['category'];
	$kind = $fonts['items'][$i]['kind'];
	$version = $fonts['items'][$i]['version'];
	$lastModified = $fonts['items'][$i]['lastModified'];

	$bdd->exec("INSERT INTO fonts (kind, family, category, version, lastModified) VALUES (".$bdd->quote($kind).", ".$bdd->quote($family).", ".$bdd->quote($category).", ".$bdd->quote($version).", ".$bdd->quote($lastModified).") ");

	$id_font = $bdd->LastInsertId();
	foreach ($fonts['items'][$i]['subsets'] as $key => $value) {
		$bdd->exec("INSERT INTO fonts_subsets (id_font, subset) VALUES (".$id_font.", ".$bdd->quote($value).")");
	}
	foreach ($fonts['items'][$i]['variants'] as $key => $value) {
		$bdd->exec("INSERT INTO fonts_files (id_font, variant, file) VALUES (".$id_font.", ".$bdd->quote($value).", ".$bdd->quote($fonts['items'][$i]['files'][$value]).") ");
	}
	$i++;
}

?>