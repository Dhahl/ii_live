<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Irish Interest - Nielsen ONIX Feed Management </title>
	<link rel="icon" type="image/png" href="../favicon.png" />

    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet">
 	
 	<!-- Style.css -->
    <link href="../css/style.css" rel="stylesheet">
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<style>
body {background-color:lightgrey;}
</style>
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script>
	var file1;
	$( document ).ready(function() {
		console.log("Document Ready");
	    setCommitBtn();
	});
		
	function sync() {
		var request = $.ajax({
			//	url: "../Nielsen_feed_db_update.php",
				url: "../db_sync.php",
				type: "POST",			
				dataType: "html"
			});

			request.done(function(msg) {
				loc ="../db_sync.php";
				//$("#mybox").html(msg);
			    document.getElementById('mybox').src = loc;
				showFileName(' ');	
				resetButtons();		
				setCommitBtn();
				stageBtn.style.color = '#000';
				commitBtn.style.color = '#000';		
			});

			request.fail(function(jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
			});
	}
	function resetButtons() {	// feed files
		var request = $.ajax({
				url: "../nielsen_getFeedStatus.php",
				type: "POST",			
				dataType: "html"
			});

			request.done(function(msg) {
				console.log('msg =' + msg);
				loc ="../nielsen_getFeedStatus.php";
			    document.getElementById('feedButtons').innerHTML = msg;
				stageBtn.disabled= true;	
			});

			request.fail(function(jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
			});
	}
	function setCommitBtn() {
		var request = $.ajax({
			url: "../nielsen_getCommitStatus.php",
			type: "POST",			
			dataType: "html"
		});

		request.done(function(msg) {
			console.log('Commit msg =' + msg);
			loc ="../nielsen_getCommitStatus.php";
		   // document.getElementById('feedButtons').innerHTML = msg; // check if any file has been staged
		   if(msg == 1) { // at least one committed file 
				commitBtn.disabled = false;
				commitBtn.style.color ='red';
		   }
		   else
				commitBtn.disabled = true; // none committed
		});

		request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
		});
	}
	function myCall(file) {	// select a feed file
		file1 = file;

	    document.getElementById('mybox').src = '';
		var request = $.ajax({
		//	url: "../Nielsen_feed_db_update.php",
			url: "../nielsen_view_feed_1.php",
			data: {file: file},
			data: {stage: '0'},
			type: "POST",			
			dataType: "html"
		});

		request.done(function(msg) {
			//loc ="../Nielsen_feed_db_update.php?file=" + file;
			loc ="../nielsen_view_feed_1.php?file=" + file+ '&stage=0';
			//$("#mybox").html(msg);
		    document.getElementById('mybox').src = loc;
			showFileName(file);	
			setButtons(file)		
	    	stageBtn.disabled = false;
	    	stageBtn.style.color = 'red';
		});

		request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
		});
	}
	function showFileName(file) {
		if(file == ' ') {
		    document.getElementById('filename').innerHTML = '';
		}
		else {
		    document.getElementById('filename').innerHTML = 'Reviewing Feed file: <span style="color:red;">' + file+'</span>';
		}		
	}
	function setButtons(file) {
		btnIds.forEach(function(btnId) {
			
			if(btnId == file) {
				document.getElementById(btnId).style.color="#999";
    		}
    		else { 
				document.getElementById(btnId).style.color="#000";
			}
    	});
    }
</script>				
<script>
	
	function stage() {
		
		var request = $.ajax({
		//	url: "../Nielsen_feed_db_update.php",
			url: "../nielsen_view_feed_1.php",
			data: {file: file1},
			data: {stage: '1'},
			type: "POST",			
			dataType: "html"
		});

		request.done(function(msg) {
			console.log('Staged file: '+ file1);
			//loc ="../Nielsen_feed_db_update.php?file=" + file;
			loc ="../nielsen_view_feed_1.php?file=" + file1 + '&stage=1';
			//$("#mybox").html(msg);
		    document.getElementById('mybox').src = loc;
			document.getElementById(file1).innerHTML += ' <span class="glyphicon glyphicon-star" aria-hidden="true"></span> ';
			commitBtn.disabled = false;
			commitBtn.style.color = 'red';		
			console.log(file1);			
		});

		request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
		});
	}
</script>				

 </head>
 <body role="document">
 
 <?php 
$userMenu =	'
	<button class="btn btn-custom dropdown-toggle" type="button" data-toggle="dropdown" 
    				style="border-radius: 10px; font-weight:900;">'
  					.$_SESSION['user'].'
    				<span class="caret"></span>
	</button>';
 if($_SESSION['accessLevel']		=='10')	{
  	$userMenu .=	'
  	  	<ul class="dropdown-menu" style="text-align:left">
		  	<li><a href="../admin/">Publishers</a></li>
		  	<li><a href="../categories/">Categories</a></li>
		  	<li><a href="../banner/">Banners</a></li>
		 	<li><a href="../utilities/">Utilities</a></li>
		  	<li class="divider"></li>
   			<li><a href="../publish/">Add Books / Edit Books</a></li>
  			<li><a href="../authors/">Author Profiles</a></li>
  			<li><a href="../profile"><i>'.$_SESSION['user'].'</i> Profile</a></li>
  			<li class="divider"></li>
 			<li><a href="../?home=1">Home Page</a></li>
 			<li><a href="../logout">Sign Out</a></li>
		  	  			</ul>';
  
  }
 if( ($_SESSION['accessLevel']		=='1')
   || ($_SESSION['accessLevel']		== '2'	) )	{
  	$userMenu .=	'
  	  	<ul class="dropdown-menu" style="text-align:left">
  			<li><a href="../publish/">Add Books / Edit Books</a></li>
  			<li><a href="../authors/">Author Profiles</a></li>
  			<li><a href="../profile"><i>'.$_SESSION['user'].'</i> Profile</a></li>
  			<li><a href="../logout">Sign Out</a></li>
  			</ul>';
  }
 if($_SESSION['accessLevel']		=='3')	{
  	$userMenu .=	'
			<ul class="dropdown-menu" style="text-align:left">
  				<li><a href="../logout">Sign Out</a></li>
  			</ul>';
  }
  ?>
