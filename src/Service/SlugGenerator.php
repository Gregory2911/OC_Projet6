<?php

namespace App\Service;

class SlugGenerator
{
    /**
     * @var array<string,string>
     */
    protected $rules = [];

    /**
     * @var array<string,mixed>
     */
    protected $options = [
        'regexp'    => '/[^A-Za-z0-9]+/', //LOWERCASE_NUMBERS_DASHES
        'separator' => '-',
        'lowercase' => true,
        'trim' => true,
        'rulesets'  => [ // Languages are preferred if they appear later
            'default',
            'french'
        ],
    ];

    public function __construct()
    {
        foreach ($this->options['rulesets'] as $ruleSet) {
            $this->activateRuleSet($ruleSet);
        }
    }

    /**
     * Returns the slug-version of the string.
     * @param string $string  	String to slugify
     * @return string 			Slugified version of the string
     */
    public function convert($string)
    {
        $string = strtr($string, $this->rules);

        if ($this->options['lowercase']) {
            $string = mb_strtolower($string);
        }

        $string = preg_replace($this->options['regexp'], $this->options['separator'], $string);

        $string = ($this->options['trim']) ? trim($string, $this->options['separator']) : $string;

        return $string;
    }

    /**
     * @param string $ruleSet
     */
    public function activateRuleSet($ruleSet)
    {
        // walking the ruleset
        foreach ($this->knownRules[$ruleSet] as $character => $replacement) {
            $this->rules[$character] = $replacement; // add a rule
        }
    }

    /**
     * @var array
     */
    protected $knownRules = /*INSERT_START*/ array(
        'default' =>
        array(
            '°' => '0',
            '¹' => '1',
            '²' => '2',
            '³' => '3',
            '⁴' => '4',
            '⁵' => '5',
            '⁶' => '6',
            '⁷' => '7',
            '⁸' => '8',
            '⁹' => '9',
            '₀' => '0',
            '₁' => '1',
            '₂' => '2',
            '₃' => '3',
            '₄' => '4',
            '₅' => '5',
            '₆' => '6',
            '₇' => '7',
            '₈' => '8',
            '₉' => '9',
            'æ' => 'ae',
            'ǽ' => 'ae',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Å' => 'AA',
            'Ǻ' => 'A',
            'Ă' => 'A',
            'Ǎ' => 'A',
            'Æ' => 'AE',
            'Ǽ' => 'AE',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'å' => 'aa',
            'ǻ' => 'a',
            'ă' => 'a',
            'ǎ' => 'a',
            'ª' => 'a',
            '@' => 'at',
            'Ĉ' => 'C',
            'Ċ' => 'C',
            'Ç' => 'C',
            'ç' => 'c',
            'ĉ' => 'c',
            'ċ' => 'c',
            '©' => 'c',
            'Ð' => 'Dj',
            'Đ' => 'D',
            'ð' => 'dj',
            'đ' => 'd',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ĕ' => 'E',
            'Ė' => 'E',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ƒ' => 'f',
            'Ĝ' => 'G',
            'Ġ' => 'G',
            'ĝ' => 'g',
            'ġ' => 'g',
            'Ĥ' => 'H',
            'Ħ' => 'H',
            'ĥ' => 'h',
            'ħ' => 'h',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ĩ' => 'I',
            'Ĭ' => 'I',
            'Ǐ' => 'I',
            'Į' => 'I',
            'Ĳ' => 'IJ',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ĩ' => 'i',
            'ĭ' => 'i',
            'ǐ' => 'i',
            'į' => 'i',
            'ĳ' => 'ij',
            'Ĵ' => 'J',
            'ĵ' => 'j',
            'Ĺ' => 'L',
            'Ľ' => 'L',
            'Ŀ' => 'L',
            'ĺ' => 'l',
            'ľ' => 'l',
            'ŀ' => 'l',
            'Ñ' => 'N',
            'ñ' => 'n',
            'ŉ' => 'n',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ō' => 'O',
            'Ŏ' => 'O',
            'Ǒ' => 'O',
            'Ő' => 'O',
            'Ơ' => 'O',
            'Ø' => 'OE',
            'Ǿ' => 'O',
            'Œ' => 'OE',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ō' => 'o',
            'ŏ' => 'o',
            'ǒ' => 'o',
            'ő' => 'o',
            'ơ' => 'o',
            'ø' => 'oe',
            'ǿ' => 'o',
            'º' => 'o',
            'œ' => 'oe',
            'Ŕ' => 'R',
            'Ŗ' => 'R',
            'ŕ' => 'r',
            'ŗ' => 'r',
            'Ŝ' => 'S',
            'Ș' => 'S',
            'ŝ' => 's',
            'ș' => 's',
            'ſ' => 's',
            'Ţ' => 'T',
            'Ț' => 'T',
            'Ŧ' => 'T',
            'Þ' => 'TH',
            'ţ' => 't',
            'ț' => 't',
            'ŧ' => 't',
            'þ' => 'th',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ũ' => 'U',
            'Ŭ' => 'U',
            'Ű' => 'U',
            'Ų' => 'U',
            'Ư' => 'U',
            'Ǔ' => 'U',
            'Ǖ' => 'U',
            'Ǘ' => 'U',
            'Ǚ' => 'U',
            'Ǜ' => 'U',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ũ' => 'u',
            'ŭ' => 'u',
            'ű' => 'u',
            'ų' => 'u',
            'ư' => 'u',
            'ǔ' => 'u',
            'ǖ' => 'u',
            'ǘ' => 'u',
            'ǚ' => 'u',
            'ǜ' => 'u',
            'Ŵ' => 'W',
            'ŵ' => 'w',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Ŷ' => 'Y',
            'ý' => 'y',
            'ÿ' => 'y',
            'ŷ' => 'y',
        ),
        'french' =>
        array(
            'À' => 'A',
            'Â' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'É' => 'E',
            'È' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ï' => 'I',
            'Î' => 'I',
            'Ô' => 'O',
            'Œ' => 'OE',
            'Ù' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'à' => 'a',
            'â' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ï' => 'i',
            'î' => 'i',
            'ô' => 'o',
            'œ' => 'oe',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ÿ' => 'y',
            'Ÿ' => 'Y',
        ),
    )/*INSERT_END*/;
}
