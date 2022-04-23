<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;

/**
 * Defines a country, that is also a player.
 */
class BaseCountry implements CountryInterface {

  /**
   * The name of the country.
   *
   * @var string
   */
  protected $name;

  /**
   * Builder.
   *
   * @param string $name
   *   The name of the country.
   */
   public function getName(): string{
    return $this->name;
  }
  public function __construct(string $name) {
    $this->name = $name;
  }
  public function setNeighbors(array $neighbors): void{
    $this->neighbors = $neighbors;
  }
  public function getNeighbors(): array{
    return $this->neighbors;
  }

  public function getNumberOfTroops(): int{
    if(property_exists($this, "numberOfTroops") == FALSE){
      $this->numberOfTroops = 3;
    }
    return $this->numberOfTroops;
  }
  public function isConquered(): bool{
    if ($this->getNumberOfTroops() == 0)
      return TRUE;
    else
      return FALSE;
  }
  public function conquer(CountryInterface $conqueredCountry): void{
    #Percorre os vizinhos do país conquistador e do país conquistado
    $aux = [];
    #Seleciona todos os vizinhos do país conquistado, sem incluir o pais conquistador
    foreach($conqueredCountry->getNeighbors() as $neighbors){
      if($neighbors->getName() != $this->getName())
        $aux[] = $neighbors;
    }
    #seleciona todos os vizinhos do país conquistador, não contendo o pais conquistado
    foreach($this->getNeighbors() as $neighbors){
      if($neighbors->getName() != $conqueredCountry->getName())
        $aux[] = $neighbors;
    }
    #É alocado os novos vizinhos no objeto
    $this->neighbors = $aux;
    #Adicionar/Cria numeros de paises conquistados
    if(property_exists($this, "numberOfConquered") == FALSE){
      $this->numberOfConquered = 1;
    }
    else
      $this->numberOfConquered++;
  }

  /**
   * Decreases the number of troops in this country by a given number.
   *
   * @param int $killedTroops
   *   The number of troops killed in battle.
   */
  public function killTroops(int $killedTroops): void{
     $this->numberOfTroops = $this->numberOfTroops - $killedTroops;
  }

}

