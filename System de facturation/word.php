<?php 
//Convert to Word  Number 
class MoroccoCurrency{
	
  public function __construct($amount){
    $this->amount=$amount;
    $this->hasPaisa=false;
    $arr=explode(".",$this->amount);
    $this->dirham=$arr[0];
    if(isset($arr[1])&&((int)$arr[1])>0){
      if(strlen($arr[1])>2){
        $arr[1]=substr($arr[1],0,2);
      }
      $this->hasPaisa=true;
      $this->paisa=$arr[1];
    }
  }
  
  public function get_words(){
    $w="";
    $thousand=(int)($this->dirham/1000);
    $this->dirham=$this->dirham%1000;
    $w.=$this->single_word($thousand,"Mille  ");
    $hundred=(int)($this->dirham/100);
    $w.=$this->single_word($hundred,"Cent ");
    $ten=$this->dirham%100;
    $w.=$this->single_word($ten,"");
    $w.="DIRHAM";
    if($this->hasPaisa){
      if($this->paisa[0]=="0"){
        $this->paisa=(int)$this->paisa;
      }
      else if(strlen($this->paisa)==1){
        $this->paisa=$this->paisa*10;
      }
      $w.=" and ".$this->single_word($this->paisa," Paisa");
    }
    return $w;
  }

  private function single_word($n,$txt){
    $t="";
    if($n<=19){
      $t=$this->words_array($n);
    }else{
      $a=$n-($n%10);
      $b=$n%10;
      $t=$this->words_array($a)." ".$this->words_array($b);
    }
    if($n==0){
      $txt="";
    }
    return $t." ".$txt;
  }

  private function words_array($num){
    $n=[0=>"", 1=>"Un", 2=>"Deux", 3=>"Trois", 4=>"Quatre", 5=>"Cinq", 6=>"Six", 7=>"Sept", 8=>"Huit", 9=>"Neuf", 10=>"Dix", 11=>"Onze", 12=>"Douze", 13=>"Treize", 14=>"Quaterze", 15=>"Quenze", 16=>"Seize", 17=>"Dix-sept", 18=>"Dix-huit", 19=>"dix-neuf", 20=>"Vingt", 30=>"Trente", 40=>"Quarente", 50=>"Cinquene", 60=>"Soixante", 70=>"Soixante-dix", 80=>"Quatre-vingt", 90=>"Quatre-vingt Dix", 100=>"Cent",];
    return $n[$num];
  }
}
?>