<?php

namespace App\Services;

class CatchAnalysisSuggestionService
{
    private $weatherSuggestions = [
        'clear' => [
            'feedback' => "Clear conditions provide excellent visibility for fishing. Fish may be more cautious due to increased light penetration, but early morning and late afternoon can be particularly productive.",
            'recommendations' => "Fish during dawn and dusk for best results\nTarget deeper waters during midday\nUse lighter, more natural-colored lures\nConsider using longer, lighter leaders\nFocus on shaded areas near structure",
            'sustainability_rating' => 'Good'
        ],
        'partly cloudy' => [
            'feedback' => "Partly cloudy conditions offer an ideal mix of light and cover. These conditions can trigger active feeding as fish feel more secure with intermittent cloud cover.",
            'recommendations' => "Take advantage of cloud passages for increased activity\nVary retrieval speeds with light changes\nUse both light and dark-colored lures\nFish multiple depths as fish may move up and down\nPay attention to shadow lines",
            'sustainability_rating' => 'Good'
        ],
        'cloudy' => [
            'feedback' => "Cloudy conditions typically provide excellent fishing opportunities. Overcast skies reduce light penetration, making fish feel more secure and likely to feed throughout the water column.",
            'recommendations' => "Fish the full water column\nUse darker lures for better silhouettes\nTry faster retrieval speeds\nFocus on areas where fish normally hold\nConsider using slightly larger lures",
            'sustainability_rating' => 'Good'
        ],
        'light rain' => [
            'feedback' => "Light rain can enhance fishing conditions by disturbing the water surface and washing insects and other food into the water. Fish often become more active during light rain.",
            'recommendations' => "Focus on areas where rainwater enters the water\nUse surface lures to mimic struggling insects\nUse darker colored lures\nPay attention to changes in water clarity\nConsider using scented baits",
            'sustainability_rating' => 'Good'
        ],
        'moderate rain' => [
            'feedback' => "Moderate rain presents both challenges and opportunities. While fishing becomes more challenging, increased water movement and food washing into the water can trigger feeding activity.",
            'recommendations' => "Use larger, more visible lures\nFocus on protected areas\nWatch for water color changes\nConsider safety precautions\nTarget areas near drainage inflows",
            'sustainability_rating' => 'Concerning'
        ],
        'heavy rain' => [
            'feedback' => "Heavy rain significantly impacts fishing conditions and safety. Water clarity may be reduced, and fish behavior can become erratic. Exercise caution and consider postponing fishing activity.",
            'recommendations' => "Prioritize safety in difficult conditions\nUse bright or noisy lures if fishing\nFocus on protected areas\nMonitor water levels and clarity\nBe prepared for rapid condition changes",
            'sustainability_rating' => 'Critical'
        ],
        'thunderstorm' => [
            'feedback' => "Thunderstorm conditions present serious safety risks. Fishing should be postponed until conditions improve. Lightning and strong winds make water activities extremely dangerous.",
            'recommendations' => "Cease fishing activity immediately\nSeek appropriate shelter\nMonitor weather updates\nWait at least 30 minutes after last thunder\nCheck equipment for damage before resuming",
            'sustainability_rating' => 'Critical'
        ]
    ];

    private $quantitySuggestions = [
        'low' => [
            'feedback' => "The catch quantity is within sustainable limits. This approach helps maintain fish populations and ecosystem balance.",
            'recommendations' => "Continue practicing selective harvesting\nConsider catch and release for some fish\nDocument catch locations for future reference\nMonitor seasonal patterns\nShare successful conservation practices",
            'sustainability_rating' => 'Good'
        ],
        'medium' => [
            'feedback' => "The catch quantity is moderate but requires attention to ensure sustainability. Consider implementing additional conservation measures.",
            'recommendations' => "Implement size-based selection criteria\nRotate fishing locations to prevent overfishing\nConsider reducing frequency of catches\nMonitor local fishing regulations\nDocument catch data for trend analysis",
            'sustainability_rating' => 'Concerning'
        ],
        'high' => [
            'feedback' => "The catch quantity is high and may impact local fish populations. Immediate action is needed to ensure sustainable practices.",
            'recommendations' => "Significantly reduce catch quantities\nImplement strict catch and release practices\nConsider alternative fishing locations\nConsult with local conservation authorities\nShare data with conservation groups",
            'sustainability_rating' => 'Critical'
        ]
    ];

