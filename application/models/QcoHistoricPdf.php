<?php

class Application_Model_QcoHistoricPdf
{

  protected $pdf;
  protected $page;
  protected $font;
  protected $maxHeightHour = 9999;


  public function createPdf($qcoId)
  {
    $this->pdf = new Zend_Pdf();
    $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
    $this->pdf->pages[0] = $page;

    $qco = new Application_Model_Qco();
    $qcoMain = $qco->returnHistoricMainById($qcoId);
    $qcoHour = $qco->returnHistoricHourById($qcoId);
    $qcoFleet = $qco->returnHistoricFleetById($qcoId);
    $height = 680;
    $height = $this->header($page,$qcoMain);
    $height = $this->fleet($page, $height, $qcoFleet);
    $height = $this->qh($page,$height,$qcoHour,$qcoId);
    return $this->pdf;
  }

  protected function header($page,$qcoMain)
  {
    $image = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH . '/../public/img/brasao.png');
    $page->drawImage($image, 61, 747, 131, 811);

    $page->setLineWidth(2)
        ->drawLine(50, 820, 545 , 820);

    $page->setLineWidth(2)
        ->drawLine(50, 740, 545, 740);

    $page->setLineWidth(2)
        ->drawLine(51, 740, 51, 819);

    $page->setLineWidth(2)
        ->drawLine(544, 740, 544, 820);

