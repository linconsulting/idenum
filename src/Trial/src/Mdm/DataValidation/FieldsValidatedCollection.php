<?php

namespace Trial\Mdm\DataValidation;



/**
 * Description of FieldsValidatedCollection:
 * 
 * raccoglie e gestisce le classi FieldValidated
 *
 * @author giacomo.solazzi
 */

class FieldsValidatedCollection implements \IteratorAggregate {

    private $counter;

    public function __construct() {
        $this->counter = 0;
    }

    /**
     * 
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this);
    }

    /**
     * 
     * @param FieldsValidated $field
     */
    public function addField(FieldValidated $field) {

        $this->{$this->counter} = $field;
        $this->counter++;
    }

    /**
     * 
     * @param array $fields array di FieldsValidated
     */
    public function addFields($fields) {

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $this->{$this->counter} = $field;
                $this->counter++;
            }
        }
    }

    /**
     * 
     * @return mixed - FieldValidated, array di FieldValidated o NULL se vuoto
     */
    public function getFields() {

        $aRet = array();

        if ($this->counter >= 1) {

            foreach ($this as $field) {

                $aRet[] = $field;
            }
        }

        return $aRet;
    }

}
