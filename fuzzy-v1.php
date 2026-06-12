<?php

$medecins = [
    'Dr Jean Dupont',
    'Dr Sophie Moreau',
    'Dr Marc Lefèvre',
    'Dr Claire Bernard',
    'Dr Antoine Girard',
    'Dr Élise Rousseau',
    'Dr Nicolas Lambert',
    'Dr Camille Dubois',
    'Dr Thomas Petit',
    'Dr Laura Robert',
    'Dr Julien Richard',
    'Dr Julie Durand',
    'Dr Pierre Garcia',
    'Dr Marie Martin',
    'Dr Lucas Leroy',
    'Dr Chloé Simon',
    'Dr Alexandre Michel',
    'Dr Emma Fournier',
    'Dr Maxime Chevalier',
    'Dr Léa François',
    'Dr Hugo Lemoine',
    'Dr Manon Roux',
    'Dr Paul Garnier',
    'Dr Sarah Moulin',
    'Dr Benoît Rey',
    'Dr Alice Denis',
    'Dr Romain Blanc',
    'Dr Lucie Guérin',
    'Dr Olivier Morin',
    'Dr Inès Gauthier',
    'Dr Jérôme Perrin',
    'Dr Margaux Robin',
    'Dr Stéphane Clement',
    'Dr Pauline Fabre',
    'Dr David Barbier',
    'Dr Amandine Arnaud',
    'Dr Vincent Martel',
    'Dr Céline Lacroix',
    'Dr Mathieu Masson',
    'Dr Océane Picard',
    'Dr Guillaume Gérard',
    'Dr Justine Leclerc',
    'Dr Florian Hervé',
    'Dr Eva Philippe',
    'Dr Sébastien Bertrand',
    'Dr Morgane Roche',
    'Dr Adrien Boyer',
    'Dr Salomé Schmitt',
    'Dr Thibault Louis',
    'Dr Laurie André',
];

function getAllSubstrings($word)
{
    $length = strlen($word);
    $substrings = [];
    for ($i = 0; $i < $length; $i++) {
        for ($j = $i + 1; $j <= $length; $j++) {
            $substrings[] = substr($word, $i, $j - $i);
        }
    }

    return array_unique($substrings);
}

function scoreMedecin($medecin, $query)
{
    $tokens = getAllSubstrings($query);
    $score = 0;
    foreach ($tokens as $token) {
        if (preg_match('/'.preg_quote($token, '/').'/', $medecin)) {
            $score += strlen($token);
        }
    }

    return $score;
}

function searchMedecins($medecins, $query, $limit = 5)
{
    $results = [];
    foreach ($medecins as $medecin) {
        $score = scoreMedecin($medecin, $query);
        if ($score > 0) {
            $results[] = ['name' => $medecin, 'score' => $score];
        }
    }
    usort($results, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return array_slice($results, 0, $limit);
}

// Test avec 'betran'
$query = 'antine Girod';
$results = searchMedecins($medecins, $query, 10);

echo "Top résultats pour '$query' :\n";
foreach ($results as $index => $result) {
    echo ($index + 1).'. '.$result['name'].' (score: '.$result['score'].")\n";
}
