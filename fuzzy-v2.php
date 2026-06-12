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

function normalizeString($str)
{
    $unwanted_array = [
        'Š' => 'S',
        'š' => 's',
        'Ž' => 'Z',
        'ž' => 'z',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
    ];

    return strtr($str, $unwanted_array);
}

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

function getWeight($tokenLength)
{
    // suite de 2 -> +0.5, suite de 3 -> +1, suite de 4 -> +1.5, suite de 5 -> +2, etc.
    if ($tokenLength <= 1) {
        return $tokenLength; // longueur 1 = +1 point
    }

    // longueur 2 = 2 + 0.5 = 2.5
    // longueur 3 = 3 + 1 = 4
    // longueur 4 = 4 + 1.5 = 5.5
    // longueur 5 = 5 + 2 = 7
    // etc.
    return $tokenLength + (($tokenLength - 1) * 0.5);
}

function scoreMedecin($medecin, $query)
{
    // Normalisation et minuscule pour ignorer la casse
    $medecinNormalized = strtolower(normalizeString($medecin));
    $queryNormalized = strtolower(normalizeString($query));

    $tokens = getAllSubstrings($queryNormalized);
    $score = 0;

    foreach ($tokens as $token) {
        if (preg_match('/'.preg_quote($token, '/').'/', $medecinNormalized)) {
            $score += getWeight(strlen($token));
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
            $results[] = ['name' => $medecin, 'score' => round($score, 1)];
        }
    }
    usort($results, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return array_slice($results, 0, $limit);
}

// Test
$query = $argv[1] ?? 'Hugo mone';
$results = searchMedecins($medecins, $query, 5);

echo "Top 5 résultats pour '$query' :\n";
foreach ($results as $index => $result) {
    echo ($index + 1).'. '.$result['name'].' (score: '.$result['score'].")\n";
}
