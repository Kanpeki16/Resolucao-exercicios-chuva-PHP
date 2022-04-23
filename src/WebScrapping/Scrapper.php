<?php

namespace Galoa\ExerciciosPhp2022\WebScrapping;

use DOMXPath;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;

#Entidades necess�rias do Spout para o uso

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and creates a XLSX file.
   */
  public function scrap(\DOMDocument $dom): void {
    libxml_use_internal_errors(true); #Deixar os erros somente internos
    $xPath = new DOMXPath($dom);
    #Carrega todos os em listas. Titulos dos artigos, Ids dos artigos e se eram palestras ou artigos, respectivamente.
    $titulos = [];
    $ids = [];
    $tipos = [];
    #Em todos s�o feitos querys no xPath para pegar somente os dados que interessam
    $aux = $xPath->query('.//h4[@class="my-xs paper-title"]');
    foreach($aux as $elemento){
      #Copia o texto de cada elemento para a lista
      $titulos[] = $elemento->textContent. PHP_EOL;
    }
    $aux = $xPath->query('.//div[@class="volume-info"]');
    foreach($aux as $elemento){
      $ids[] = $elemento->textContent. PHP_EOL;
    }
    $aux = $xPath->query('.//div[@class="tags mr-sm"]');
    foreach($aux as $elemento){
      $tipos[] = $elemento->textContent. PHP_EOL;
    }
    $autores_titulos = $xPath->query('.//div[@class="authors"]//span');
    $autores = [];
    $inst = [];
    #Lista que contem o alfabeto
    $alfa = range("A","Z");
    #For para percorrer todos os span que contem os autores e suas respectivas institui��es
    foreach ($autores_titulos as $elemento){
      $flag = False; #Flag que verifica se � um nome valido
      foreach($alfa as $l){ #For para verificar se o nome coletado possui alguma letra
        if(strpos(strtoupper($elemento->textContent. PHP_EOL), $l) !== false){
          #Possuindo alguma letra, o for � parado e a flag � acionada
          $flag = True;
          break;
        }
      }
      #Caso a flag for verdadeira, o nome do autor e sua institui��o � salvo nas respectivas listas
      if($flag == True){
        $autores[] = substr($elemento->textContent, 0, strlen($elemento->textContent)-1); #Copia o nome do autor sem o ; ao final
        $inst[] = $elemento->attributes->getNamedItem('title')->nodeValue . PHP_EOL;
      }
    }
    
    #Quantidade de autores em cada um dos artigos/paletras 
    $aux = $xPath->query('.//div[@class="authors"]');
    $qtd_autores = [];
    foreach ($aux as $elemento){
      $aux_aux = $elemento->textContent. PHP_EOL;
      $subs = explode("; ", $aux_aux); #Separa os autores por meio do ';' em uma lista
      $cont = 0; #Contador de participantes do artigo/palestra
      #Ocorre a mesma verifica��o para os nomes validos
      foreach($subs as $nome){
        $flag = False;
        foreach($alfa as $l){
          if(strpos($nome, $l) !== false){
            $flag = True;
            break;
          }
        }
        #A flag sendo positiva, a qtd de autores � incrementada
        if($flag == True){
          $cont++;
        }
      }
      $qtd_autores[] = $cont; #Qtd de autores desse artigo/palestra
    }

    #Criando a planilha
    $filePath = './src/WebScrapping/planilha.xlsx'; #Planilha � criada nessa mesma pasta
    $writer = WriterEntityFactory::createODSWriter();#Cria o Escritor de planilha
    $writer->setShouldCreateNewSheetsAutomatically(true);#Cria a panilha automaticamente
    $writer->openToFile($filePath);#Recebe o caminho da panilha

    #Cria o estilo da borda do cabe�alho
    $border = (new BorderBuilder())
    ->setBorderBottom(Color::BLUE, Border::WIDTH_THIN, Border::STYLE_SOLID)
    #azul como a chuva
    ->build();

    #Estilo do cabe�alho
    $style1 = (new StyleBuilder())
          ->setFontBold(True)
          ->setFontSize(12)
          ->setShouldWrapText()
          ->setCellAlignment(CellAlignment::LEFT)
          ->setBorder($border)
          ->build();

    #Estilo das linhas
    $style2 = (new StyleBuilder())
          ->setFontSize(12)
          ->setShouldWrapText()
          ->setCellAlignment(CellAlignment::LEFT)
          ->build();
    
    #Cria o vetor do cabe�alho
    $cabeca = ['ID','Title','Type'];
    #La�o de repeti��o For para escrever a qtd m�xima de autores possiveis atraves da var qtd_autores
    for($i = 0; $i < max($qtd_autores); $i++){
      $aux = 'Author ';
      $aux .= $i+1;
      $cabeca[] = $aux;
      $aux .= ' Institution';
      $cabeca[] = $aux;
    }
    #Adiciona o cabe�alho na planilha
    $row = WriterEntityFactory::createRowFromArray($cabeca, $style1);
    $writer->addRow($row);

    #Coloca todos os valores encontrados dentro de um vetor e escreve na planilha
    $cont_nomes = 0;
    for($i = 0; $i < sizeof($ids); $i++){
      $aux = [];
      $aux[] = $ids[$i];
      $aux[] = $titulos[$i];
      $aux[] = $tipos[$i];
      for($y = 0; $y < $qtd_autores[$i]; $y++){
         $aux[] = $autores[$cont_nomes];
         $aux[] = $inst[$cont_nomes];
         $cont_nomes++;
      }
      $row = WriterEntityFactory::createRowFromArray($aux, $style2);
      $writer->addRow($row);
    }

    $writer->close(); #Fecha a planilha
  }
}
