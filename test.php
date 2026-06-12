<?php

declare(strict_types=1);

require './data.php';

/**
 * A fuzzy search engine that matches strings based on substring weighting.
 *
 * The engine normalizes strings (removes accents, special characters) and scores
 * matches by analyzing all possible substrings of the query against the target strings.
 *
 * @example
 * $engine = new SearchEngine(['John Doe', 'Jane Smith']);
 * $results = $engine->search('Jon Doe', 3);
 */
class SearchEngine
{
    /**
     * @var array<int, string> The dataset to search within
     */
    private array $data = [];

    /**
     * @param  array<int, string>  $data  Initial dataset for searching
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Replaces the entire dataset with a new one.
     *
     * @param  array<int, string>  $data  New dataset
     * @return self Returns self for method chaining
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns the current dataset.
     *
     * @return array<int, string>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Searches the dataset for items matching the query.
     *
     * @param  string  $query  The search query
     * @param  positive-int  $limit  Maximum number of results to return (default: 5)
     * @return array<int, array{name: string, cleaned_name: string, score: float, max_possible: float, percentage: float}>
     */
    public function search(string $query, int $limit = 5): array
    {
        $cleanedQuery = $this->cleanString($query);
        $queryWords = $this->splitIntoWords($cleanedQuery);

        if (empty($queryWords)) {
            return [];
        }

        $results = [];

        foreach ($this->data as $item) {
            $result = $this->computeMatchScore($item, $queryWords);

            if ($result !== null) {
                $results[] = $result;
            }
        }

        $this->sortResultsByRelevance($results);

        return array_slice($results, 0, $limit);
    }

    /**
     * Computes the match score between an item and the query words.
     *
     * @param  string  $item  The item to evaluate
     * @param  array<int, string>  $queryWords  Array of cleaned query words
     * @return array{name: string, cleaned_name: string, score: float, max_possible: float, percentage: float}|null
     */
    private function computeMatchScore(string $item, array $queryWords): ?array
    {
        $cleanedItem = $this->cleanString($item);
        $itemWords = $this->splitIntoWords($cleanedItem);

        if (empty($itemWords)) {
            return null;
        }

        $totalScore = 0.0;
        $totalMaxPossible = 0.0;
        $totalPercentage = 0.0;
        $wordCount = 0;

        foreach ($queryWords as $queryWord) {
            $bestMatch = $this->findBestMatchingWord($queryWord, $itemWords);

            $totalScore += $bestMatch['score'];
            $totalMaxPossible += $bestMatch['max_possible'];
            $totalPercentage += $bestMatch['percentage'];
            $wordCount++;
        }

        if ($totalScore === 0.0) {
            return null;
        }

        $globalPercentage = $wordCount > 0 ? round($totalPercentage / $wordCount, 2) : 0.0;

        return [
            'name' => $item,
            'cleaned_name' => $cleanedItem,
            'score' => $totalScore,
            'max_possible' => $totalMaxPossible,
            'percentage' => $globalPercentage,
        ];
    }

    /**
     * Finds the best matching word from the item words for a given query word.
     *
     * @param  string  $queryWord  The query word to match
     * @param  array<int, string>  $itemWords  Array of item words to search against
     * @return array{score: float, max_possible: float, percentage: float}
     */
    private function findBestMatchingWord(string $queryWord, array $itemWords): array
    {
        $bestScore = 0.0;
        $bestMaxPossible = 0.0;
        $bestPercentage = 0.0;

        foreach ($itemWords as $itemWord) {
            $score = $this->calculateWordScore($queryWord, $itemWord);
            $maxPossible = $this->getMaxPossibleScore($itemWord);

            $queryLength = max(strlen($queryWord), 1);
            $wordLength = max(strlen($itemWord), 1);

            $percentage = ($score / $queryLength) * 100 / ($maxPossible / $wordLength);
            $percentage = min(round($percentage, 2), 100.0);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMaxPossible = $maxPossible;
                $bestPercentage = $percentage;
            }
        }

