<?php

class Application_Model_General
{
	public static function dateToUs($date)
	{
		if($date == '0000-00-00' || $date == '')
      return new Zend_Db_Expr('NULL');
		$aux = explode('/', $date);
		return rtrim($aux[2]).'-'.$aux[1].'-'.$aux[0];
	}

	public static function dateToBr($date)
	{
    if($date == '0000-00-00' || $date == '')
      return '';
		$aux = explode('-', $date);
		$date = $aux[2].'/'.$aux[1].'/'.$aux[0];
		return $date;
	}

	public static function dateTimeToUs($date) // Kneel before Zod creator of this function!!!
	{
	    if($date == '0000-00-00' || $date == '')
	      return '';
		$aux = explode('/', $date);
		$aux1 = explode(' ', $aux[2]); // separa o dia da hora
		$dateTime = rtrim($aux1[0]).'-'.$aux[1].'-'.$aux[0].' '.$aux1[1];
		return $dateTime;
	}

	public static function convertToMinute($hour)
	{
	    if($hour == '00:00' || $hour == '')
	      return '';
		$aux = explode(':', $hour);
		$min = $aux[1];
		$hour = $aux[0];
		$conv_hour= $hour*60;
		$total_min = $conv_hour + $min;

		return $total_min;
	}

	public static function convertToHour($hour)
	{
	    if($hour == '00:00' || $hour == '')
	      return '';
		$hour = $hour/60;

		return $hour;
	}

	public static function hourToMinute($start_hour,$end_hour)
	{
	    if($start_hour == '0000-00-00' || $start_hour == '' || $end_hour == '0000-00-00' || $end_hour == '')
	      return '';
		$aux = explode(':', $start_hour);
		$min = $aux[1];
		$hour = $aux[0];
		$start_conv_hour= $hour*60;
		$start_total_min = $start_conv_hour + $min;

		$aux1 = explode(':', $end_hour);
		$min2 = $aux1[1];
		$hour2 = $aux1[0];
		$end_conv_hour= $hour2*60;
		$end_total_min = $end_conv_hour + $min2;

		$total_time = $end_total_min - $start_total_min;
		return $total_time." min";
	}

	public static function dateTimeToBr($date)
	{
	    if($date == '0000-00-00' || $date == '')
	      return '';
		$aux = explode('-', $date);
		$aux1 = explode(' ', $aux[2]); // separa o dia da hora
		$dateTime = $aux1[0].'/'.$aux[1].'/'.$aux[0].' '.$aux1[1];
		return $dateTime;
	}

	public static function convertHour($hour)
	{
		if($hour == '')
      return new Zend_Db_Expr('NULL');
    return $hour[0].$hour[1].':'.$hour[2].$hour[3];
	}

	public static function returnConsortium($rit)
	{
		$consortium = new Application_Model_DbTable_Consortium();
		$consortiumRow = $consortium->fetchRow($consortium->select()->where('id = ?',$rit));
		return $consortiumRow->name;
	}

	public static function cutString($text, $start=0, $end=100)
	{
		$text = ltrim($text);
		$j = $start;
		if(isset($text[$start-1]) && ( $text[$start] != "" || $text[$start] != " ") )
		{
			while($text[$j] != " ")
  		{
    		$j++;
  		}
		}
		$text = ltrim($text);
		$cutText = substr($text, $j, $end-$j);
		if(strlen($text) < $end)
		{
    	$end = strlen($text);
		}
		$i = $end;
  	if($cutText[strlen($cutText)-1] != " ")
  	{
  		if(isset($text[$i]))
  		{
	  		while($text[$i] != " ")
	  		{
	    		$cutText .= $text[$i];
	    		$i++;
	  		}
  		}
  	}
    return $cutText;
  }

  public function getWrappedText($string, Zend_Pdf_Style $style,$max_width)
	{
    $wrappedText = '' ;
    $lines = explode("\n",$string) ;
    foreach($lines as $line) {
         $words = explode(' ',$line) ;
         $word_count = count($words) ;
         $i = 0 ;
         $wrappedLine = '' ;
         while($i < $word_count)
         {
             /* if adding a new word isn't wider than $max_width,
             we add the word */
             if($this->widthForStringUsingFontSize($wrappedLine.' '.$words[$i]
                 ,$style->getFont()
                 , $style->getFontSize()) < $max_width) {
                 if(!empty($wrappedLine)) {
                     $wrappedLine .= ' ' ;
                 }
                 $wrappedLine .= $words[$i] ;
             } else {
                 $wrappedText .= $wrappedLine."\n" ;
                 $wrappedLine = $words[$i] ;
             }
             $i++ ;
         }
         $wrappedText .= $wrappedLine."\n" ;
     }
     return $wrappedText ;
	}

	/**
	 * found here, not sure of the author :
	 * http://devzone.zend.com/article/2525-Zend_Pdf-tutorial#comments-2535
	 */
	 public function widthForStringUsingFontSize($string, $font, $fontSize)
	 {
	     $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
	     $characters = array();
	     for ($i = 0; $i < strlen($drawingString); $i++) {
	         $characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);
	     }
	     $glyphs = $font->glyphNumbersForCharacters($characters);
	     $widths = $font->widthsForGlyphs($glyphs);
	     $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
	     return $stringWidth;
	 }

	 public function dateBefore($date){
	 	$aux = explode("/", $date);
	 	return date("d/m/Y", mktime(0,0,0, $aux[1], $aux[0]-1, $aux[2]));
	 }

	 public function dotTocomma()
	 {
	 	str_replace('.', ',', subject);
	 }
}

