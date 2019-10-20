<?php

namespace Trial\Db\Entity;

/**
 * Description of Tasso
 *
 * @author giacomo.solazzi
 */
class Tassi {

    private $data = NULL;

    /**
     * 
     * @param \Trial\Mdm\TipiTassi $tipo
     * @param const $attributo - \Trial\DbEntity\Tasso::IMPORTO o Tasso::TASSO
     * @param float $valore
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($tipo = NULL, $attributo = NULL, $valore = NULL) {

        if (is_null($tipo)) {
            $this->inizializzaDefaults();
        } else {

            $reflTipo = new \ReflectionClass(\Trial\Mdm\TipiTassi::class);
            $reflAttr = new \ReflectionClass(\Trial\DbEntity\Tasso::class);

            if (in_array($tipo, $reflTipo->getConstants()) && in_array($attributo, $reflAttr->getConstants())) {

                $this->data[$tipo][$attributo] = $valore;
            } else {
                $this->inizializzaDefaults();
                unset($reflAttr);
                unset($reflTipo);
                throw new \InvalidArgumentException('Args not allowed');
            }

            unset($reflAttr);
            unset($reflTipo);
        }
    }

    /**
     * 
     * @param \Trial\Mdm\TipiTassi $tipo
     * @param float $valore
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function setImporto($tipo, $valore) {


        $refl = new \ReflectionClass(\Trial\Mdm\TipiTassi::class);

        if (in_array($tipo, $refl->getConstants())) {

            $this->data[$tipo][\Trial\DbEntity\Tasso::IMPORTO] = $valore;
            return TRUE;
        } else {

            throw new \InvalidArgumentException('Args 1 allow only \\Trial\\Mdm\\TipiTassi constants');
        }
    }

    /**
     * 
     * @param \Trial\Mdm\TipiTassi $tipo
     * 
     * @throws \InvalidArgumentException
     * @return mixed float | boolean
     */
    public function getImporto($tipo) {

        if (isset($this->data[$tipo][\Trial\DbEntity\Tasso::IMPORTO])) {

            return $this->data[$tipo][\Trial\DbEntity\Tasso::IMPORTO];
        } else {

            throw new \InvalidArgumentException('Args 1 allow only \\Trial\\Mdm\\TipiTassi constants');
        }
    }

    /**
     * 
     * @param \Trial\Mdm\TipiTassi $tipo
     * @param float $valore
     * 
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function setTasso($tipo, $valore) {


        $refl = new \ReflectionClass(\Trial\Mdm\TipiTassi::class);

        if (in_array($tipo, $refl->getConstants())) {

            $this->data[$tipo][\Trial\DbEntity\Tasso::TASSO] = $valore;

            return TRUE;
        } else {

            throw new \InvalidArgumentException('Args 1 allow only \\Trial\\Trial\Mdm\\TipiTassi constants');
        }
    }

    /**
     * 
     * @param \Trial\Mdm\TipiTassi $tipo
     * 
     * @throws \InvalidArgumentException
     * @return mixed float | boolean
     */
    public function getTasso($tipo) {


        if (isset($this->data[$tipo][\Trial\DbEntity\Tasso::TASSO])) {

            return $this->data[$tipo][\Trial\DbEntity\Tasso::TASSO];
        } else {

            throw new \InvalidArgumentException('Args 1 allow only \\Trial\\Mdm\\TipiTassi constants');
        }
    }

    /**
     * imposta i valori di default per la proprietà $data
     * 
     */
    private function inizializzaDefaults() {


        $reflTipo = new \ReflectionClass(\Trial\Mdm\TipiTassi::class);

        foreach ($reflTipo->getConstants() as $tipo) {

            $this->data[$tipo][\Trial\DbEntity\Tasso::IMPORTO] = 0;
            $this->data[$tipo][\Trial\DbEntity\Tasso::TASSO] = 1;
        }

        unset($reflTipo);
    }

    /**
     * 
     * @return array
     */
    public function toArray() {


        return $this->data;
    }

    /**
     * 
     * @param mixed \Trial\DbEntity\Tassi | array $tassi
     * 
     */
    public function aggiungiTassi(\Trial\DbEntity\Tassi $tassi) {

        if (is_array($tassi)) {

            foreach ($tassi as $objTasso) {

                $this->data = array_merge($this->data, $objTasso->toArray());
            }
        } elseif ($tassi instanceof \Trial\DbEntity\Tassi) {

            $this->data = array_merge($this->data, $tassi->toArray());
        } else {

            throw new \InvalidArgumentException('Arg not allowed');
        }
    }
    
    

}
