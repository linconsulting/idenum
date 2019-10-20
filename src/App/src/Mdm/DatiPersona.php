<?php

namespace App\Mdm;

use App\Mdm\RedNum;
use App\Mdm\LetNum;

/**
 * Description of DatiPersona
 *
 * @author giacomo.solazzi
 */
class DatiPersona {

    public $nome;
    public $cognome;
    public $dataNascita;    
    
    protected $giornoNascita;
    protected $meseNascita;
    protected $annoNascita;


    public function __construct($nome = 'nome', $cognome = 'cognome', $data = '01/01/1970') {
        
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->dataNascita = explode('/', $data);
        
        $this->giornoNascita = $this->dataNascita[0];
        $this->meseNascita = $this->dataNascita[1];
        $this->annoNascita = $this->dataNascita[2];
        
        $this->dataNascita = $data;
        
    }
    
    
    
    public function numeroNome(){
        
        return intval(LetNum::matchStringNumber($this->nome));
        
    }
    
    public function numeroCognome(){
        
        return intval(LetNum::matchStringNumber($this->cognome));
        
    }
    
    public function numeroNascita(){
        
        return intval(RedNum::shrink($this->getNumeriNascita()));
        
    }
    
    public function getNumeriNascita(){
        
        return intval($this->giornoNascita.$this->meseNascita.$this->annoNascita);
        
    }
    
    public function numeroMissioneVita(){
        
        return RedNum::shrink($this->numeroNome()+$this->numeroCognome()+$this->numeroNascita());
        
    }
    
    

}
