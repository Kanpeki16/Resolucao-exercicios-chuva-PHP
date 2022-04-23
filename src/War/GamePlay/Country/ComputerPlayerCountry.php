<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;


class ComputerPlayerCountry extends BaseCountry {


  public function chooseToAttack(): ?CountryInterface {
    $neighbors = $this->getNeighbors();
    $resu = rand(0, sizeof($neighbors));
    #Se o valor for igual a 0, n�o querer atacar, ou o pais tiver a qtd de tropas <= 1...
    #N�o ser� possivel atacar
    if($this->getNumberOfTroops() <= 1 or $resu == 0){
      print " ".$this->getName()." n�o ataca!\n";
      return NULL;
    }
    else
      return $neighbors[$resu-1];
  }

}