    $page->setLineWidth(2)
        ->drawLine(544, 740, 544, 820);

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),15)
                    ->drawText('QUADRO DE CARACTERÍSTICAS OPERACIONAIS', 160, 780, 'UTF-8');

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                    ->drawText('SECRETARIA DE TRANSPORTES E OBRAS PÚBLICAS', 225, 767, 'UTF-8');

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),10)
                    ->drawText('Linha: ', 40, 710, 'UTF-8');

    $style = new Zend_Pdf_Style();
    $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),11);
    $general = new Application_Model_General();

    $y = 710;
    $lines = explode("\n",$general->getWrappedText($qcoMain->number_communication . ' - '. $qcoMain->name,$style,425));
    foreach($lines as $line)
    {
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),11)
              ->drawText($line, 100, $y);
        $y-=15;
    }

    if($y < 680)
    {
      $height = 675;
    }
    else
    {
      $height = 690;
    }

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),10)
                    ->drawText('Consórcio: ', 40, $height, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),11)
                    ->drawText(Application_Model_General::returnConsortium($qcoMain->number_communication[0]), 100, $height, 'UTF-8');

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),10)
                    ->drawText('Operação: ', 40, ($height - 20), 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),11)
                    ->drawText('<não feito ainda>', 100, ($height - 20), 'UTF-8');

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),10)
                    ->drawText('Vigência: ', 40, ($height - 40), 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),11)
                    ->drawText($qcoMain->start_validity_date, 100, ($height - 40), 'UTF-8');
    return ($height - 70);
  }

  protected function fleet($page, $height, $qcoFleet)
  {
    $qco = new Application_Model_Qco();
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),10)
                    ->drawText('Quantidade de Viagens Programadas', 60, $height - 30, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),10)
                    ->drawText('Tipo de Dia', 40, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),10)
                    ->drawText('PC', 160, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),10)
                    ->drawText('Viagens', 200, $height - 40, 'UTF-8');
    $y = $height - 50;
    foreach ($qcoFleet as $fleet) 
    {
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),8)
                    ->drawText($fleet->type_day_name, 40, $y, 'UTF-8');
      $countJourneys = $qco->returnCountJourneyById($fleet->qco_id, $fleet->id_type_day);
                    
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),8)
                    ->drawText($countJourneys->count, 210, $y, 'UTF-8');
      $y -= 10;
    }

    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),10)
                    ->drawText('Frota Operacional por Faixa Horária', 345, $height - 30, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('00', 260, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('01', 273, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('02', 286, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('03', 299, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('04', 312, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('05', 325, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('06', 338, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('07', 351, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('08', 364, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('09', 377, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('10', 390, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('11', 403, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('12', 416, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('13', 429, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('14', 442, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('15', 455, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('16', 468, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('17', 481, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('18', 494, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('19', 507, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('20', 520, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('21', 533, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('22', 546, $height - 40, 'UTF-8');
    $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                    ->drawText('23', 559, $height - 40, 'UTF-8');
    $y = $height - 50;
    foreach ($qcoFleet as $fleet) 
    {
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_00, 260, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_01, 273, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_02, 286, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_03, 299, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_04, 312, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_05, 325, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_06, 338, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_07, 351, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_08, 364, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_09, 377, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_10, 390, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_11, 403, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_12, 416, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_13, 429, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_14, 442, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_15, 455, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_16, 468, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_17, 481, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_18, 494, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_19, 507, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_20, 520, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_21, 533, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_22, 546, $y, 'UTF-8');
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($fleet->hour_23, 559, $y, 'UTF-8');
      $y -= 10;
    }
    return $y;
  }

  protected function qh($page,$height,$qcoHour,$qcoId,$qcoRoute)
  {
    $y = 800;
    $qco = new Application_Model_Qco();
    foreach ($qcoHour as $hour) 
    {
      $page = $this->pdf->newPage(Zend_Pdf_Page::SIZE_A4);
      $this->pdf->pages[] = $page;

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText('Quadro de horário para '.$hour->type_day_name, 60, $y, 'UTF-8');
      $y -= 15;
      $maxHeight = array();

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('00', 60, $y, 'UTF-8');
      $hour_00 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'00');
      $flag = $this->printMinutes($page,$y,60,$hour_00);
      array_push($maxHeight, $flag);

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('01', 80, $y, 'UTF-8');
      $hour_01 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'01');
      $flag = $this->printMinutes($page,$y,80,$hour_01);
      array_push($maxHeight, $flag);

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('02', 100, $y, 'UTF-8');
      $hour_02 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'02');
      $flag = $this->printMinutes($page,$y,100,$hour_02);
      array_push($maxHeight, $flag);

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('03', 120, $y, 'UTF-8');
      $hour_03 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'03');
      $flag = $this->printMinutes($page,$y,120,$hour_03);
      array_push($maxHeight, $flag);

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('04', 140, $y, 'UTF-8');
      $hour_04 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'04');
      $flag = $this->printMinutes($page,$y,140,$hour_04);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('05', 160, $y, 'UTF-8');
      $hour_05 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'05');
      $flag = $this->printMinutes($page,$y,160,$hour_05);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('06', 180, $y, 'UTF-8');
      $hour_06 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'06');
      $flag = $this->printMinutes($page,$y,180,$hour_06);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('07', 200, $y, 'UTF-8');
      $hour_07 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'07');
      $flag = $this->printMinutes($page,$y,200,$hour_07);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('08', 220, $y, 'UTF-8');
      $hour_08 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'08');
      $flag = $this->printMinutes($page,$y,220,$hour_08);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('09', 240, $y, 'UTF-8');
      $hour_09 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'09');
      $flag = $this->printMinutes($page,$y,240,$hour_09);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('10', 260, $y, 'UTF-8');
      $hour_10 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'10');
      $flag = $this->printMinutes($page,$y,260,$hour_10);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('11', 280, $y, 'UTF-8');
      $hour_11 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'11');
      $flag = $this->printMinutes($page,$y,280,$hour_11);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('12', 300, $y, 'UTF-8');
      $hour_12 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'12');
      $flag = $this->printMinutes($page,$y,300,$hour_12);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('13', 320, $y, 'UTF-8');
      $hour_13 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'13');
      $flag = $this->printMinutes($page,$y,320,$hour_13);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('14', 340, $y, 'UTF-8');
      $hour_14 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'14');
      $flag = $this->printMinutes($page,$y,340,$hour_14);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('15', 360, $y, 'UTF-8');
      $hour_15 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'15');
      $flag = $this->printMinutes($page,$y,360,$hour_15);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('16', 380, $y, 'UTF-8');
      $hour_16 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'16');
      $flag = $this->printMinutes($page,$y,380,$hour_16);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('17', 400, $y, 'UTF-8');
      $hour_17 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'17');
      $flag = $this->printMinutes($page,$y,400,$hour_17);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('18', 420, $y, 'UTF-8');
      $hour_18 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'18');
      $flag = $this->printMinutes($page,$y,420,$hour_18);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('19', 440, $y, 'UTF-8');
      $hour_19 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'19');
      $flag = $this->printMinutes($page,$y,440,$hour_19);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('20', 460, $y, 'UTF-8');
      $hour_20 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'20');
      $flag = $this->printMinutes($page,$y,460,$hour_20);
      array_push($maxHeight, $flag);

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('21', 480, $y, 'UTF-8');
      $hour_21 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'21');
      $flag = $this->printMinutes($page,$y,480,$hour_21);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('22', 500, $y, 'UTF-8');
      $hour_22 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'22');
      $flag = $this->printMinutes($page,$y,500,$hour_22);
      array_push($maxHeight, $flag);
      
      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),9)
                      ->drawText('23', 520, $y, 'UTF-8');
      $hour_23 = $qco->returnMinutesHistoric($qcoId,$hour->id_type_day,'23');
      $flag = $this->printMinutes($page,$y,520,$hour_23);
      array_push($maxHeight, $flag);

      $qcoRoute = $qco->returnHistoricRoute($qcoId,$hour->id_type_journey);
      //$height = $this->route($page, $y, $qcoRoute);

      foreach ($qcoRoute as $route) 
      {
        $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),8)
                      ->drawText('Extensão do asfalto: ', 60,min($maxHeight), 'UTF-8');
        $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),8)
                      ->drawText($route->ext_asphalt, 130, min($maxHeight), 'UTF-8');

        $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),8)
                      ->drawText('Pontos de parada: ', 60,min($maxHeight)-10, 'UTF-8');

        $peds = $route->ped;
        $lines = str_split($peds, 95);
        $i=20;
        foreach($lines as $line)
        {
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),7)
                  ->drawText($line, 70, min($maxHeight)-$i,'UTF-8');
            $y-=15;
            $i+=10;
        }

       // $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),8)
       //               ->drawText($route->ped, 60, min($maxHeight)-10, 'UTF-8');

        $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD),8)
                      ->drawText('Itinerário: ', 60,min($maxHeight)-$i, 'UTF-8');
       
        $routess = $route->route;
        $lines = str_split($routess, 95);
        foreach($lines as $line)
        {
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),7)
                  ->drawText($line, 70, min($maxHeight)-$i-10,'UTF-8');
            $y-=15;
            $i+=10;
        }

        $y -= 10;
      }

      $y = 800;
    }
  }

  protected function printMinutes($page,$height,$start,$hourRow)
  {
    $height -= 13;
    foreach($hourRow as $hourMinute)
    {

      $page     ->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES),9)
                      ->drawText($hourMinute->minutes, $start, $height, 'UTF-8');
      $height-= 12;
    }
    return $height;
  }

}

