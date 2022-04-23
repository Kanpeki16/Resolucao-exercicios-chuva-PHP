<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;


class ComputerPlayerCountry extends BaseCountry {


  public function chooseToAttack(): ?CountryInterface {
    $neighbors = $this->getNeighbors();
    $resu = rand(0, sizeof($neighbors));
    #Se o valor for igual a 0, não querer atacar, ou o pais tiver a qtd de tropas <= 1...
    #Não será possivel atacar
    if($this->getNumberOfTroops() <= 1 or $resu == 0){
      print " ".$this->getName()." não ataca!\n";
      return NULL;
    }
    else
      return $neighbors[$resu-1];
  }

}
