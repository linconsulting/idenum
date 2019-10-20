<?php

namespace Trial\Resources\Template;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Zend\I18n\Translator\Translator;

/**
 * Description of PlateTranslator
 *
 * @author giacomo.solazzi
 */
class PlatesTranslator implements ExtensionInterface {

    private $translator;
    private $translationRequired = FALSE;

    /**
     * 
     * @param Translator $translator
     */
    public function __construct(Translator $translator) {

        $this->translator = $translator;
    }

    /**
     * 
     * @param Engine $engine
     */
    public function register(Engine $engine) {

        $engine->registerFunction('translate', [$this, 'translateString']);
    }

    /**
     * 
     * @param bool $val
     */
    public function setRequiredTranslation($val) {

        $this->translationRequired = $val;
    }

    /**
     * 
     * @param string $string
     * @return string
     */
    public function translateString($string) {

        if ($this->translationRequired) {
            $string = $this->translator->translate($string);
        }

        return $string;
    }

}
