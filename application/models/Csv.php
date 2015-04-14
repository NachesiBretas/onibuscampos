<?php
class Application_Model_Csv
{
  public function createCsvSystem($inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue){
  header('Content-Encoding: utf-8');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=relatorio_composicao_receita.csv');
  echo "\xEF\xBB\xBF";
  Zend_Layout::getMvcInstance()->setLayout('ajax');
  $cash = new Application_Model_Mco();
  $output = fopen('php://output', 'w');
  $value = $cash->reportCashSystemValue($inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue);
  $type = $cash->reportCashSystemType($inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue);
  fputcsv($output, array('Inicio: '.$inicio,'Fim: '.$fim ),';');
  fputcsv($output, array('                                                                               '),';');
  fputcsv($output, array('Tarifa','Quantidade de Passageiros','Porcentagem', 'Receita Bruta', 'Porcentagem', 'Repasse CBTU', 'Porcentagem',
    'Receita Liquida', 'Porcentagem'),';');
  foreach ($value as $values){
    fputcsv($output, array($values->value,$values->amount,$values->amount_percent.'%',$values->full_composition,
      $values->full_composition_percentage.'%',$values->cbtu_transfer,$values->cbtu_transfer_percentage.'%',
      $values->liquid_composition,$values->liquid_composition_percentage.'%'),';');
  }
  fputcsv($output, array('Total',$total_passenger,'100%',$total_revenue,'100%',$total_cbtu_transfer,
    '100%',$total_liquid_revenue,'100%'),';');
  fputcsv($output, array('                                                                               '),';');
  fputcsv($output, array('Tipo da Tarifa','Quantidade de Passageiros','Porcentagem','Receita Bruta','Porcentagem','Repasse CBTU',
    'Porcentagem','Receita Liquida', 'Porcentagem'),';');
  foreach ($type as $types){
    fputcsv($output, array($types->type,$types->amount,$types->amount_percent.'%',$types->full_composition,
      $types->full_composition_percentage.'%',$types->cbtu_transfer,$types->cbtu_transfer_percentage.'%',
      $types->liquid_composition,$types->liquid_composition_percentage.'%'),';');
  }
  fputcsv($output, array('Total',$total_passenger,'100%',$total_revenue,'100%',$total_cbtu_transfer,
    '100%',$total_liquid_revenue,'100%'),';');
    exit; 
  }

  public function createCsvCell($aux, $cell, $inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue){
  header('Content-Encoding: utf-8');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=relatorio_receita_'.$aux[2].'_'.$aux[3].'.csv');
  echo "\xEF\xBB\xBF";
  Zend_Layout::getMvcInstance()->setLayout('ajax');
  $cash = new Application_Model_Mco();
  $output = fopen('php://output', 'w');
  $type = $cash->reportCashCellType($cell, $inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue);
  $value = $cash->reportCashCellValue($cell, $inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue);
  fputcsv($output, array('Inicio: '.$inicio,'Fim: '.$fim ),';');
  fputcsv($output, array('                                                                               '),';');
  fputcsv($output, array('Tarifa','Quantidade de Passageiros','Porcentagem', 'Receita Bruta', 'Porcentagem', 'Repasse CBTU', 'Porcentagem',
    'Receita Liquida', 'Porcentagem'),';');
  foreach ($value as $values){
    fputcsv($output, array($values->value,$values->amount,$values->amount_percent.'%',$values->full_composition,
      $values->full_composition_percentage.'%',$values->cbtu_transfer,$values->cbtu_transfer_percentage.'%',
      $values->liquid_composition,$values->liquid_composition_percentage.'%'),';');
  }
  fputcsv($output, array('Total',$total_passenger,'100%',$total_revenue,'100%',$total_cbtu_transfer,
    '100%',$total_liquid_revenue,'100%'),';');
  fputcsv($output, array('                                                                               '),';');
  fputcsv($output, array('Tipo da Tarifa','Quantidade de Passageiros','Porcentagem','Receita Bruta','Porcentagem','Repasse CBTU',
    'Porcentagem','Receita Liquida', 'Porcentagem'),';');
  foreach ($type as $types){
    fputcsv($output, array($types->type,$types->amount,$types->amount_percent.'%',$types->full_composition,
      $types->full_composition_percentage.'%',$types->cbtu_transfer,$types->cbtu_transfer_percentage.'%',
      $types->liquid_composition,$types->liquid_composition_percentage.'%'),';');
  }
  fputcsv($output, array('Total',$total_passenger,'100%',$total_revenue,'100%',$total_cbtu_transfer,
    '100%',$total_liquid_revenue,'100%'),';');
    exit; 
  }
}