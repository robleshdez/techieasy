<?php    
class TextClassifier {

   

    /**
     * Tokeniza el texto dividiéndolo en palabras y elimina las stop words.
     *
     * @param string $text El texto a analizar.
     * @return array Un arreglo con las palabras tokenizadas sin stop words.
     */
    private function tokenize(string $text): array {

     $stopWords = [
        'te','ti','el', 'la', 'los', 'las', 'y', 'de', 'en', 'a', 'para', 'con', 'por', 'que','q', 'un', 'una', 'al', 'del', 'se', 'es', 'fue', 'hace', 'como', 'más'
    ];
    
     
        // Convertir a minúsculas
        $text = strtolower($text);

        // Eliminar caracteres especiales
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $text);

        // Tokenizar el texto en palabras
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Normalizar las palabras y eliminar las stop words
        $tokens = array_diff($tokens, $stopWords);
        $tokens = array_map([$this, 'normalizeWord'], $tokens);
        $tokens = array_map([$this, 'lemmatizeWord'], $tokens);
        $mgrams = $this->generateNgrams($tokens, 1);
        $bigrams = $this->generateNgrams($tokens, 2);
        $trigrams = $this->generateNgrams($tokens, 3);
        $tokens = array_merge($mgrams, $bigrams, $trigrams);

        // Eliminar tokens vacíos
        return array_filter($tokens, function($word) {
            //print_r('palabra= '.!empty($word).'<br>');
            return !empty($word);
        });
    }

    /**
     * Normaliza las palabras (de plural a singular en casos comunes).
     *
     * @param string $word La palabra a normalizar.
     * @return string La palabra normalizada.
     */
    private function normalizeWord(string $word): string {

        $word = preg_replace('/([aeiou])\1{2,}$/', '$1', $word);
        $word = preg_replace('/(.)\1{2,}$/', '$1', $word);

        $pluralToSingular = [
        'on' => '',
        's' => '',
        'es' => '',  // Como 'ofertas' -> 'oferta'
        'ces' => 'z' // Como 'flores' -> 'flor'
    ];
        foreach ($pluralToSingular as $plural => $singular) {
            // Si la palabra termina en "plural", la convertimos a singular
            if (substr($word, -strlen($plural)) === $plural) {
                return substr($word, 0, -strlen($plural)) . $singular;
            }
        }
        return $word;
    }

    private function lemmatizeWord(string $word): string {
    // Lista básica de sufijos y sus transformaciones
    $suffixes = [
        'ando' => 'ar',   // Ejemplo: trabajando -> trabajar
        'iendo' => 'er',  // Ejemplo: comiendo -> comer
        'ar' => '', 
        'er' => '',
        'ir' => '',
        'ción' => '',     // Ejemplo: organización -> organiz
        'mente' => ''     // Ejemplo: rápidamente -> rápid
    ];

    foreach ($suffixes as $suffix => $replacement) {
        if (str_ends_with($word, $suffix)) {
            return substr($word, 0, -strlen($suffix)) . $replacement;
        }
    }

    return $word;
}

private function generateNgrams(array $tokens, int $n = 1): array {
    $ngrams = [];
    for ($i = 0; $i <= count($tokens) - $n; $i++) {
        $ngrams[] = implode(' ', array_slice($tokens, $i, $n));
    }
    print_r($ngrams);
    echo "<br>";
    return $ngrams;
}



    /**
     * Clasifica un texto según las palabras clave de los intents.
     *
     * @param string $text El texto a analizar.
     * @param array $intents Arreglo de intents con palabras clave.
     * @return string|null El ID del intent más acorde o null si no hay coincidencias.
     */
    public function intentsClassify(string $text, array $intents)
    {
        $textTokens = $this->tokenize($text);

        $bestMatchId = null;
        $maxScore = 0;

        // Recorremos los intents
        foreach ($intents as $intentId => $keywords) {
            // Tokenizamos las palabras clave del intent
            $keywordsTokens = $this->tokenize(implode(' ', $keywords));

            // Inicializamos un puntaje por coincidencias exactas
            $score = 0;

            // Contar coincidencias exactas de palabras
            $commonWords = array_intersect($textTokens, $keywordsTokens);
            $score += count($commonWords);

            // Añadir puntaje por coincidencias parciales usando Levenshtein
            foreach ($textTokens as $textWord) {
                foreach ($keywordsTokens as $keyword) {
                    if ($this->levenshteinDistance($textWord, $keyword) <= 2) { // Si la distancia es pequeña
                        $score += 1; // Puntaje por coincidencia parcial
                    }
                }
            
            }

            // Si el score es mayor al máximo, actualizamos el mejor match
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestMatchId = $intentId;
            }
            if ($maxScore < 1) { // Umbral arbitrario: ajusta según necesidad
                    //return null; // Sin coincidencias significativas
            }
        }

        return $bestMatchId;
    }

    /**
     * Calcula la distancia de Levenshtein para manejar coincidencias parciales.
     *
     * @param string $word1 Primera palabra.
     * @param string $word2 Segunda palabra.
     * @return int La distancia de Levenshtein.
     */
    private function levenshteinDistance(string $word1, string $word2): int
    {
        return levenshtein($word1, $word2);
    }
}
 
 