<?php


defined('DACCESS') or die ('access denied');
 
class Book extends Controller {
 
    function __construct() {
        parent::__construct();
        $this->load->model('bookmodel');
    }
    public function index() {
		if(isset($_GET['Book_fav'])) { /* AJAX call from Book Page */
			$_SESSION['ok']	=	$this->bookmodel->addFavourite($_GET['Book_fav']);
 			$data['book'] = $this->bookmodel->getBook_1($_GET['Book_fav']);			 
			return;
		}
   		if(isset($_GET['Book_unfav'])) { /* AJAX call from Book Page */
			$_SESSION['ok']	=	$this->bookmodel->removeFavourite($_GET['Book_unfav']);
			echo "Removed -".$_GET['Book_unfav'];
 			$data['book'] = $this->bookmodel->getBook_1($_GET['Book_unfav']);			 
			return ;
		}
		if(isset($_GET['fav']))	{
    		$_SESSION['ok']	=	$this->bookmodel->addFavourite($_GET['fav']);
    	}

    	if(isset($_GET['unfav']))	{
    		$_SESSION['ok']	=	$this->bookmodel->removeFavourite();
    	}
    	if(!$_GET['uri']=='book/') {
	    	if( (isset($_GET['author'])   )	|| (   ($_SESSION['mode'] == 'author') && (   !isset($_GET['search'])  )  ) ){
    			$data['book']	=	$this->bookmodel->getAuthor();
    		}
	    	elseif( (isset($_GET['category'])   )	|| (   ($_SESSION['mode'] == 'category') && (   !isset($_GET['search'])  )  ) ){
	    		$data['book']	=	$this->bookmodel->getCategoryBooks();
	    	}
    	}
    	else {
 			$data['book'] = $this->bookmodel->getBook();			 
 			
//      	  var_dump($data);
		}
        $this->load->view('book',$data);
		
    }
    public function logout()	{
    	
    	$this->bookmodel->logout();    	
    }
}
