<?php

$medecins = [
    'Jean Dupont',
    'Sophie Moreau',
    'Marc Lefèvre',
    'Claire Bernard',
    'Antoine Girard',
    'Élise Rousseau',
    'Nicolas Lambert',
    'Camille Dubois',
    'Thomas Petit',
    'Laura Robert',
    'Julien Richard',
    'Julie Durand',
    'Pierre Garcia',
    'Marie Martin',
    'Lucas Leroy',
    'Chloé Simon',
    'Alexandre Michel',
    'Emma Fournier',
    'Maxime Chevalier',
    'Léa François',
    'Hugo Lemoine',
    'Manon Roux',
    'Paul Garnier',
    'Sarah Moulin',
    'Benoît Rey',
    'Alice Denis',
    'Romain Blanc',
    'Lucie Guérin',
    'Olivier Morin',
    'Inès Gauthier',
    'Jérôme Perrin',
    'Margaux Robin',
    'Stéphane Clement',
    'Pauline Fabre',
    'David Barbier',
    'Amandine Arnaud',
    'Vincent Martel',
    'Céline Lacroix',
    'Mathieu Masson',
    'André Kani',
    'Océane Picard',
    'Guillaume Gérard',
    'Justine Leclerc',
    'Florian Hervé',
    'Eva Philippe',
    'Sébastien Bertrand',
    'Morgane Roche',
    'Adrien Boyer',
    'Salomé Schmitt',
    'Thibault Louis',
    'Laurie André',
];

$specialitesMedicales = [
    'Allergologie',
    'Anesthésiologie',
    'Andrologie',
    'Angéiologie',
    'Addictologie',
    'Anatomopathologie',
    'Audiologie',
    'Bactériologie',
    'Biochimie médicale',
    'Biologie médicale',
    'Cardiologie',
    'Chirurgie cardiaque',
    'Chirurgie digestive',
    'Chirurgie endocrinienne',
    'Chirurgie générale',
    'Chirurgie maxillo-faciale',
    'Chirurgie oncologique',
    'Chirurgie orthopédique',
    'Chirurgie pédiatrique',
    'Chirurgie plastique',
    'Chirurgie thoracique',
    'Chirurgie vasculaire',
    'Chirurgie viscérale',
    'Coloproctologie',
    'Cytologie',
    'Dermato-allergologie',
    'Dermatologie',
    'Diabétologie',
    'Échographie',
    'Embryologie',
    'Endocrinologie',
    'Épidémiologie',
    'Épithéliologie',
    'Gastro-entérologie',
    'Gériatrie',
    'Gynécologie médicale',
    'Gynécologie obstétrique',
    'Hématologie',
    'Hématologie biologique',
    'Hépatologie',
    'Histologie',
    'Immunologie',
    'Dentisterie',
    'Infectiologie',
    'Imagerie médicale',
    'Kinésithérapie',
    'Maladies infectieuses',
    'Mammologie',
    "Médecine d'urgence",
    'Médecine de la reproduction',
    'Médecine du sport',
    'Médecine du travail',
    'Médecine esthétique',
    'Médecine interne',
    'Médecine légale',
    'Médecine palliative',
    'Médecine physique',
    'Médecine préventive',
    'Médecine vasculaire',
    'Microbiologie',
    'Néonatologie',
    'Néphrologie',
    'Neurochirurgie',
    'Neurologie',
    'Neuroradiologie',
    'Nutrition',
    'Obstétrique',
    'Oncologie médicale',
    'Oncologie radiothérapie',
    'Ophtalmologie',
    'Orthodontie',
    'Orthophonie',
    'Orthopédie',
    'Oto-rhino-laryngologie (ORL)',
    'Parasitologie',
    'Pédiatrie',
    'Pneumologie',
    'Podologie',
    'Proctologie',
    'Psychiatrie',
    'Psychiatrie infanto-juvénile',
    'Psychologie clinique',
    'Radiologie',
    'Radiothérapie',
    'Réanimation médicale',
    'Rééducation fonctionnelle',
    'Rhumatologie',
    'Santé publique',
    'Sénologie',
    'Stomatologie',
    'Toxicologie',
    'Transplantation',
    'Traumatologie',
    'Urologie',
    'Virologie',
    'Acupuncture',
    'Homéopathie',
    'Mésothérapie',
    'Ostéopathie',
    'Phlébologie',
    'Sexologie',
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
    if ($tokenLength <= 1) {
        return $tokenLength;
    }

    return $tokenLength + (($tokenLength - 1) * 0.5);
}

function getMaxPossibleScore($word)
{
    $wordNormalized = strtolower(normalizeString($word));
    $tokens = getAllSubstrings($wordNormalized);
    $maxScore = 0;

    foreach ($tokens as $token) {
        if (preg_match('/'.preg_quote($token, '/').'/', $wordNormalized)) {
            $maxScore += getWeight(strlen($token));
        }
    }

    return round($maxScore, 1);
}

function scoreMedecin($medecin, $query)
{
    $medecinNormalized = strtolower(normalizeString($medecin));
    $queryNormalized = strtolower(normalizeString($query));

    $tokens = getAllSubstrings($queryNormalized);
    $score = 0;

    foreach ($tokens as $token) {
        if (preg_match('/'.preg_quote($token, '/').'/', $medecinNormalized)) {
            $score += getWeight(strlen($token));
        }
    }

    return round($score, 1);
}

function searchMedecins($medecins, $query, $limit = 5)
{
    $results = [];

    foreach ($medecins as $medecin) {
        $score = scoreMedecin($medecin, $query);
        if ($score > 0) {
            $maxPossible = getMaxPossibleScore($medecin);
            $percentage = ($score * 100) / $maxPossible;
            // $percentage = ($score / strlen($query)) * 100 / ($maxPossible / strlen($medecin)); // AVEC PERTINENCE

            $results[] = [
                'name' => $medecin,
                'score' => $score,
                'max_possible' => $maxPossible,
                'percentage' => round($percentage, 2),
            ];
        }
    }

    usort($results, function ($a, $b) {
        return $b['percentage'] <=> $a['percentage'];
    });

    return array_slice($results, 0, $limit);
}

// Test
$query = $argv[1] ?? 'Lucas Leroy';
$results = searchMedecins($medecins, $query, 5);

echo "Top 5 résultats pour '$query' :\n";
echo str_repeat('=', 80)."\n";
foreach ($results as $index => $result) {
    $isMax = ($result['percentage'] == 100) ? ' - [MAX POSSIBLE]' : '';
    echo ($index + 1).'. '.$result['name'].
        ' (score: '.$result['score'].
        ' / max: '.$result['max_possible'].
        ') - Pertinence: '.$result['percentage'].'%'.
        $isMax."\n";
}
