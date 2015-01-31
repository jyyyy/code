<?php
//

require_once('BindParam.php');
 class  Db{
     public  static  $db;
private function __construct(){


  self::$db=new MySQLi("localhost","root","","frellencer_arabyan_xx");
  if(self::$db->connect_errno){
    echo "there is an error on database";
  }

}



//recuprerer une seule instance dyal db
public static  function   getInstance(){
    if(!isset(self::$db)){

      return new Db();
    }
}

public  static function desconcter_db(){

    mysqli_close(self::$db);

}
public static function getAllFrom($table){
  self::$db->set_charset("utf8"); 
$tb=Db::$db->real_escape_string(htmlentities($table));
$td=Db::$db->query("select * from ".$tb);
$table=array();
if($td===false){
 return null;
}
while($u=mysqli_fetch_row($td)){

   $table[]=$u;
}
return $table;
}




  private static function  insert_syntax_prepared($field=array(),$table){
    $fie="(";
    $fie.=  implode($field,",");
     $fie.=")";

     $val="(";
     $inc=0;
    foreach($field as $v){
      $inc++;
      if($inc===count($field)){
                    $val.="?)";
      }
      else{
                      $val.="?,";
      }

    }
    $req="insert into ".$table."  ".$fie." values".$val;
    return $req;
}

/* update query */
  public static function  update($request,$values=array(),$types=array()){
	  // update table set nom=? where id=?
	  
	  if(count(($values))>0 && count(($types))>0 ){
		  $stmt=self::$db->prepare($request);
		  if($stmt === false) {
  
	  return null ;
		}
	$bindParam = new BindParam(); 
	$int=0;
	
	foreach($values as &$v){
		$i=$types[$int];
		$bindParam->add($i,$v);
		$int++;
		
		}
		
		call_user_func_array(array($stmt, 'bind_param'),self::refValues($bindParam->get()) );
		 
		  return $stmt->execute();
		  
		  }else{
			$stmt=self::$db->prepare($request);
				if($stmt===false){
 					return null;
				}


		return $stmt->execute();
			  }
    
   
}

/* end update */




/*delete */

/* update query */
  public static function  delete($request,$values=array(),$types=array()){
	  // update table set nom=? where id=?
	  
	  if(count(($values))>0 && count(($types))>0 ){
		  $stmt=self::$db->prepare($request);
		  if($stmt === false) {
  
	  return null ;
		}
	$bindParam = new BindParam(); 
	$int=0;
	
	foreach($values as &$v){
		$i=$types[$int];
		$bindParam->add($i,$v);
		$int++;
		
		}
		
		call_user_func_array(array($stmt, 'bind_param'),self::refValues($bindParam->get()) );
		 
		  return $stmt->execute();
		  
		  }else{
			$stmt=self::$db->prepare($request);
				if($stmt===false){
 					return null;
				}


		return $stmt->execute();
			  }
    
   
}

/* end update */

/*end delete */

public static function insert($field,$table,$values,$type_field=array()){
 $query= $syntax_insert=self::insert_syntax_prepared($field,$table);
if((count($field)===count($values)) && (count($field)===count($type_field))  && (count($values)===count($type_field))  )
{
	$stmt=self::$db->prepare($query);
	if($stmt === false) {
  
  return null ;
	}
$bindParam = new BindParam(); 
$int=0;

foreach($values as &$v){
	$i=$type_field[$int];
	$bindParam->add($i,$v);
	$int++;
	
	}


	
		
		
call_user_func_array(array($stmt, 'bind_param'),self::refValues($bindParam->get()) );
return ($stmt->execute());
	
	
}else{
		
}
}



/* select avec just requete */
public static function select_with_query($query,$values=array(),$type_field=array()){
	$table=array();
	
	
	
	
	 
	$stmt=self::$db->prepare($query);
	if($stmt===false){
		return "errore on query";
		}
		if(count($values)>0){
	$bindParam = new BindParam();
	$int=0;

foreach($values as &$v){
	$i=$type_field[$int];
	$bindParam->add($i,$v);
	$int++;
	
	} 
	
	call_user_func_array(array($stmt, 'bind_param'),self::refValues($bindParam->get()) );
	
		}
	
		
		
			
if($stmt->execute()){
$table=array();
var_dump($results=$stmt->get_result());

while ($row = $results->fetch_assoc()) {
	
     $table[]=$row;  

   }
   return $table;

}	
			
	}




/*end it*/



	
	
	

	private static function refValues($arr){ 
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    { 
        $refs = array(); 
        foreach($arr as $key => $value) 
            $refs[$key] = &$arr[$key]; 
        return $refs; 
    } 
    return $arr; 
}

public static function select_from_syntax_prepared($select=array(),$from=array(),$where=array()){

    $fie=$select;
      $fro=$from;


     if(is_array($select)){
       $fie="";
      $fie.=  implode($select,",");
     }
     if(is_array($from)){
       $fro="";
       $fro.=  implode($from,",");
     }
     //where  exemple where id=?
	 if(count($where)>0){

     if(is_array($where)){
       $wher="";
          $inc=0;

     foreach($where as $v){
         $inc++;
     if($v==="and"){
       $wher=$wher." and ";
       continue;
     }else if($v==="or"){
            $wher=$wher." or ";
            continue;
     }
                    $wher=$wher." $v "."=?";
    }
    }
    else{

      $wher=$where.'=?';
    }
	return  "SELECT  ".$fie ." FROM ".$fro.' WHERE  '.$wher;
	}
	else{
		return  "SELECT  ".$fie ." FROM ".$fro ;
		}
    
}
public static function select($selct,$from,$where=array(),$values=array(),$type_field=array()){
	$table=array();
	
	if(count($where)>0){
	  $query= self::select_from_syntax_prepared($selct,$from,$where);	
	
	 
	$stmt=self::$db->prepare($query);
	if($stmt==false){
		return null;
		}
	$bindParam = new BindParam();
	$int=0;

foreach($values as &$v){
	$i=$type_field[$int];
	$bindParam->add($i,$v);
	$int++;
	
	} 
	
	call_user_func_array(array($stmt, 'bind_param'),self::refValues($bindParam->get()) );
	
		
		
			
$stmt->execute(); 


if($stmt===false){
 return null;
}
$results=$stmt->get_result();
$table=array();
while ($row = $results->fetch_assoc()) {
       $table[]=$row;    

   }

	
			
	
	}else{

		$query= self::select_from_syntax_prepared($selct,$from);
		$stmt=self::$db->prepare($query);
				if($stmt===false){
 					return null;
				}


		$stmt->execute();
	

$results=$stmt->get_result();
$table=array();
while ($row = $results->fetch_assoc()) {
        $table[]=$row; 

   }

		}
	return $table;
	
	}
	



}
?>