        return [
            'score' => $bestScore,
            'max_possible' => $bestMaxPossible,
            'percentage' => $bestPercentage,
        ];
    }

    /**
     * Calculates the similarity score between two words using substring weighting.
     *
     * @param  string  $word1  First word (typically from query)
     * @param  string  $word2  Second word (typically from dataset)
     * @return float Score representing the match quality
     */
    private function calculateWordScore(string $word1, string $word2): float
    {
        $cleanedWord1 = strtolower($this->cleanString($word1));
        $cleanedWord2 = strtolower($this->cleanString($word2));

        $substrings = $this->getAllSubstrings($cleanedWord1);
        $score = 0.0;

        foreach ($substrings as $substring) {
            if (preg_match('/'.preg_quote($substring, '/').'/', $cleanedWord2)) {
                $score += $this->getWeight(strlen($substring));
            }
        }

        return $score;
    }

    /**
     * Calculates the maximum possible score a word can achieve when matching against itself.
     *
     * @param  string  $word  The word to calculate maximum score for
     * @return float The maximum possible score
     */
    private function getMaxPossibleScore(string $word): float
    {
        $normalizedWord = strtolower($this->cleanString($word));
        $substrings = $this->getAllSubstrings($normalizedWord);
        $maxScore = 0.0;

        foreach ($substrings as $substring) {
            if (preg_match('/'.preg_quote($substring, '/').'/', $normalizedWord)) {
                $maxScore += $this->getWeight(strlen($substring));
            }
        }

        return round($maxScore, 1);
    }

    /**
     * Splits a cleaned string into individual words.
     *
     * @param  string  $string  The cleaned string to split
     * @return array<int, string> Array of non-empty words
     */
    private function splitIntoWords(string $string): array
    {
        $words = explode(' ', $string);

        return array_filter($words, fn (string $word): bool => $word !== '');
    }

    /**
     * Removes special characters from a string, keeping only letters, numbers, spaces, hyphens and apostrophes.
     *
     * @param  string  $string  The input string
     * @return string The string with special characters replaced by spaces
     */
    private function replaceSpecialChars(string $string): string
    {
        return preg_replace('/[^a-zA-Z0-9\s\'-]/u', ' ', $string);
    }

    /**
     * Normalizes a string by removing accents and diacritics.
     *
     * @param  string  $string  The input string with potential accents
     * @return string The normalized string without accents
     */
    private function normalizeString(string $string): string
    {
        $mapping = [
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

        return strtr($string, $mapping);
    }

    /**
     * Cleans a string by normalizing accents and removing special characters.
     *
     * @param  string  $string  The input string
     * @return string The cleaned string
     */
    private function cleanString(string $string): string
    {
        $normalized = $this->normalizeString($string);
        $cleaned = $this->replaceSpecialChars($normalized);

        return preg_replace('/\s+/', ' ', trim($cleaned));
    }

    /**
     * Generates all possible non-empty substrings of a word.
     *
     * @param  string  $word  The input word
     * @return array<int, string> Array of unique substrings
     */
    private function getAllSubstrings(string $word): array
    {
        $length = strlen($word);
        $substrings = [];

        for ($i = 0; $i < $length; $i++) {
            for ($j = $i + 1; $j <= $length; $j++) {
                $substrings[] = substr($word, $i, $j - $i);
            }
        }

        return array_values(array_unique($substrings));
    }

    /**
     * Calculates the weight of a substring based on its length.
     *
     * Longer substrings get exponentially more weight to favor exact matches.
     *
     * @param  int  $length  The length of the substring
     * @return float The calculated weight
     */
    private function getWeight(int $length): float
    {
        if ($length <= 1) {
            return (float) $length;
        }

        return $length + (($length - 1) * 0.5);
    }

    /**
     * Sorts results by relevance (percentage descending, then score descending).
     *
     * @param  array<int, array{percentage: float, score: float}>  $results  Reference to results array to sort
     */
    private function sortResultsByRelevance(array &$results): void
    {
        usort($results, function (array $a, array $b): int {
            if ($b['percentage'] === $a['percentage']) {
                return $b['score'] <=> $a['score'];
            }

            return $b['percentage'] <=> $a['percentage'];
        });
    }
}

$searchEngine = new SearchEngine($chansonsCelebres);

$query = $argv[1] ?? 'Lucas Leroy';
$results = $searchEngine->search($query, 5);

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