    private $sizeSuggestions = [
        'small' => [
            'feedback' => "The average catch size indicates juvenile or small species. This requires careful monitoring to ensure population sustainability.",
            'recommendations' => "Consider using larger mesh sizes or hooks\nRelease undersized fish immediately\nRecord size data for population monitoring\nAvoid areas with high concentrations of small fish\nReport unusual patterns of small catches",
            'sustainability_rating' => 'Concerning'
        ],
        'medium' => [
            'feedback' => "The average catch size suggests mature fish within the typical range. This is generally indicative of a healthy population.",
            'recommendations' => "Continue current gear selection practices\nMonitor size trends over time\nRotate fishing locations\nDocument successful fishing methods\nShare size data with local authorities",
            'sustainability_rating' => 'Good'
        ],
        'large' => [
            'feedback' => "Large average catch size indicates successful targeting of mature fish. While positive, it's important to ensure breeding population sustainability.",
            'recommendations' => "Consider releasing some larger breeding fish\nDocument locations of large catches\nUse appropriate gear for large fish\nPractice careful handling techniques\nShare data with research institutions",
            'sustainability_rating' => 'Good'
        ]
    ];

    private $weightAnalysis = [
        'light' => [
            'feedback' => "The total catch weight is relatively light, suggesting sustainable harvesting levels.",
            'recommendations' => "Monitor weight trends over time\nConsider if gear is appropriate for target species\nDocument environmental conditions\nVerify if catch weight aligns with local limits\nConsider seasonal variations",
            'sustainability_rating' => 'Good'
        ],
        'moderate' => [
            'feedback' => "The total catch weight is moderate and requires monitoring to maintain sustainability.",
            'recommendations' => "Compare with local catch limits\nConsider reducing frequency of catches\nDocument weight distribution\nMonitor impact on local ecosystem\nShare data with fishery managers",
            'sustainability_rating' => 'Concerning'
        ],
        'heavy' => [
            'feedback' => "The total catch weight is significant and may require adjustment to ensure long-term sustainability.",
            'recommendations' => "Review local fishing regulations\nConsider reducing catch volume\nImplement catch rotation system\nMonitor ecosystem impact\nConsult with conservation authorities",
            'sustainability_rating' => 'Critical'
        ]
    ];

    private $speciesGuidelines = [
        'tilapia' => [
            'feedback' => "Tilapia are generally hardy and fast-growing, but require monitoring to prevent overcrowding.",
            'recommendations' => "Monitor for signs of overcrowding\nCheck for proper size distribution\nConsider ecosystem impact\nDocument breeding patterns\nFollow local harvest guidelines",
            'sustainability_rating' => 'Good'
        ],
        'carp' => [
            'feedback' => "Carp can impact local ecosystems. Careful management is needed to maintain balance.",
            'recommendations' => "Monitor impact on vegetation\nCheck for proper size distribution\nDocument population changes\nFollow local control measures\nReport unusual activities",
            'sustainability_rating' => 'Concerning'
        ],
        'catfish' => [
            'feedback' => "Catfish populations are generally stable but require monitoring of breeding populations.",
            'recommendations' => "Protect breeding areas\nMonitor size distribution\nDocument catch locations\nFollow seasonal guidelines\nReport unusual patterns",
            'sustainability_rating' => 'Good'
        ],
    ];

