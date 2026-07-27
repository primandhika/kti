/**
 * Calculate Levenshtein distance between two strings
 * Used for fuzzy string matching
 */
export function levenshteinDistance(str1, str2) {
    const len1 = str1.length;
    const len2 = str2.length;
    const matrix = [];

    // Initialize matrix
    for (let i = 0; i <= len1; i++) {
        matrix[i] = [i];
    }
    for (let j = 0; j <= len2; j++) {
        matrix[0][j] = j;
    }

    // Fill matrix
    for (let i = 1; i <= len1; i++) {
        for (let j = 1; j <= len2; j++) {
            const cost = str1[i - 1] === str2[j - 1] ? 0 : 1;
            matrix[i][j] = Math.min(
                matrix[i - 1][j] + 1,      // deletion
                matrix[i][j - 1] + 1,      // insertion
                matrix[i - 1][j - 1] + cost // substitution
            );
        }
    }

    return matrix[len1][len2];
}

/**
 * Calculate similarity score between two strings (0-1)
 */
export function similarityScore(str1, str2) {
    const longer = str1.length > str2.length ? str1 : str2;
    const shorter = str1.length > str2.length ? str2 : str1;

    if (longer.length === 0) return 1.0;

    const distance = levenshteinDistance(longer.toLowerCase(), shorter.toLowerCase());
    return (longer.length - distance) / longer.length;
}

/**
 * Find best matches for a search term from a list of candidates
 * Returns array of matches sorted by similarity score
 */
export function findBestMatches(searchTerm, candidates, minScore = 0.6, maxResults = 3) {
    if (!searchTerm || !candidates || candidates.length === 0) {
        return [];
    }

    const searchLower = searchTerm.toLowerCase().trim();

    // Calculate similarity for each candidate
    const scored = candidates
        .map(candidate => {
            const candidateLower = candidate.toLowerCase().trim();

            // Exact match gets perfect score
            if (candidateLower === searchLower) {
                return { text: candidate, score: 1.0 };
            }

            // Check if it's a substring match (partial match gets high score)
            if (candidateLower.includes(searchLower) || searchLower.includes(candidateLower)) {
                return { text: candidate, score: 0.9 };
            }

            // Calculate fuzzy match score
            const score = similarityScore(searchTerm, candidate);
            return { text: candidate, score };
        })
        .filter(item => item.score >= minScore && item.text.toLowerCase() !== searchLower)
        .sort((a, b) => b.score - a.score)
        .slice(0, maxResults);

    return scored;
}

/**
 * Normalize Indonesian text for better matching
 * Handles common typos and variations
 */
export function normalizeText(text) {
    return text
        .toLowerCase()
        .trim()
        // Remove extra spaces
        .replace(/\s+/g, ' ')
        // Common Indonesian typos
        .replace(/ng+/g, 'ng')
        .replace(/ny+/g, 'ny');
}

/**
 * Extract searchable terms from menu items (including variants)
 */
export function extractSearchableTerms(menus) {
    const terms = new Set();

    menus.forEach(menu => {
        // Add menu name
        terms.add(menu.name);

        // Add sub category
        if (menu.sub_kategori) {
            terms.add(menu.sub_kategori);
        }

        // Add variants
        if (menu.varians && menu.varians.length > 0) {
            menu.varians.forEach(varian => {
                terms.add(varian.nama_varian);
                // Also add combined: "menu - variant"
                terms.add(`${menu.name} ${varian.nama_varian}`);
            });
        }
    });

    return Array.from(terms);
}