<div class="row" style="background-color:white" >
	<a href='../'> 
		<div class="col-md-2 col-sm-1 voffset4" style="background-color:#ffffff; z-index:1000; padding-left:50px; padding-top:10px;" >
			<img alt="" src="../ii_circle.png" class="image-responsive " style="height:100px;" >
		</div>
		<div class="col-md-4  col-sm-4 voffset2 " style="text-align:center">
			<div class="voffset6" style="font-size:24px;"><b>Nielsen Bookdata Feed Manager</b></div>
		
			<!--<img alt="" src="../IRISH_INTEREST_text.png" class="image-responsive ii_text" > -->
		</div>
	</a>
	<div class="col-md-4 voffset6">
		<div class="col-md-2" style="font-size: 16px;">
			
		</div>
		<div class="col-md-4" style="font-size: 16px;">
			
		</div>
		<div class="col-md-5">
			<div class="col-md-8 pull-right" style="border: 0px solid; border-radius: 10px; line-height: 2; font-weight: 900; font-size: 14px; font: Impact, Charcoal, sans-serif;">
				<?php echo $userMenu; ?>
			</div>
			<div class="col-md-4"></div>
		</div>
	</div>
</div>
<div class="row" style="padding-left:30px">
	<div class ="col-md-2" style="text-align:center">
	<hr>
		<input class="btn" type="button" onclick="sync();" value="Sync with Live">
	<hr>
 	<h5><u>Available Feeds</u></h5>
					<div id="feedButtons" style="height:200px; overflow:auto"  >
<div class="btn-group-vertical btn-group-sm" role="group">
<?php
 function parseFileName($fileName) {
	$fileParsed = explode('_',$fileName);
	$date=date_parse($fileParsed[3]);
	return $fileParsed[2].'-'.$date['year'].'-'.$date['month'].'-'.$date['day'].'-'.$fileParsed[4];
}

$files = $feed;
$fileNames = array();
if ($files) { 
	foreach ($files as $file=>$bookCount) {
		$fileNames[] = $file;		// for JS 
		$feedDate = parseFileName($file);
		$style='';
		if($bookCount) $style = ' <span class="glyphicon glyphicon-star" aria-hidden="true"></span> ';
		echo '<button id="'.$file.'"'.' type="button" class="btn btn-default btn-xs" onclick="myCall(\''.$file.'\');" >'. $feedDate .$style.'</button>'; 
	}
} else { 
	exit('No files found.'); 
} 
?>
<script type="text/javascript">
    var btnIds = <?php echo json_encode($fileNames); ?>;
</script>
			</div>
			
			
		</div>
	<input class="btn" id="commitBtn" type="button" onclick="myCall(\''.$file.'\');" value="Commit to Live System">
	</div>
	<!-- div class="col-md-7" id="mybox" style="overflow:scroll; height:400px;">
	 -->
	 <div style="">
	 <div class="col-md-9" style=" height:400px;"><!--overflow-x:auto;-->
	 <div id="filename" style="padding-top:10px;">No File Selected</div>
	  <iframe id="mybox" src="about:blank" width="100%" height="100%" frameborder="1" scrolling="yes"></iframe>
	</div>
</div>
</div>
<div class="row" style="text-align:center; padding-top:30px;">
<?php
echo '
	<input class="btn" type="button" id="stageBtn" disabled onclick="stage();" value="Stage">'
	?>
</div>
<?php 
function displayMessage()	{
	if(isset($_SESSION['msg'])) {
		$rtn = '<div class="row" style="background-color:#f3be22; margin-left:10%; margin-right:10%;border-radius:10px;">
					<a href=./?reset=1>
						<span class="glyphicon glyphicon-remove-circle orange pull-right"	title="reset" style="font-size: 20px; text-shadow: black 0px 1px 1px; padding:10px;"></span>
					</a>';
		$rtn .=  '<h5 class="text-center ">';
		
		foreach($_SESSION['msg'] as $msg) {
			$rtn .= $msg.'<br>';			
		}
		$rtn .=	'</strong></h5></div>';
		unset($_SESSION['msg']);
		return $rtn;
	}
}
?>		
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.bootstrap-touchspin.min.js"></script>
<script type="text/javascript">
function triggerModal(avalue) {
 
    document.getElementById('the_id').value = avalue;
 
    $('#largeModal').modal();
 
}
</script>        
		
		