    public function getSuggestions(array $catchData): array
    {
        $weather = strtolower(trim($catchData['weather_conditions'] ?? 'unknown'));
        $quantity = $this->categorizeQuantity($catchData['quantity'] ?? 0);
        $size = $this->categorizeSize($catchData['average_size'] ?? 0);
        $weight = $this->categorizeWeight($catchData['total_weight'] ?? 0);
        $species = strtolower(trim($catchData['fish_species'] ?? 'unknown'));

        $weatherSuggestion = $this->weatherSuggestions[$weather] ?? $this->getDefaultSuggestions();
        $quantitySuggestion = $this->quantitySuggestions[$quantity];
        $sizeSuggestion = $this->sizeSuggestions[$size];
        $weightSuggestion = $this->weightAnalysis[$weight];
        $speciesSuggestion = $this->speciesGuidelines[$species] ?? $this->getDefaultSpeciesGuidelines();

        return [
            'feedback' => $this->combineAllFeedback([
                $weatherSuggestion,
                $quantitySuggestion,
                $sizeSuggestion,
                $weightSuggestion,
                $speciesSuggestion
            ]),
            'recommendations' => $this->combineAllRecommendations([
                $weatherSuggestion,
                $quantitySuggestion,
                $sizeSuggestion,
                $weightSuggestion,
                $speciesSuggestion
            ]),
            'sustainability_rating' => $this->determineOverallSustainabilityRating([
                $weatherSuggestion,
                $quantitySuggestion,
                $sizeSuggestion,
                $weightSuggestion,
                $speciesSuggestion
            ])
        ];
    }

    private function categorizeQuantity(int $quantity): string
    {
        if ($quantity <= 5) {
            return 'low';
        } elseif ($quantity <= 15) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    private function categorizeSize(float $averageSize): string
    {
        if ($averageSize <= 0.5) {
            return 'small';
        } elseif ($averageSize <= 2) {
            return 'medium';
        } else {
            return 'large';
        }
    }

    private function categorizeWeight(float $totalWeight): string
    {
        if ($totalWeight <= 10) {
            return 'light';
        } elseif ($totalWeight <= 30) {
            return 'moderate';
        } else {
            return 'heavy';
        }
    }

    private function getDefaultSuggestions(): array
    {
        return [
            'feedback' => "Weather conditions were not specified. General fishing practices and conservation guidelines should be followed.",
            'recommendations' => "Monitor local weather conditions\nFollow local fishing guidelines\nPractice sustainable fishing methods\nKeep detailed records of catches\nConsider joining local conservation efforts",
            'sustainability_rating' => 'Concerning'
        ];
    }

    private function getDefaultSpeciesGuidelines(): array
    {
        return [
            'feedback' => "Species-specific guidelines are not available. Follow general sustainable fishing practices.",
            'recommendations' => "Research local species guidelines\nMonitor catch characteristics\nDocument species patterns\nFollow local regulations\nConsult with local experts",
            'sustainability_rating' => 'Concerning'
        ];
    }

    private function combineAllFeedback(array $suggestions): string
    {
        return implode("\n\n", array_map(function($suggestion) {
            return $suggestion['feedback'];
        }, $suggestions));
    }

    private function combineAllRecommendations(array $suggestions): string
    {
        return implode("\n\n", array_map(function($suggestion) {
            return $suggestion['recommendations'];
        }, $suggestions));
    }

    private function determineOverallSustainabilityRating(array $suggestions): string
    {
        $ratings = [
            'Good' => 1,
            'Concerning' => 2,
            'Critical' => 3
        ];

        $totalRating = 0;
        foreach ($suggestions as $suggestion) {
            $totalRating += $ratings[$suggestion['sustainability_rating']];
        }

        $averageRating = $totalRating / count($suggestions);

        if ($averageRating <= 1.5) {
            return 'Good';
        } elseif ($averageRating <= 2.2) {
            return 'Concerning';
        } else {
            return 'Critical';
        }
    }
} 