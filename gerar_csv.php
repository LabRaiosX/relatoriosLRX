<?php

$fp = fopen("uploads/theta.csv", "w"); // $fp conterá o handle do arquivo que abriremos

  echo 'Iniciando processo... <br/>';


  //cabecalho
  fwrite($fp,'Data;');
  fwrite($fp,'Arquivo;');
  fwrite($fp,'Laboratorio/Grupo;');
  fwrite($fp,'Prof.Orientador;');
  fwrite($fp,'Email;');
  fwrite($fp,'Aluno;');
  fwrite($fp,'Email;');
  fwrite($fp,'IC/MS/Dr/Pesq;');
  fwrite($fp,'Nome Amostra;');
  fwrite($fp,'2thetai;');
  fwrite($fp,'2thetaf;');
  fwrite($fp,'Countingtime;');
  fwrite($fp,'TotalTime;');
  fwrite($fp,'XrayTube;');
  fwrite($fp,'XrayMirror');
  fwrite($fp, "\n");

  //caminho da pasta
  $path = "file-uploader/server/php/uploads/";
  $files = opendir($path);


  //variavel auxiliar
  $i = 0;

  //lendo os arquivos da pasta e gravando em um array
  while ( ($file = readdir($files)) != false )
  {
      //se o arquivo e um arquivo
      if ( is_dir($file) == false)
      {
        $array[$i] = $file;
        $i++;
      }
  }

  echo "Quantidade de Arquivos: ".count($array);

  if(count($array) != 0){
        //ordenando os arquivos
        for ( $i = 0 ; $i < count($array) ; $i++)
        {
            $y = 0;
            $z = 0;
            $ativo = false;
            $snumero = null;
            while ( ($array[$i][$y] != '.') )
            {
              if ($array[$i][$y] == '_' ) { $ativo = true;   $y++; }

              if ( $ativo ) 
              {
                $snumero[$z] = $array[$i][$y];
                $z++;
              }
              $y++;
            }
            $qtd = strripos($array[$i], '.') - strripos($array[$i], '_') - 1;

            if ($qtd == 1)
            {
              $numero =  $snumero[0];
            }
            else if ($qtd == 2)
            {
              $numero =  $snumero[0] . $snumero[1];
            }
            else
            {
              $numero =  $snumero[0] . $snumero[1] . $snumero[2];
            }
            
            $arrayAux[] = array ( 'NOME' => $array[$i], 'VALOR' => $numero);

        }


        foreach ( $arrayAux as $key => $row)
        {
            $nome[$key] = $row['NOME'];
            $valor[$key] = $row['VALOR'];
        }

        array_multisort($valor,SORT_ASC,$nome,SORT_DESC,$arrayAux);

        $doc = new DomDocument;

        $p = 0;
        foreach ( $arrayAux as $key => $row)
        {
           $doc->load($path .'/' . $row['NOME'], null);

           //Data
           fwrite($fp,substr($doc->getElementsByTagName('startTimeStamp')->item(0)->nodeValue,0,10) . ';');

           //Arquivo
           fwrite($fp, $row['NOME'] . ';');
           //Laboratorio/Grupo
           fwrite($fp,';');
           //Prof.Orientador
           fwrite($fp,';');
           //Email
           fwrite($fp,';');
           //Aluno
           fwrite($fp,';');
           //Email
           fwrite($fp,';');
           //IC/MS/Dr/Pesq
           fwrite($fp,';');
           //nome amostra
           fwrite($fp, $doc->getElementsByTagName('name')->item(0)->nodeValue . ';');
           //2thetai
           fwrite($fp, $doc->getElementsByTagName('startPosition')->item(0)->nodeValue . ';');
           //2thetaf
           fwrite($fp, $doc->getElementsByTagName('endPosition')->item(0)->nodeValue . ';');
           //counting time
           fwrite($fp, $doc->getElementsByTagName('commonCountingTime')->item(0)->nodeValue . ';');

           //TOTAL TIME
            //hora inicio
            $startH = substr($doc->getElementsByTagName('startTimeStamp')->item(0)->nodeValue,11,2);
            //minutos inicio
            $startM = substr($doc->getElementsByTagName('startTimeStamp')->item(0)->nodeValue,14,2);
            //segundos inicio
            $startS = substr($doc->getElementsByTagName('startTimeStamp')->item(0)->nodeValue,17,2);
            $totalStart = $startH*3600 + $startM*60 + $startS;

            //hora fim
            $endH = substr($doc->getElementsByTagName('endTimeStamp')->item(0)->nodeValue,11,2);
            //minutos fim
            $endM = substr($doc->getElementsByTagName('endTimeStamp')->item(0)->nodeValue,14,2);
            //segundos fim
            $endS = substr($doc->getElementsByTagName('endTimeStamp')->item(0)->nodeValue,17,2);
            $totalEnd = $endH*3600 + $endM*60 + $endS;

           fwrite($fp,$totalEnd - $totalStart . ';');
           //XrayTube
           fwrite($fp,$doc->getElementsByTagName('xRayTube')->item(0)->getAttribute('name') . ';');
           //XrayMirror
           if ($doc->getElementsByTagName('xRayMirror')->item(0) != NULL)
           {
             fwrite($fp,$doc->getElementsByTagName('xRayMirror')->item(0)->getAttribute('name').';');
           }
           else
           {
             fwrite($fp,';');
           }
           fwrite($fp,"\n");

        }

        echo '<br/> Terminou<br/>';
        echo "<a href='uploads/theta.csv' target='_blank'>Link para o arquivo CSV</a><br/>";

        closedir($files);

        fclose($fp);

  }else{ echo "Nao ha arquivos carregados";}

  system("cd $path && rm *");

?>