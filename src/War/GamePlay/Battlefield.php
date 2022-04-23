<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay;

use Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface;

/**
 * A manager that will roll the dice and compute the winners of a battle.
 */
class Battlefield implements BattlefieldInterface {
	 public function rollDice(CountryInterface $country, bool $isAtacking): array{
        $qtd = $country->getNumberOfTroops();
        if ($isAtacking == TRUE)
          $qtd--;
        $rolls = [];
        for($i = 0;$i < $qtd;$i++){
          $rolls[] = rand(1, 6);
        }
        return $rolls;
      }

      public function computeBattle(CountryInterface $attackingCountry, array $attackingDice, CountryInterface $defendingCountry, array $defendingDice): void{
        #Quantidade de dados comparados é igual a menor quantidade de dados entre o atacante e o defensor
        $qtd = min(sizeof($attackingDice),sizeof($defendingDice));
        $attackPoints = 0;
        $defendingPoints = 0;
        for($i=0;$i<$qtd;$i++){
          #Sendo o dado do atacante maior que o do defensor, o atacante ganha nesse caso
          if($attackingDice[$i] > $defendingDice[$i])
            $attackPoints++;
          #Qualquer outro caso, quem ganha é o defensor
          else
            $defendingPoints++;
        }
        #Subtrai a qtd de tropas mortas de cada pais
        $attackingCountry->killTroops($defendingPoints);
        $defendingCountry->killTroops($attackPoints);
      }
}
