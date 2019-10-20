<?php

namespace Trial\Resources\Pages\Helpers;

use cobisja\BootHelp\BootHelp;

/*
 * classe per la crezione del modal standard
 * 
 * usato per interagire con l'utente in fase
 * 
 * di editing dei dati sul db
 * 
 */

class UiModal extends BootHelp {

    private $buttonAction;
    private $buttonActionNo;
    private $buttonText;
    private $buttonTextNo;
    private $optionModal;
    private $contentModal;
    private $blockInsideModal;
    private $htmlModalElements;

    

    public function __set($name, $value) {
        $this->{$name} = $value;
    }

    public function __get($name) {
        return $this->{$name};
    }

    /**
     * 
     * crea il modal sulla base
     * delle proprietà della classe.
     * 
     * NB: il metodo va chiamato dopo aver settato le proprietà
     * 
     * @return string
     */
    public function getJsString($isConfirm = FALSE) {

        $local = $this;

        if ($isConfirm) {

            $this->blockInsideModal = function() use ($local) {
                return
                        $local->content_tag('div', $local->contentModal, ['class' => 'modal-body']) .
                        $local->content_tag('div', ['class' => 'modal-footer'], function() use ($local) {
                            return $local->content_tag('button', $local->buttonText, ['type' => 'button', 'class' => 'btn btn-primary', 'onclick' => $local->buttonAction]) .
                                    $local->content_tag('button', $local->buttonTextNo, ['type' => 'button', 'class' => 'btn btn-primary', 'onclick' => $local->buttonActionNo]);
                        });
            };
        } else {

            $this->blockInsideModal = function() use ($local) {
                return
                        $local->content_tag('div', $local->contentModal, ['class' => 'modal-body']) .
                        $local->content_tag('div', ['class' => 'modal-footer'], function() use ($local) {
                            return $local->content_tag('button', $local->buttonText, ['type' => 'button', 'class' => 'btn btn-primary', 'onclick' => $local->buttonAction]);
                        });
            };
        }


        $this->htmlModalElements = $this->modal($this->optionModal, $this->blockInsideModal)->get_html()->get_content()->get_content();

        return str_replace(array("\r\n", "\n", "\r"), ' ', $this->htmlModalElements[1]->to_string());
    }

}
