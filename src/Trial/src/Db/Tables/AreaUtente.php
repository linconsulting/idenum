<?php

namespace Trial\Db\Tables;

use Trial\Db\Tables\Utenti;

/**
 * Description of AreaUtenti
 *
 * @author giacomo.solazzi
 */
class AreaUtente extends AbstractDbTables {

    const FK_UTENTI = 'usr';
    const ENUM_AREA_COMMERCIALE = 0;
    const ENUM_AREA_TECNICA = 1;
    const ENUM_AREA_AMMINISTRATIVA = 2;
    const ENUM_AREA_AGENTI_ESTERNI = 3;
    const ENUM_AREA_TECNICI_ESTERNI = 4;

    public function __construct($cond = NULL) {

        parent::__construct('gns_utenti_area_a', $cond);
    }

    /**
     * 
     * @param int $idArea
     * @return boolean TRUE se l'area è interna
     */
    static function interna($idArea) {

        $refl = new \ReflectionClass(get_class());

        foreach ($refl->getConstants() as $constName => $constVal) {

            if (strpos($constName, 'ESTERN') !== FALSE && $idArea == $constVal) {

                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * 
     * informa se l'utente è tecnico o commerciale
     * e se è un collaboratore o un dipendente
     * 
     * @param Utenti $user
     * @return array -- key: isTech, isCollaborator
     * 
     */
    static function infoArea(Utenti $user) {

        $idArea = is_null($user->areaRowset) ? $user->area()->areaRowset->current()->id_area : $user->areaRowset->current()->id_area;

        $aRet = array();

        $aRet[Utenti::USR_IS_TECH] = FALSE;

        if ($idArea == self::ENUM_AREA_TECNICA ||
                $idArea == self::ENUM_AREA_TECNICI_ESTERNI) {

            $aRet[Utenti::USR_IS_TECH] = TRUE;
        }

        $aRet[Utenti::USR_IS_COLLAB] = TRUE;

        if (self::interna($idArea)) {

            $aRet[Utenti::USR_IS_COLLAB] = FALSE;
        }

        return $aRet;
    }

}
