<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('Orient');
	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('display');
        $this->set('title_for_layout','Project X');
    } 


/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
	private function print_orient($rels){
		echo '==============================';
		if($rels) 
			{ if(is_array($rels))
				foreach ($rels as $rel ) {
		        	# code...
		        	foreach ($rel->data as $key => $value) {
		        		echo '<br>';
		        	
			        	if (!in_array($key,array('in','out')))echo '<strong>'.$key.'</strong>->'.$value.'<br>';
			        	else foreach ($value as $k=> $val) {
			        		# code...
			        		echo $key.':'.$k.'->'.$val.'<br>';
			        	}
			        }
	    		}
	    		else foreach ($rels->data as $key => $value) {
		        		echo '<br>';
		        	
			        	if (!in_array($key,array('in','out')))echo '<strong>'.$key.'</strong>->'.$value.'<br>';
			        	else foreach ($value as $k=> $val) {
			        		# code...
			        		echo $key.':'.$k.'->'.$val.'<br>';
			        	}
			        }

        }
        echo '==============================';
	}

	public function display() {
        
        $currentTime = microtime(true);
        $RecordID1 = '#6:7';
        $RecordID2 = '#6:9';
        //===============QUERY
        //$rels = $this->Orient->find('all',array('conditions'=>array('clusterID'=>6,'recordPos'=>2 )));

        $rels = $this->Orient->find('all',array('conditions'=>array('type'=>'institution',
        															'name'=>'Centre A1'
        															)));
        $this->print_orient($rels);
        
        
		//===============QUERY
        
        //find all the children in a cluster
        
        //UPDATE NAME
        //$rels = $this->Orient->command_query('UPDATE V SET name = "Centre B1" where @rid = '.$RecordID1);

        //===============ADD A NEW VERTEX
        //$rels = $this->Orient->command_query('insert into V (name,type) VALUES ("Centre C3","institution")');
		//debug($rels);
		
		/*$this->Orient->create();
		$rel = $this->Orient->save(array('type'=>'institution','name'=>'Centre C3'));

		*/

		echo 'GET THE LATEST INSERTED VERTEX RID';// may use the method craete and save(consider)
		$rels = $this->Orient->command_query('select @rid from V  order by rid desc limit 1');
		//$this->print_orient($rels);
		//echo  $rels[0];
		echo substr($rels[0],4);
		



		echo '<br>FIND FIRST VERTEX';
        $rels = $this->Orient->find('first',array('conditions'=>array('type'=>'institution',
        															'name'=>'Centre C3'
        															)));

        //echo 'CREATE EDGE FROM PARENT TO CHILD';
        //debug($rels['id']);
/*
        $ParentID = '#6:3';
        $ChildID = '#6:10';
        $rels = $this->Orient->command_query('CREATE EDGE FROM '.$ParentID.' TO '.$ChildID);
       	$this->print_orient($rels);
*/


        
        

        echo 'GET DIRECT CHILDREN';
        $RecordID2 = '#6:7';
        $rels = $this->Orient->command_query('select from (traverse V.out,E.in from '.$RecordID2.' where $depth <= 2) where $depth >= 1 and @class = "OGraphVertex"');
 		$this->print_orient($rels);

 		echo 'GET DIRECT PARENT';
        $rels = $this->Orient->command_query('select from (traverse V.in,E.out from '.$RecordID2.' where $depth <= 2) where $depth >= 1 and @class = "OGraphVertex"');
 		$this->print_orient($rels);
		
		echo 'GET  CHILDREN WITH THE SAME TYPE';
		$type = 'institution';
        $rels = $this->Orient->command_query('select from (traverse V.out,E.in from '.$RecordID2.' ) where $depth >= 1 and @class = "OGraphVertex" and type = "'.$type.'"');
 		$this->print_orient($rels);

 		echo 'GET ANCESTORS';
        $rels = $this->Orient->command_query('select from (traverse V.in,E.out from '.$RecordID2.' where $depth <= 2) where $depth >= 1 and @class = "OGraphVertex"');
 		$this->print_orient($rels);
/*
 		echo 'DELETE';
        $RecordID= '#6:17';
        $rels = $this->Orient->command_query('DELETE FROM V where @rid = "'.$RecordID.'" ');


 		
*/
echo 'Query in '.(microtime(true)-$currentTime).' s';
		$path = func_get_args();
		$count = count($path);

		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
}
