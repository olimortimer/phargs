<?php
namespace Phargs\Formatter;

class Table {
  protected $fields = array(); 
  protected $fieldWidths = array();
  protected $fieldCount = 0;
  protected $rows = array();

  protected $vertChar = '|';
  protected $horizChar = '-';
  protected $cornerChar = '+';

  public function __construct(Array $fields = array()){
    $this->setFields($fields); 
  }
  
  public function setFields(Array $fields){
    $this->fields = $fields;
    $this->fieldCount = sizeOf($this->fields);
    $this->setFieldWidths($this->fields);
  }

  protected function setFieldWidths(Array $row){
    for ($i = 0; $i < $this->fieldCount; $i++){
      if (!isset($this->fieldWidths[$i])){
        $this->fieldWidths[$i] = 0;
      }

      if ($this->strlen($row[$i]) > $this->fieldWidths[$i]){
        $this->fieldWidths[$i] = $this->strlen($row[$i]);
      }
    }
  }

  protected function getTableWidth(){
    $paddingEtc = (3 * $this->fieldCount) + 1;
    $fields = array_sum($this->fieldWidths);

    return $fields + $paddingEtc;
  }

  public function getFieldCount(){
    return $this->fieldCount;
  }

  public function getRowCount(){
    return sizeOf($this->rows);
  }

  public function addRow(Array $row){
    if (sizeOf($row) != $this->fieldCount){
      return false;
    }
    $this->rows[] = $row;
    $this->setFieldWidths($row);
  }

  public function addRows(Array $rows){
    foreach ($rows as $row){
      if (!is_array($row)) continue;
      $this->addRow($row);
    }
  }

  protected function getRowString(Array $row){
    for ($i = 0; $i < $this->fieldCount; $i++){
      $finalWidth = $this->fieldWidths[$i];
      $paddingNeeded = $finalWidth - $this->strlen($row[$i]);
      $row[$i] = $row[$i].str_repeat(' ', $paddingNeeded);
    }
    
    $out  = "{$this->vertChar} ";
    $out .= implode(" {$this->vertChar} ", $row);
    $out .= " {$this->vertChar}";

    return $out;
  }

  protected function getRowSeparatorString() {
    $out = $this->cornerChar;

    for ($i = 0; $i < $this->fieldCount; $i++){
      $out .= str_repeat($this->horizChar, $this->fieldWidths[$i] + 2).$this->cornerChar;
    }

    return $out.PHP_EOL;
  }

  public function getTableString(){
    // Top
    $out = $this->getRowSeparatorString();
    
    // Headings
    $out .= $this->getRowString($this->fields).PHP_EOL;

    // Divider
    $out .= $this->getRowSeparatorString();

    // Rows 
    foreach ($this->rows as $row){
      $out .= $this->getRowString($row).PHP_EOL;
    }

    // Bottom
    $out .= $this->getRowSeparatorString();
    
    return $out;
  }

  public function __toString(){
    return $this->getTableString();
  }

  protected function strlen($str){
    if (function_exists('mb_strlen')){
      return mb_strlen($str, 'UTF-8');
    }
    return strlen($str);
  }
}
